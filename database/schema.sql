-- =============================================================================
-- ONLINE eBOOK STORE & READER (READLY) — DATABASE SCHEMA & SEED DATA
-- Hệ quản trị: MySQL 8.4+
-- Bộ ký tự: utf8mb4 / Collation: utf8mb4_unicode_ci
-- =============================================================================

CREATE DATABASE IF NOT EXISTS `ebook_store`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `ebook_store`;

-- Tắt kiểm tra khóa ngoại tạm thời khi tạo cấu trúc
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- 1. BẢNG USERS (Tài khoản người dùng)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id`                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`              VARCHAR(120)    NOT NULL,
  `email`             VARCHAR(190)    NULL,
  `phone`             VARCHAR(20)     NULL,
  `password_hash`     VARCHAR(255)    NOT NULL,
  `role`              ENUM('customer', 'publisher', 'admin') NOT NULL DEFAULT 'customer',
  `balance`           DECIMAL(12,2)   NOT NULL DEFAULT 0.00,
  `avatar_path`       VARCHAR(255)    NULL,
  `social_provider`   ENUM('google', 'facebook') NULL,
  `social_id`         VARCHAR(255)    NULL,
  `remember_token`    VARCHAR(100)    NULL,
  `email_verified_at` TIMESTAMP       NULL,
  `created_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  UNIQUE KEY `uk_users_email` (`email`),
  UNIQUE KEY `uk_users_phone` (`phone`),
  UNIQUE KEY `uk_users_social` (`social_provider`, `social_id`),
  INDEX `idx_users_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 2. BẢNG PUBLISHERS (Nhà xuất bản / Nhà phát hành)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `publishers`;
CREATE TABLE `publishers` (
  `id`            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`       BIGINT UNSIGNED NOT NULL,
  `company_name`  VARCHAR(200)    NOT NULL,
  `company_email` VARCHAR(190)    NULL,
  `company_phone` VARCHAR(20)     NULL,
  `address`       TEXT            NULL,
  `logo_path`     VARCHAR(255)    NULL,
  `description`   TEXT            NULL,
  `is_verified`   TINYINT(1)      NOT NULL DEFAULT 0,
  `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  UNIQUE KEY `uk_publishers_user` (`user_id`),
  CONSTRAINT `fk_publishers_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 3. BẢNG CATEGORIES (Thể loại / Danh mục sách)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id`         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `parent_id`  BIGINT UNSIGNED NULL,
  `name`       VARCHAR(100)    NOT NULL,
  `slug`       VARCHAR(120)    NOT NULL,
  `icon`       VARCHAR(50)     NULL,
  `sort_order` INT             NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

  UNIQUE KEY `uk_categories_slug` (`slug`),
  INDEX `idx_categories_parent` (`parent_id`),
  CONSTRAINT `fk_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 4. BẢNG BOOKS (Sách & Ấn phẩm điện tử)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `books`;
CREATE TABLE `books` (
  `id`                    BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `publisher_id`          BIGINT UNSIGNED NULL,
  `category_id`           BIGINT UNSIGNED NULL,
  `title`                 VARCHAR(255)    NOT NULL,
  `slug`                  VARCHAR(280)    NOT NULL,
  `author`                VARCHAR(160)    NOT NULL,
  `description`           TEXT            NULL,
  `isbn`                  VARCHAR(20)     NULL,
  `publish_year`          SMALLINT UNSIGNED NULL,
  `pages`                 INT UNSIGNED    NULL,
  `cover_path`            VARCHAR(255)    NULL,
  `cover_color`           VARCHAR(50)     NULL DEFAULT 'cover-alchemist',
  `file_path`             VARCHAR(255)    NULL,
  `file_format`           VARCHAR(50)     NULL DEFAULT 'EPUB',
  `list_price`            DECIMAL(12,2)   NOT NULL DEFAULT 0.00,
  `digital_price`         DECIMAL(12,2)   NOT NULL DEFAULT 0.00,
  `sale_price`            DECIMAL(12,2)   NULL,
  `allow_activation_code` TINYINT(1)      NOT NULL DEFAULT 1,
  `allow_rental`          TINYINT(1)      NOT NULL DEFAULT 0,
  `status`                ENUM('draft', 'pending', 'published', 'rejected') NOT NULL DEFAULT 'draft',
  `rejection_reason`      TEXT            NULL,
  `submitted_at`          TIMESTAMP       NULL,
  `published_at`          TIMESTAMP       NULL,
  `total_readers`         INT UNSIGNED    NOT NULL DEFAULT 0,
  `total_sold`            INT UNSIGNED    NOT NULL DEFAULT 0,
  `avg_rating`            DECIMAL(2,1)    NOT NULL DEFAULT 0.0,
  `total_reviews`         INT UNSIGNED    NOT NULL DEFAULT 0,
  `created_at`            TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`            TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  UNIQUE KEY `uk_books_slug` (`slug`),
  UNIQUE KEY `uk_books_isbn` (`isbn`),
  INDEX `idx_books_publisher` (`publisher_id`),
  INDEX `idx_books_category` (`category_id`),
  INDEX `idx_books_status` (`status`),
  INDEX `idx_books_rating` (`avg_rating`),
  INDEX `idx_books_sold` (`total_sold`),
  FULLTEXT KEY `ft_books_search` (`title`, `author`, `description`),

  CONSTRAINT `fk_books_publisher` FOREIGN KEY (`publisher_id`) REFERENCES `publishers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_books_category`  FOREIGN KEY (`category_id`)  REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 5. BẢNG BOOK_CHAPTERS (Mục lục sách)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `book_chapters`;
CREATE TABLE `book_chapters` (
  `id`             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `book_id`        BIGINT UNSIGNED NOT NULL,
  `chapter_number` INT UNSIGNED    NOT NULL,
  `title`          VARCHAR(255)    NOT NULL,
  `start_page`     INT UNSIGNED    NOT NULL DEFAULT 1,

  UNIQUE KEY `uk_chapters_book_num` (`book_id`, `chapter_number`),
  CONSTRAINT `fk_chapters_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 6. BẢNG ORDERS (Đơn hàng)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id`              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`         BIGINT UNSIGNED NOT NULL,
  `order_code`      VARCHAR(20)     NOT NULL,
  `total`           DECIMAL(12,2)   NOT NULL,
  `discount_amount` DECIMAL(12,2)   NOT NULL DEFAULT 0.00,
  `final_total`     DECIMAL(12,2)   NOT NULL,
  `payment_method`  VARCHAR(50)     NULL,
  `status`          ENUM('pending', 'paid', 'cancelled', 'failed') NOT NULL DEFAULT 'pending',
  `paid_at`         TIMESTAMP       NULL,
  `created_at`      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

  UNIQUE KEY `uk_orders_code` (`order_code`),
  INDEX `idx_orders_user` (`user_id`),
  INDEX `idx_orders_status` (`status`),
  INDEX `idx_orders_created` (`created_at`),
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 7. BẢNG PROMOTIONS (Chiến dịch khuyến mãi)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `promotions`;
CREATE TABLE `promotions` (
  `id`               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `publisher_id`     BIGINT UNSIGNED NOT NULL,
  `name`             VARCHAR(200)    NOT NULL,
  `type`             ENUM('flash_sale', 'voucher') NOT NULL,
  `discount_percent` TINYINT UNSIGNED NOT NULL,
  `max_uses`         INT UNSIGNED    NULL,
  `used_count`       INT UNSIGNED    NOT NULL DEFAULT 0,
  `start_date`       DATE            NOT NULL,
  `end_date`         DATE            NOT NULL,
  `is_active`        TINYINT(1)      NOT NULL DEFAULT 1,
  `created_at`       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

  INDEX `idx_promotions_publisher` (`publisher_id`),
  INDEX `idx_promotions_dates` (`start_date`, `end_date`),
  INDEX `idx_promotions_active` (`is_active`),
  CONSTRAINT `fk_promotions_publisher` FOREIGN KEY (`publisher_id`) REFERENCES `publishers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 8. BẢNG ORDER_ITEMS (Chi tiết đơn hàng)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id`             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id`       BIGINT UNSIGNED NOT NULL,
  `book_id`        BIGINT UNSIGNED NOT NULL,
  `unit_price`     DECIMAL(12,2)   NOT NULL,
  `discount_price` DECIMAL(12,2)   NULL,
  `promotion_id`   BIGINT UNSIGNED NULL,

  UNIQUE KEY `uk_order_book` (`order_id`, `book_id`),
  CONSTRAINT `fk_oi_order`     FOREIGN KEY (`order_id`)     REFERENCES `orders` (`id`)         ON DELETE CASCADE,
  CONSTRAINT `fk_oi_book`      FOREIGN KEY (`book_id`)      REFERENCES `books` (`id`)          ON DELETE RESTRICT,
  CONSTRAINT `fk_oi_promotion` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`id`)     ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 9. BẢNG PROMOTION_BOOKS (Liên kết Sách - Khuyến mãi)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `promotion_books`;
CREATE TABLE `promotion_books` (
  `promotion_id` BIGINT UNSIGNED NOT NULL,
  `book_id`      BIGINT UNSIGNED NOT NULL,

  PRIMARY KEY (`promotion_id`, `book_id`),
  CONSTRAINT `fk_pb_promotion` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pb_book`      FOREIGN KEY (`book_id`)      REFERENCES `books` (`id`)      ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 10. BẢNG REVIEWS (Đánh giá & Nhận xét)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id`         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`    BIGINT UNSIGNED NOT NULL,
  `book_id`    BIGINT UNSIGNED NOT NULL,
  `rating`     TINYINT UNSIGNED NOT NULL,
  `comment`    TEXT            NULL,
  `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

  UNIQUE KEY `uk_reviews_user_book` (`user_id`, `book_id`),
  INDEX `idx_reviews_book_rating` (`book_id`, `rating`),
  CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reviews_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chk_reviews_rating` CHECK (`rating` BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 11. BẢNG FAVORITES (Sách yêu thích)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `favorites`;
CREATE TABLE `favorites` (
  `id`         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`    BIGINT UNSIGNED NOT NULL,
  `book_id`    BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

  UNIQUE KEY `uk_favorites_user_book` (`user_id`, `book_id`),
  CONSTRAINT `fk_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_favorites_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 12. BẢNG ACTIVATION_CODES (Mã kích hoạt sách)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `activation_codes`;
CREATE TABLE `activation_codes` (
  `id`           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `book_id`      BIGINT UNSIGNED NOT NULL,
  `publisher_id` BIGINT UNSIGNED NOT NULL,
  `code`         VARCHAR(20)     NOT NULL,
  `status`       ENUM('unused', 'used', 'expired') NOT NULL DEFAULT 'unused',
  `used_by`      BIGINT UNSIGNED NULL,
  `used_at`      TIMESTAMP       NULL,
  `expires_at`   TIMESTAMP       NULL,
  `created_at`   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

  UNIQUE KEY `uk_activation_code` (`code`),
  INDEX `idx_ac_book` (`book_id`),
  INDEX `idx_ac_publisher` (`publisher_id`),
  INDEX `idx_ac_status` (`status`),
  INDEX `idx_ac_used_by` (`used_by`),
  CONSTRAINT `fk_ac_book`      FOREIGN KEY (`book_id`)      REFERENCES `books` (`id`)      ON DELETE RESTRICT,
  CONSTRAINT `fk_ac_publisher` FOREIGN KEY (`publisher_id`) REFERENCES `publishers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_ac_user`      FOREIGN KEY (`used_by`)       REFERENCES `users` (`id`)      ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 13. BẢNG USER_BOOKS (Thư viện cá nhân của độc giả)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `user_books`;
CREATE TABLE `user_books` (
  `id`                 BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`            BIGINT UNSIGNED NOT NULL,
  `book_id`            BIGINT UNSIGNED NOT NULL,
  `order_id`           BIGINT UNSIGNED NULL,
  `activation_code_id` BIGINT UNSIGNED NULL,
  `reading_status`     ENUM('unread', 'reading', 'completed') NOT NULL DEFAULT 'unread',
  `progress_percent`   TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `current_chapter`    INT UNSIGNED    NULL,
  `current_page`       INT UNSIGNED    NULL,
  `last_read_at`       TIMESTAMP       NULL,
  `completed_at`       TIMESTAMP       NULL,
  `acquired_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

  UNIQUE KEY `uk_ub_user_book` (`user_id`, `book_id`),
  INDEX `idx_ub_reading_status` (`user_id`, `reading_status`),
  INDEX `idx_ub_last_read` (`last_read_at`),
  CONSTRAINT `fk_ub_user`       FOREIGN KEY (`user_id`)            REFERENCES `users` (`id`)             ON DELETE CASCADE,
  CONSTRAINT `fk_ub_book`       FOREIGN KEY (`book_id`)            REFERENCES `books` (`id`)             ON DELETE RESTRICT,
  CONSTRAINT `fk_ub_order`      FOREIGN KEY (`order_id`)           REFERENCES `orders` (`id`)            ON DELETE SET NULL,
  CONSTRAINT `fk_ub_activation` FOREIGN KEY (`activation_code_id`) REFERENCES `activation_codes` (`id`)  ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 14. BẢNG HIGHLIGHTS (Ghi chú & Trích dẫn)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `highlights`;
CREATE TABLE `highlights` (
  `id`              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`         BIGINT UNSIGNED NOT NULL,
  `book_id`         BIGINT UNSIGNED NOT NULL,
  `quote`           TEXT            NOT NULL,
  `note`            TEXT            NULL,
  `page`            INT UNSIGNED    NOT NULL,
  `chapter_id`      BIGINT UNSIGNED NULL,
  `highlight_color` VARCHAR(7)      NOT NULL DEFAULT '#FFCA3A',
  `created_at`      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

  INDEX `idx_hl_user_book` (`user_id`, `book_id`),
  INDEX `idx_hl_created` (`created_at`),
  CONSTRAINT `fk_hl_user`    FOREIGN KEY (`user_id`)    REFERENCES `users` (`id`)          ON DELETE CASCADE,
  CONSTRAINT `fk_hl_book`    FOREIGN KEY (`book_id`)    REFERENCES `books` (`id`)          ON DELETE CASCADE,
  CONSTRAINT `fk_hl_chapter` FOREIGN KEY (`chapter_id`) REFERENCES `book_chapters` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 15. BẢNG READING_SESSIONS (Phiên đọc sách)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `reading_sessions`;
CREATE TABLE `reading_sessions` (
  `id`               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`          BIGINT UNSIGNED NOT NULL,
  `book_id`          BIGINT UNSIGNED NOT NULL,
  `started_at`       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ended_at`         TIMESTAMP       NULL,
  `duration_minutes` INT UNSIGNED    NOT NULL DEFAULT 0,
  `pages_read`       INT UNSIGNED    NOT NULL DEFAULT 0,

  INDEX `idx_rs_user_started` (`user_id`, `started_at`),
  INDEX `idx_rs_book` (`book_id`),
  CONSTRAINT `fk_rs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rs_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 16. BẢNG READING_GOALS (Mục tiêu rèn luyện đọc)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `reading_goals`;
CREATE TABLE `reading_goals` (
  `id`               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`          BIGINT UNSIGNED NOT NULL,
  `daily_minutes`    INT UNSIGNED    NOT NULL DEFAULT 20,
  `date`             DATE            NOT NULL,
  `achieved_minutes` INT UNSIGNED    NOT NULL DEFAULT 0,
  `is_completed`     TINYINT(1)      NOT NULL DEFAULT 0,

  UNIQUE KEY `uk_rg_user_date` (`user_id`, `date`),
  CONSTRAINT `fk_rg_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 17. BẢNG NEWSLETTER_SUBSCRIBERS (Bản tin email)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `newsletter_subscribers`;
CREATE TABLE `newsletter_subscribers` (
  `id`              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email`           VARCHAR(190)    NOT NULL,
  `is_active`       TINYINT(1)      NOT NULL DEFAULT 1,
  `subscribed_at`   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `unsubscribed_at` TIMESTAMP       NULL,

  UNIQUE KEY `uk_newsletter_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bật lại kiểm tra khóa ngoại
SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================================
-- DỮ LIỆU KHỞI TẠO MẪU (SEED DATA ĐỒNG BỘ 100% GIAO DIỆN)
-- =============================================================================

-- 1. Danh mục (Categories)
INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `sort_order`) VALUES
  (1, 'Văn học',       'van-hoc',       'book-open', 1),
  (2, 'Kinh tế',       'kinh-te',       'trending-up', 2),
  (3, 'Tâm lý',        'tam-ly',        'heart', 3),
  (4, 'Kỹ năng sống',  'ky-nang-song',  'compass', 4),
  (5, 'Thiếu nhi',     'thieu-nhi',     'smile', 5),
  (6, 'Ngoại ngữ',     'ngoai-ngu',     'globe', 6),
  (7, 'Khoa học',      'khoa-hoc',      'cpu', 7),
  (8, 'Kinh doanh',    'kinh-doanh',    'briefcase', 8),
  (9, 'Giáo dục',      'giao-duc',      'award', 9);

-- 2. Người dùng (Users: Admin, Publishers, Customers)
-- Password mặc định cho tất cả tài khoản mẫu là: password (bcrypt hashed)
INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password_hash`, `role`) VALUES
  (1, 'Admin Readly', 'admin@readly.vn', '0900000001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
  (2, 'NXB Kim Đồng', 'nxb@kimdong.vn', '0900000002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'publisher'),
  (3, 'NXB Trẻ', 'nxb@tre.vn', '0900000003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'publisher'),
  (4, 'NXB Giáo Dục', 'nxb@giaoduc.vn', '0900000004', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'publisher'),
  (5, 'NXB Văn Học', 'nxb@vanhoc.vn', '0900000005', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'publisher'),
  (6, 'NXB Hội Nhà Văn', 'nxb@hoinhavan.vn', '0900000006', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'publisher'),
  (7, 'NXB Thế Giới', 'nxb@thegioi.vn', '0900000007', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'publisher'),
  (8, 'NXB Lao Động', 'nxb@laodong.vn', '0900000008', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'publisher'),
  (9, 'Nguyễn Văn An', 'nguyen.an@example.com', '0912345671', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
  (10, 'Trần Thị Minh', 'minh.t@example.com', '0912345672', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
  (11, 'Lê Văn Cường', 'le.van.c@example.com', '0912345673', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
  (12, 'Phạm Thị Dung', 'pham.thi.d@example.com', '0912345674', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
  (13, 'Hoàng Văn Em', 'hoang.van.e@example.com', '0912345675', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer');

-- 3. Hồ sơ Nhà xuất bản (Publishers)
INSERT INTO `publishers` (`id`, `user_id`, `company_name`, `company_email`, `company_phone`, `is_verified`) VALUES
  (1, 2, 'NXB Kim Đồng', 'nxb@kimdong.vn', '02439434730', 1),
  (2, 3, 'NXB Trẻ', 'nxb@tre.vn', '02839316289', 1),
  (3, 4, 'NXB Giáo Dục', 'nxb@giaoduc.vn', '02438220801', 1),
  (4, 5, 'NXB Văn Học', 'nxb@vanhoc.vn', '02438253164', 1),
  (5, 6, 'NXB Hội Nhà Văn', 'nxb@hoinhavan.vn', '02438222135', 1),
  (6, 7, 'NXB Thế Giới', 'nxb@thegioi.vn', '02438253841', 1),
  (7, 8, 'NXB Lao Động', 'nxb@laodong.vn', '02438515380', 1);

-- 4. Sách (Books)
INSERT INTO `books` (`id`, `publisher_id`, `category_id`, `title`, `slug`, `author`, `description`, `isbn`, `publish_year`, `pages`, `cover_color`, `list_price`, `digital_price`, `sale_price`, `status`, `avg_rating`, `total_reviews`, `total_sold`, `total_readers`) VALUES
  (1, 5, 1, 'Nhà giả kim', 'nha-gia-kim', 'Paulo Coelho', 'Nhà giả kim (The Alchemist) kể về chuyến phiêu lưu kỳ diệu của Santiago — một cậu bé chăn cừu người Tây Ban Nha — trong hành trình theo đuổi giấc mơ tìm kho báu ở Kim tự tháp Ai Cập.', '9786045627235', 2020, 228, 'cover-alchemist', 129000, 89000, NULL, 'published', 4.8, 1234, 12500, 12500),
  (2, 5, 1, 'Cây cam ngọt của tôi', 'cay-cam-ngot-cua-toi', 'José Mauro de Vasconcelos', 'Câu chuyện về cậu bé Zezé 5 tuổi sống trong một gia đình nghèo tại Brazil. Với trí tưởng tượng phong phú, Zezé biến cây cam ngọt trong vườn thành người bạn tri kỷ.', '9786045678912', 2021, 244, 'cover-orange-tree', 139000, 95000, NULL, 'published', 4.9, 2150, 18200, 18200),
  (3, 6, 3, 'Tư duy nhanh và chậm', 'tu-duy-nhanh-va-cham', 'Daniel Kahneman', 'Daniel Kahneman — nhà tâm lý học đoạt giải Nobel Kinh tế — giải thích hai hệ thống tư duy chi phối mọi quyết định của con người: Hệ thống 1 (nhanh, trực giác) và Hệ thống 2 (chậm, logic).', '9786047721834', 2019, 568, 'cover-thinking', 199000, 159000, NULL, 'published', 4.7, 980, 8900, 8900),
  (4, 7, 4, 'Ikigai', 'ikigai', 'Héctor García', 'Ikigai — bí mật sống trường thọ và hạnh phúc của người Nhật. Cuốn sách khám phá triết lý Ikigai thông qua lối sống của cư dân vùng Okinawa.', '9786045982145', 2022, 196, 'cover-ikigai', 99000, 79000, NULL, 'published', 4.6, 1420, 15300, 15300),
  (5, 1, 4, 'Đắc Nhân Tâm', 'dac-nhan-tam', 'Dale Carnegie', 'Cuốn sách nghệ thuật thu phục lòng người kinh điển nhất mọi thời đại.', '9786042189456', 2020, 320, 'cover-alchemist', 129000, 89000, 79000, 'published', 4.8, 3200, 45800, 45800),
  (6, 1, 8, 'Nghệ Thuật Bán Hàng', 'nghe-thuat-ban-hang', 'Brian Tracy', 'Bí quyết và kỹ năng bán hàng đỉnh cao của chuyên gia hàng đầu thế giới.', '9786042189457', 2021, 280, 'cover-thinking', 159000, 119000, NULL, 'published', 4.5, 187, 6700, 6700),
  (7, 1, 3, 'Tâm Lý Học Hành Vi', 'tam-ly-hoc-hanh-vi', 'Daniel Kahneman', 'Nghiên cứu sâu sắc về hành vi và những định kiến vô thức định hình đời sống.', '9786042189458', 2021, 350, 'cover-orange-tree', 179000, 149000, NULL, 'published', 4.9, 312, 9200, 9200),
  (8, 1, 8, 'Kỹ Năng Lãnh Đạo', 'ky-nang-lanh-dao', 'John C. Maxwell', 'Phát triển năng lực lãnh đạo và dẫn dắt đội ngũ thành công.', '9786042189459', 2022, 310, 'cover-ikigai', 199000, 169000, NULL, 'published', 4.6, 156, 8300, 8300),
  (9, 1, 4, 'Tư Duy Phản Biện', 'tu-duy-phan-bien', 'Tom Chatfield', 'Rèn luyện kỹ năng phân tích và phản biện độc lập trong kỷ nguyên số.', '9786042189460', 2022, 260, 'cover-thinking', 139000, 99000, NULL, 'published', 4.7, 203, 7100, 7100),
  (10, 2, 4, 'Bí mật tối thượng', 'bi-mat-toi-thuong', 'Rhonda Byrne', 'Khám phá sức mạnh tối thượng bên trong mỗi con người.', '9786041189461', 2021, 290, 'cover-alchemist', 149000, 149000, 119000, 'published', 4.8, 1890, 25000, 25000),
  (11, 2, 4, 'Bước chậm lại giữa thế gian vội vã', 'buoc-cham-lai', 'Haemin Sunim', 'Lời khuyên bình an cho tâm hồn giữa cuộc sống hiện đại tất bật.', '9786041189462', 2020, 256, 'cover-orange-tree', 99000, 99000, 79000, 'published', 4.9, 2100, 22000, 22000),
  (12, 2, 4, 'Tuổi trẻ đáng giá bao nhiêu', 'tuoi-tre-dang-gia-bao-nhieu', 'Rosie Nguyễn', 'Cuốn sách truyền cảm hứng sống đẹp cho hàng triệu bạn trẻ Việt Nam.', '9786041189463', 2019, 288, 'cover-ikigai', 89000, 89000, 69000, 'published', 4.7, 1670, 19000, 19000),
  (13, 3, 2, 'Tâm lý học về tiền', 'tam-ly-hoc-ve-tien', 'Morgan Housel', 'Bài học vượt thời gian về sự giàu có, lòng tham và hạnh phúc.', '9786040189464', 2021, 380, 'cover-thinking', 159000, 159000, 139000, 'published', 4.8, 1450, 17000, 17000),
  (14, 4, 3, 'Đi tìm lẽ sống', 'di-tim-le-song', 'Viktor Frankl', 'Trải nghiệm trong trại tập trung và liệu pháp ý nghĩa cuộc đời.', '9786043189465', 2019, 220, 'cover-orange-tree', 119000, 119000, 99000, 'published', 4.9, 1320, 16000, 16000),
  (15, 1, 1, 'Nghệ Thuật Lập Trình', 'nghe-thuat-lap-trinh', 'Donald Knuth', 'Tác phẩm đồ sộ về khoa học máy tính và thuật toán.', '9786042189470', 2026, 650, 'cover-thinking', 350000, 280000, NULL, 'pending', 0.0, 0, 0, 0),
  (16, 2, 8, 'Chiến Lược Kinh Doanh', 'chien-luoc-kinh-doanh', 'Michael Porter', 'Phương pháp định vị và cạnh tranh trong thị trường hiện đại.', '9786041189471', 2026, 420, 'cover-ikigai', 260000, 199000, NULL, 'pending', 0.0, 0, 0, 0),
  (17, 3, 3, 'Tâm Lý Học Đại Cương', 'tam-ly-hoc-dai-cuong', 'Nhiều tác giả', 'Giáo trình chuẩn về các hiện tượng tâm lý người.', '9786040189472', 2026, 380, 'cover-orange-tree', 180000, 120000, NULL, 'pending', 0.0, 0, 0, 0),
  (18, 4, 1, 'Lịch Sử Việt Nam', 'lich-su-viet-nam', 'Viện Sử Học', 'Bộ thông sử toàn diện về lịch sử dựng nước và giữ nước.', '9786043189473', 2026, 800, 'cover-alchemist', 450000, 320000, NULL, 'pending', 0.0, 0, 0, 0);

-- 5. Mục lục chương (Book Chapters) cho sách Nhà giả kim (id=1)
INSERT INTO `book_chapters` (`book_id`, `chapter_number`, `title`, `start_page`) VALUES
  (1, 1, 'Phần một', 1),
  (1, 2, 'Phần hai', 45),
  (1, 3, 'Phần ba', 89),
  (1, 4, 'Phần bốn', 134),
  (1, 5, 'Phần năm', 178),
  (1, 6, 'Phần sáu', 210);

-- 6. Khuyến mãi (Promotions)
INSERT INTO `promotions` (`id`, `publisher_id`, `name`, `type`, `discount_percent`, `max_uses`, `used_count`, `start_date`, `end_date`, `is_active`) VALUES
  (1, 1, 'Ưu đãi sách mùa hè', 'flash_sale', 30, 1000, 680, '2026-07-20', '2026-08-31', 1),
  (2, 1, 'Tri ân độc giả mới', 'voucher', 25, 500, 210, '2026-07-01', '2026-08-31', 1);

-- 7. Sách áp dụng khuyến mãi (Promotion Books)
INSERT INTO `promotion_books` (`promotion_id`, `book_id`) VALUES
  (1, 5), -- Đắc Nhân Tâm (Flash Sale)
  (1, 8), -- Kỹ Năng Lãnh Đạo (Flash Sale)
  (2, 6); -- Nghệ Thuật Bán Hàng (Voucher)

-- 8. Mã kích hoạt (Activation Codes)
INSERT INTO `activation_codes` (`id`, `book_id`, `publisher_id`, `code`, `status`, `used_by`, `used_at`) VALUES
  (1, 5, 1, 'RDL-Y7KP-9H2M', 'used', 9, '2026-07-24 10:15:00'),
  (2, 7, 1, 'RDL-Q8MT-4XKA', 'unused', NULL, NULL),
  (3, 6, 1, 'RDL-H5NZ-7CPR', 'used', 10, '2026-07-23 14:30:00');

-- 9. Đơn hàng (Orders) & Chi tiết đơn hàng (Order Items)
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total`, `discount_amount`, `final_total`, `payment_method`, `status`, `paid_at`, `created_at`) VALUES
  (1, 9,  'DH001234', 249000, 0, 249000, 'VNPAY', 'paid', '2026-07-24 08:30:00', '2026-07-24 08:25:00'),
  (2, 10, 'DH001235', 189000, 0, 189000, 'MoMo',  'paid', '2026-07-24 09:12:00', '2026-07-24 09:10:00'),
  (3, 11, 'DH001236', 329000, 0, 329000, 'Card',  'paid', '2026-07-25 11:45:00', '2026-07-25 11:40:00'),
  (4, 12, 'DH001237', 159000, 0, 159000, 'VNPAY', 'failed', NULL, '2026-07-25 15:20:00'),
  (5, 13, 'DH001238', 279000, 0, 279000, 'MoMo',  'paid', '2026-07-26 14:10:00', '2026-07-26 14:05:00');

INSERT INTO `order_items` (`order_id`, `book_id`, `unit_price`, `discount_price`) VALUES
  (1, 1, 89000, NULL),
  (1, 3, 160000, NULL),
  (2, 2, 95000, NULL),
  (2, 4, 94000, NULL),
  (3, 3, 159000, NULL),
  (3, 8, 170000, NULL);

-- 10. Tủ sách cá nhân của Độc giả (User Books)
INSERT INTO `user_books` (`user_id`, `book_id`, `reading_status`, `progress_percent`, `current_chapter`, `current_page`, `last_read_at`) VALUES
  (9, 1, 'reading',   68, 3, 89,  '2026-08-19 20:30:00'),
  (9, 2, 'reading',   42, 2, 45,  '2026-08-18 19:15:00'),
  (9, 3, 'reading',   85, 5, 480, '2026-08-19 21:45:00'),
  (9, 4, 'reading',   23, 1, 25,  '2026-08-17 10:20:00'),
  (9, 5, 'reading',   56, 3, 110, '2026-08-19 14:00:00'),
  (9, 12, 'reading',  31, 2, 60,  '2026-08-16 16:30:00'),
  (9, 10, 'completed', 100, 6, 290, '2026-08-15 22:00:00'),
  (9, 11, 'unread',    0, 1, 1,   NULL);

-- 11. Ghi chú & Trích dẫn (Highlights)
INSERT INTO `highlights` (`user_id`, `book_id`, `quote`, `page`, `highlight_color`) VALUES
  (9, 1, '"Khi bạn muốn một điều gì đó, cả vũ trụ sẽ hợp lực giúp bạn đạt được điều đó."', 24, '#FFCA3A'),
  (9, 4, '"Hạnh phúc không phải là điều bạn tìm thấy ở cuối con đường, mà là chính con đường đó."', 87, '#FF5A5F'),
  (9, 3, '"Chúng ta không thể giải quyết vấn đề bằng cùng một cách suy nghĩ khi ta tạo ra nó."', 156, '#087E8B');

-- 12. Mục tiêu đọc sách hôm nay (Reading Goals)
INSERT INTO `reading_goals` (`user_id`, `daily_minutes`, `date`, `achieved_minutes`, `is_completed`) VALUES
  (9, 20, CURRENT_DATE(), 13, 0);

-- 13. Phiên đọc sách (Reading Sessions)
INSERT INTO `reading_sessions` (`user_id`, `book_id`, `duration_minutes`, `pages_read`) VALUES
  (9, 1, 13, 12),
  (9, 3, 32, 28),
  (10, 2, 25, 20);

-- 14. Đánh giá & Bình luận (Reviews)
INSERT INTO `reviews` (`user_id`, `book_id`, `rating`, `comment`) VALUES
  (9, 1, 5, 'Cuốn sách tuyệt vời, thay đổi nhân sinh quan của tôi!'),
  (10, 1, 5, 'Một tác phẩm rất truyền cảm hứng.'),
  (11, 2, 5, 'Rất xúc động và sâu lắng.'),
  (9, 3, 4, 'Sách hay nhưng cần đọc chậm để hiểu hết.');

-- 15. Yêu thích (Favorites)
INSERT INTO `favorites` (`user_id`, `book_id`) VALUES
  (9, 1),
  (9, 2),
  (9, 5);

-- 16. Đăng ký nhận bản tin (Newsletter)
INSERT INTO `newsletter_subscribers` (`email`, `is_active`) VALUES
  ('reader@readly.vn', 1),
  ('nguyen.an@example.com', 1);
