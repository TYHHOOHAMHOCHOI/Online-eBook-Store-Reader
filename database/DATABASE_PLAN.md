# 📚 Kế Hoạch & Thiết Kế Cơ Sở Dữ Liệu Chi Tiết — Online eBook Store & Reader (Readly)

> **Hệ quản trị CSDL**: MySQL 8.4 (InnoDB)  
> **Bộ mã ký tự**: `utf8mb4` / Collation: `utf8mb4_unicode_ci`  
> **Database Name**: `ebook_store`  
> **Mục tiêu**: Đáp ứng toàn diện 100% tính năng của hệ thống gồm Customer (Độc giả), Publisher (Nhà xuất bản), và Admin (Quản trị viên).

---

## 📑 MỤC LỤC

1. [Tổng Quan & Phân Tích Yêu Cầu Từng Thành Phần Giao Diện](#1-tổng-quan--phân-tích-yêu-cầu-từng-thành-phần-giao-diện)
2. [So Sánh Schema Hiện Tại vs Schema Mới](#2-so-sánh-schema-hiện-tại-vs-schema-mới)
3. [Danh Sách & Chi Tiết Toàn Bộ 17 Bảng Dữ Liệu](#3-danh-sách--chi-tiết-toàn-bộ-17-bảng-dữ-liệu)
4. [Sơ Đồ Quan Hệ Thực Thể (ERD Diagram)](#4-sơ-đồ-quan-hệ-thực-thể-erd-diagram)
5. [Ma Trận Khóa Ngoại & Quan Hệ (Foreign Keys)](#5-ma-trận-khóa-ngoại--quan-hệ-foreign-keys)
6. [Chiến Lược Đánh Chỉ Mục (Indexes Optimization)](#6-chiến-lược-đánh-chỉ-mục-indexes-optimization)
7. [Mã Nguồn SQL Hoàn Chỉnh Khởi Tạo Database](#7-mã-nguồn-sql-hoàn-chỉnh-khởi-tạo-database)

---

## 1. TỔNG QUAN & PHÂN TÍCH YÊU CẦU TỪNG THÀNH PHẦN GIAO DIỆN

Sau khi rà soát toàn bộ các file giao diện (views, components, css, js) và logic backend hiện có, hệ thống được cấu thành từ 3 phân hệ chính với các yêu cầu dữ liệu cụ thể như sau:

### 1.1. Phân Hệ Người Dùng & Cửa Hàng (Storefront / Home)
- **`Login.php` (Đăng nhập / Đăng ký / Modal)**:
  - Hỗ trợ đăng ký 2 loại tài khoản: **Độc giả (READER / Customer)** và **Nhà phát hành (PUBLISHER)**.
  - Cho phép đăng nhập bằng **Email hoặc Số điện thoại**, Mật khẩu.
  - Hỗ trợ Social Login: **Google**, **Facebook**.
  - Tính năng "Ghi nhớ đăng nhập" (`remember_token`), Quên mật khẩu.
- **`HomePage.php` (Trang chủ)**:
  - **Sách được yêu thích**: Lọc theo lượt yêu thích/đánh giá cao nhất.
  - **Best Seller**: Sách bán chạy nhất theo từng tab thể loại (Văn học, Kinh tế, Tâm lý, Kỹ năng sống, Thiếu nhi, Ngoại ngữ, Khoa học...).
  - **Gợi ý cho bạn**: Gợi ý sách dựa trên lịch sử đọc và sách đã lưu/yêu thích của người dùng.
- **`BookCards.php` & `BookListPage.php` (Danh sách sách & Bộ lọc)**:
  - Hiển thị badge ("Bán chạy", "Yêu thích", "Top 10", "Mới"), rating sao, số lượt độc giả đã đọc, giá bán, giá khuyến mãi (`salePrice` vs `originalPrice`).
  - Bộ lọc đa tiêu chí: Lọc theo **Thể loại** (nhiều danh mục), **Khoảng giá** (<50k, 50k-100k, 100k-150k, >150k), **Đánh giá** (5★, 4★+, 3★+).
  - Phân trang (Pagination).
- **`BookDetailPage.php` (Trang chi tiết sách)**:
  - Thông tin xuất bản chi tiết: Nhà xuất bản, Năm xuất bản, Số trang, Định dạng (eBook EPUB/PDF).
  - Thông số tương tác: Điểm rating trung bình, Tổng số lượt đánh giá, Tổng số độc giả, Số lượng đã bán.
  - Hành động: Mua ngay (Order), Đọc thử (Sample Reader), Yêu thích (Favorite / Wishlist), Chia sẻ.
  - Danh sách sách liên quan (Related Books theo category hoặc tác giả).
- **`SearchResultsPage.php` (Tìm kiếm sách)**:
  - Tìm kiếm toàn văn (Fulltext search) theo tên sách, tác giả, mô tả; kết hợp với các bộ lọc đang active.
- **`Footer.php` / `LibraryFooter.php`**:
  - Đăng ký nhận bản tin khuyến mãi & sách mới qua Email (Newsletter).

---

### 1.2. Phân Hệ Thư Viện Cá Nhân & Trình Đọc Sách (Reader / Library)
- **`LibraryOverview.php` (Tổng quan thư viện)**:
  - **Mục tiêu đọc sách hôm nay (Reading Goal)**: Theo dõi số phút đọc trong ngày (VD: 13/20 phút - đạt 65%), vòng tròn tiến độ SVG.
- **`LibraryBooks.php` (Tủ sách cá nhân)**:
  - Quản lý sách người dùng đã sở hữu (mua qua giỏ hàng hoặc kích hoạt bằng mã).
  - Lọc theo 3 tab trạng thái đọc: **Đang đọc (reading)**, **Chưa đọc (unread)**, **Đã hoàn thành (completed)**.
  - Tiến độ đọc theo phần trăm (0% - 100%).
  - Sắp xếp theo: Tên sách, Ngày mua/kích hoạt, Tiến độ đọc.
- **`BookReader.php` (Trình đọc sách trực tuyến)**:
  - Lưu trạng thái đọc: Chương hiện tại (`currentChapter`), trang hiện tại, % tiến độ.
  - Quản lý **Mục lục sách (Book Chapters)**: Danh sách các phần/chương và trang tương ứng.
  - Tùy chỉnh đọc: Cỡ chữ (Font size), Theme giao diện (Light, Dark, Sepia).
- **`RecentActivities.php` (Hoạt động & Trích dẫn/Highlight)**:
  - Lưu trích dẫn / highlight khi đọc: Đoạn trích (`quote`), ghi chú (`note`), sách, số trang, màu sắc highlight (`highlightColor`).

---

### 1.3. Phân Hệ Quản Trị Hệ Thống (Admin Dashboard)
- **`AdminSidebar.php` & `AdminStats.php` (Thống kê & Quản lý)**:
  - Thống kê tổng hợp: Tổng người dùng, Tổng NXB, Tổng số sách, Tổng số đơn hàng, Tổng mã kích hoạt đã phát hành.
  - Menu chức năng: Dashboard, Quản lý người dùng, Duyệt sách (với badge số lượng sách chờ duyệt), Quản lý danh mục, Giao dịch & Đối soát.
- **`AdminBottomSection.php` (Duyệt sách & Lịch sử giao dịch)**:
  - **Duyệt sách (Book Moderation)**: Sách do NXB gửi lên ở trạng thái chờ duyệt (`pending`), Admin có thể Duyệt (`published`), Từ chối (`rejected`), hoặc Yêu cầu sửa.
  - **Giao dịch mới nhất (Recent Transactions)**: Mã đơn hàng (VD: DH001234), Người mua, Số tiền, Trạng thái (Thành công / Thất bại / Đang xử lý).
- **`RevenueChart.php`**:
  - Biểu đồ doanh thu toàn hệ thống theo Hôm nay / Tuần này / Tháng này / Năm nay.

---

### 1.4. Phân Hệ Dành Cho Nhà Xuất Bản (Publisher Dashboard)
- **`publisher_dashboard.php` & `ReportsPage.php`**:
  - Hồ sơ NXB: Tên đơn vị (VD: NXB Kim Đồng), email, avatar/logo, trạng thái xác minh.
  - Thống kê NXB: Doanh số bán sách, Số lượt tải/đọc, Thời gian đọc trung bình (phút/phiên), Tỷ lệ kích hoạt mã.
  - Báo cáo chi tiết: Biểu đồ doanh thu theo thời gian, Top sản phẩm bán chạy, Phân bố thể loại.
- **`ProductsPage.php` & `AddBookForm.php`**:
  - Quản lý sản phẩm sách của NXB: Giá niêm yết (`listPrice`), Giá bán điện tử (`digitalPrice`), Chiến dịch khuyến mãi đang áp dụng.
  - Form thêm sách: Tải file EPUB, Tải ảnh bìa, Tên sách, Tác giả, Thể loại, Mô tả, ISBN, Năm xuất bản, Tùy chọn cho phép tạo mã sách giấy, Tùy chọn cho phép thuê sách.
- **`ActivationCodesPage.php` (Quản lý mã kích hoạt sách)**:
  - Phát hành mã kích hoạt sách điện tử đi kèm sách giấy (VD: `RDL-Y7KP-9H2M`).
  - Quản lý trạng thái mã: **Chưa sử dụng (unused)**, **Đã sử dụng (used)**, **Hết hạn (expired)**.
  - Theo dõi người đã kích hoạt (`used_by`), ngày kích hoạt.
- **`PromotionsPage.php` (Chiến dịch khuyến mãi)**:
  - Tạo chiến dịch: **Flash Sale** hoặc **Voucher**.
  - Cấu hình: Mức giảm (%), Ngày bắt đầu, Ngày kết thúc, Giới hạn số lượng sử dụng, Danh sách sách áp dụng (N:N).

---

## 2. SO SÁNH SCHEMA HIỆN TẠI VS SCHEMA MỚI

| Tiêu chí | Schema Ban Đầu (`database/schema.sql`) | Schema Hoàn Thiện Đề Xuất |
| :--- | :--- | :--- |
| **Số lượng bảng** | 5 bảng (`users`, `categories`, `books`, `orders`, `order_items`) | **17 bảng hoàn chỉnh** |
| **Phân quyền người dùng** | Chỉ có `customer`, `admin` | Hỗ trợ 3 role: `customer`, `publisher`, `admin` + Bảng thông tin NXB `publishers` |
| **Thông tin sách** | Chỉ có tên, tác giả, giá, cover, file | Bổ sung: `isbn`, `publish_year`, `pages`, `file_format`, `list_price`, `digital_price`, `sale_price`, `status` (draft/pending/published/rejected), cache metrics (`avg_rating`, `total_sold`, `total_readers`) |
| **Mục lục & Đọc sách** | Không có | Có bảng `book_chapters` (chương, trang) |
| **Thư viện cá nhân** | Không có | Có bảng `user_books` (tiến độ đọc %, trang đang đọc, trạng thái đọc) |
| **Đánh giá & Yêu thích** | Không có | Có bảng `reviews` (1-5 sao, bình luận) và `favorites` |
| **Highlight & Ghi chú** | Không có | Có bảng `highlights` (trích dẫn, trang, màu highlight) |
| **Thói quen & Mục tiêu đọc** | Không có | Có bảng `reading_sessions` (thời gian đọc) và `reading_goals` (mục tiêu phút/ngày) |
| **Mã kích hoạt (Code)** | Không có | Có bảng `activation_codes` (quản lý mã kích hoạt từ NXB) |
| **Khuyến mãi (Marketing)** | Không có | Có bảng `promotions` và `promotion_books` (Flash sale, Voucher, áp dụng theo sách) |
| **Bản tin (Newsletter)** | Không có | Có bảng `newsletter_subscribers` |

---

## 3. DANH SÁCH & CHI TIẾT TOÀN BỘ 17 BẢNG DỮ LIỆU

### 3.1. Bảng `users` (Người dùng hệ thống)
Lưu trữ thông tin tài khoản của toàn bộ người dùng (Độc giả, Nhà xuất bản, Quản trị viên).

| Tên Cột | Kiểu Dữ Liệu | Ràng Buộc (Constraints) | Ý Nghĩa / Mô Tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | Định danh duy nhất người dùng |
| `name` | `VARCHAR(120)` | `NOT NULL` | Họ và tên hiển thị |
| `email` | `VARCHAR(190)` | `NOT NULL`, `UNIQUE` | Email đăng nhập |
| `phone` | `VARCHAR(20)` | `NULL`, `UNIQUE` | Số điện thoại (hỗ trợ login SĐT) |
| `password_hash` | `VARCHAR(255)` | `NOT NULL` | Mật khẩu mã hóa (bcrypt/argon2) |
| `role` | `ENUM('customer','publisher','admin')` | `NOT NULL`, `DEFAULT 'customer'` | Vai trò tài khoản |
| `avatar_path` | `VARCHAR(255)` | `NULL` | Đường dẫn ảnh đại diện |
| `social_provider` | `ENUM('google','facebook')` | `NULL` | Nền tảng đăng nhập MXH |
| `social_id` | `VARCHAR(255)` | `NULL` | ID định danh từ Google/Facebook |
| `remember_token` | `VARCHAR(100)` | `NULL` | Token duy trì đăng nhập |
| `email_verified_at`| `TIMESTAMP` | `NULL` | Thời điểm xác thực email |
| `created_at` | `TIMESTAMP` | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP` | Thời điểm tạo tài khoản |
| `updated_at` | `TIMESTAMP` | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP` | Thời điểm cập nhật |

---

### 3.2. Bảng `publishers` (Thông tin Nhà xuất bản / Nhà phát hành)
Lưu trữ hồ sơ doanh nghiệp/nhà xuất bản liên kết 1-1 với tài khoản có role `publisher`.

| Tên Cột | Kiểu Dữ Liệu | Ràng Buộc | Ý Nghĩa / Mô Tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | ID nhà xuất bản |
| `user_id` | `BIGINT UNSIGNED` | `NOT NULL`, `UNIQUE`, `FK -> users(id)` | Tài khoản quản trị NXB |
| `company_name` | `VARCHAR(200)` | `NOT NULL` | Tên NXB (VD: NXB Kim Đồng, NXB Trẻ) |
| `company_email` | `VARCHAR(190)` | `NULL` | Email liên hệ chính thức |
| `company_phone` | `VARCHAR(20)` | `NULL` | Số điện thoại liên hệ |
| `address` | `TEXT` | `NULL` | Địa chỉ trụ sở |
| `logo_path` | `VARCHAR(255)` | `NULL` | Logo NXB |
| `description` | `TEXT` | `NULL` | Giới thiệu về NXB |
| `is_verified` | `TINYINT(1)` | `NOT NULL`, `DEFAULT 0` | Trạng thái xác thực của Admin (0: Chưa, 1: Đã duyệt) |
| `created_at` | `TIMESTAMP` | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP` | Ngày tham gia hệ thống |
| `updated_at` | `TIMESTAMP` | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP` | Ngày cập nhật thông tin |

---

### 3.3. Bảng `categories` (Thể loại / Danh mục sách)
Lưu trữ danh mục phân loại sách, hỗ trợ phân cấp cha-con (hệ thống tab và bộ lọc).

| Tên Cột | Kiểu Dữ Liệu | Ràng Buộc | Ý Nghĩa / Mô Tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | ID thể loại |
| `parent_id` | `BIGINT UNSIGNED` | `NULL`, `FK -> categories(id)` | ID danh mục cha (nếu là danh mục con) |
| `name` | `VARCHAR(100)` | `NOT NULL` | Tên thể loại (Văn học, Kinh tế, Tâm lý...) |
| `slug` | `VARCHAR(120)` | `NOT NULL`, `UNIQUE` | Đường dẫn tĩnh thân thiện (URL Slug) |
| `icon` | `VARCHAR(50)` | `NULL` | Biểu tượng icon đại diện |
| `sort_order` | `INT` | `NOT NULL`, `DEFAULT 0` | Thứ tự ưu tiên hiển thị |
| `created_at` | `TIMESTAMP` | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP` | Ngày tạo |

---

### 3.4. Bảng `books` (Sách & Ấn phẩm điện tử)
Trung tâm của toàn bộ hệ thống bán và đọc sách.

| Tên Cột | Kiểu Dữ Liệu | Ràng Buộc | Ý Nghĩa / Mô Tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | ID cuốn sách |
| `publisher_id` | `BIGINT UNSIGNED` | `NULL`, `FK -> publishers(id)` | NXB phát hành sách |
| `category_id` | `BIGINT UNSIGNED` | `NULL`, `FK -> categories(id)` | Thể loại chính |
| `title` | `VARCHAR(255)` | `NOT NULL` | Tên sách |
| `slug` | `VARCHAR(280)` | `NOT NULL`, `UNIQUE` | URL slug chi tiết sách |
| `author` | `VARCHAR(160)` | `NOT NULL` | Tên tác giả |
| `description` | `TEXT` | `NULL` | Giới thiệu tóm tắt nội dung |
| `isbn` | `VARCHAR(20)` | `NULL`, `UNIQUE` | Mã chuẩn quốc tế ISBN |
| `publish_year` | `SMALLINT UNSIGNED` | `NULL` | Năm xuất bản |
| `pages` | `INT UNSIGNED` | `NULL` | Tổng số trang sách |
| `cover_path` | `VARCHAR(255)` | `NULL` | Đường dẫn ảnh bìa sách |
| `file_path` | `VARCHAR(255)` | `NULL` | Đường dẫn file nội dung (EPUB/PDF) |
| `file_format` | `VARCHAR(50)` | `NULL`, `DEFAULT 'EPUB'` | Định dạng sách điện tử |
| `list_price` | `DECIMAL(12,2)` | `NOT NULL`, `DEFAULT 0.00` | Giá bìa niêm yết |
| `digital_price` | `DECIMAL(12,2)` | `NOT NULL`, `DEFAULT 0.00` | Giá bán sách điện tử |
| `sale_price` | `DECIMAL(12,2)` | `NULL` | Giá bán sau khuyến mãi |
| `allow_activation_code`| `TINYINT(1)` | `NOT NULL`, `DEFAULT 1` | Cho phép tạo mã kích hoạt sách giấy |
| `allow_rental` | `TINYINT(1)` | `NOT NULL`, `DEFAULT 0` | Cho phép thuê sách điện tử |
| `status` | `ENUM('draft','pending','published','rejected')` | `NOT NULL`, `DEFAULT 'draft'` | Trạng thái: Bản nháp / Chờ duyệt / Đã xuất bản / Từ chối |
| `rejection_reason` | `TEXT` | `NULL` | Lý do từ chối kiểm duyệt (Admin phản hồi) |
| `submitted_at` | `TIMESTAMP` | `NULL` | Thời điểm gửi yêu cầu duyệt |
| `published_at` | `TIMESTAMP` | `NULL` | Thời điểm chính thức lên kệ |
| `total_readers` | `INT UNSIGNED` | `NOT NULL`, `DEFAULT 0` | Tổng lượt độc giả đã đọc (Cache counter) |
| `total_sold` | `INT UNSIGNED` | `NOT NULL`, `DEFAULT 0` | Tổng số lượng đã bán (Cache counter) |
| `avg_rating` | `DECIMAL(2,1)` | `NOT NULL`, `DEFAULT 0.0` | Điểm đánh giá TB (Cache counter: 0.0 - 5.0) |
| `total_reviews` | `INT UNSIGNED` | `NOT NULL`, `DEFAULT 0` | Tổng số lượt đánh giá (Cache counter) |
| `created_at` | `TIMESTAMP` | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP` | Ngày tạo bản ghi |
| `updated_at` | `TIMESTAMP` | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP` | Ngày cập nhật |

---

### 3.5. Bảng `book_chapters` (Mục lục & Danh sách chương)
Quản lý cấu trúc chương hồi của sách, phục vụ cho trình đọc `BookReader.php`.

| Tên Cột | Kiểu Dữ Liệu | Ràng Buộc | Ý Nghĩa / Mô Tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | ID chương sách |
| `book_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK -> books(id)` | Thuộc cuốn sách nào |
| `chapter_number` | `INT UNSIGNED` | `NOT NULL` | Số thứ tự chương (1, 2, 3...) |
| `title` | `VARCHAR(255)` | `NOT NULL` | Tên chương (VD: Phần một, Chương 1) |
| `start_page` | `INT UNSIGNED` | `NOT NULL`, `DEFAULT 1` | Trang bắt đầu của chương |

---

### 3.6. Bảng `orders` (Đơn hàng)
Quản lý giao dịch mua sách điện tử của người dùng.

| Tên Cột | Kiểu Dữ Liệu | Ràng Buộc | Ý Nghĩa / Mô Tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | ID đơn hàng |
| `user_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK -> users(id)` | Người mua hàng |
| `order_code` | `VARCHAR(20)` | `NOT NULL`, `UNIQUE` | Mã đơn hàng hiển thị (VD: DH001234) |
| `total` | `DECIMAL(12,2)` | `NOT NULL` | Tổng tiền gốc |
| `discount_amount` | `DECIMAL(12,2)` | `NOT NULL`, `DEFAULT 0.00` | Số tiền được giảm giá |
| `final_total` | `DECIMAL(12,2)` | `NOT NULL` | Tổng tiền thực trả |
| `payment_method` | `VARCHAR(50)` | `NULL` | Phương thức thanh toán (VNPAY, MoMo, Card...) |
| `status` | `ENUM('pending','paid','cancelled','failed')` | `NOT NULL`, `DEFAULT 'pending'` | Trạng thái thanh toán |
| `paid_at` | `TIMESTAMP` | `NULL` | Thời điểm thanh toán thành công |
| `created_at` | `TIMESTAMP` | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP` | Thời điểm tạo đơn |

---

### 3.7. Bảng `order_items` (Chi tiết đơn hàng)
Chi tiết từng cuốn sách trong đơn hàng.

| Tên Cột | Kiểu Dữ Liệu | Ràng Buộc | Ý Nghĩa / Mô Tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | ID dòng đơn hàng |
| `order_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK -> orders(id)` | Thuộc đơn hàng |
| `book_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK -> books(id)` | Cuốn sách được mua |
| `unit_price` | `DECIMAL(12,2)` | `NOT NULL` | Giá bán tại thời điểm mua |
| `discount_price` | `DECIMAL(12,2)` | `NULL` | Giá sau khi áp dụng khuyến mãi |
| `promotion_id` | `BIGINT UNSIGNED` | `NULL`, `FK -> promotions(id)` | Mã khuyến mãi đã áp dụng (nếu có) |

---

### 3.8. Bảng `reviews` (Đánh giá & Bình luận sách)
Lưu đánh giá số sao (1-5★) và bình luận từ độc giả.

| Tên Cột | Kiểu Dữ Liệu | Ràng Buộc | Ý Nghĩa / Mô Tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | ID đánh giá |
| `user_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK -> users(id)` | Độc giả đánh giá |
| `book_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK -> books(id)` | Sách được đánh giá |
| `rating` | `TINYINT UNSIGNED` | `NOT NULL`, `CHECK (rating BETWEEN 1 AND 5)` | Số sao đánh giá (1 đến 5 sao) |
| `comment` | `TEXT` | `NULL` | Nội dung nhận xét chi tiết |
| `created_at` | `TIMESTAMP` | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP` | Ngày đánh giá |

---

### 3.9. Bảng `favorites` (Sách yêu thích / Wishlist)
Danh sách sách được người dùng bấm "❤️ Yêu thích".

| Tên Cột | Kiểu Dữ Liệu | Ràng Buộc | Ý Nghĩa / Mô Tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | ID yêu thích |
| `user_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK -> users(id)` | Người dùng |
| `book_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK -> books(id)` | Cuốn sách yêu thích |
| `created_at` | `TIMESTAMP` | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP` | Thời điểm thêm vào yêu thích |

---

### 3.10. Bảng `user_books` (Thư viện sách cá nhân)
Quản lý quyền sở hữu sách của độc giả và đồng bộ tiến độ đọc giữa các thiết bị.

| Tên Cột | Kiểu Dữ Liệu | Ràng Buộc | Ý Nghĩa / Mô Tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | ID thư viện cá nhân |
| `user_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK -> users(id)` | Độc giả sở hữu |
| `book_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK -> books(id)` | Cuốn sách sở hữu |
| `order_id` | `BIGINT UNSIGNED` | `NULL`, `FK -> orders(id)` | Đơn hàng mua sách (nếu mua online) |
| `activation_code_id` | `BIGINT UNSIGNED` | `NULL`, `FK -> activation_codes(id)` | Mã kích hoạt (nếu nhận từ sách giấy) |
| `reading_status` | `ENUM('unread','reading','completed')` | `NOT NULL`, `DEFAULT 'unread'` | Trạng thái đọc (Chưa đọc/Đang đọc/Hoàn thành) |
| `progress_percent` | `TINYINT UNSIGNED` | `NOT NULL`, `DEFAULT 0` | Tiến độ đọc hiện tại (0 - 100%) |
| `current_chapter` | `INT UNSIGNED` | `NULL` | Chương đang đọc dở |
| `current_page` | `INT UNSIGNED` | `NULL` | Trang sách đang đọc dở |
| `last_read_at` | `TIMESTAMP` | `NULL` | Thời điểm đọc gần nhất |
| `completed_at` | `TIMESTAMP` | `NULL` | Thời điểm đọc hoàn thành cuốn sách |
| `acquired_at` | `TIMESTAMP` | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP` | Ngày nhận quyền sở hữu sách |

---

### 3.11. Bảng `highlights` (Trích dẫn & Ghi chú khi đọc)
Lưu các đoạn văn bản được highlight và ghi chú cá nhân trong quá trình đọc.

| Tên Cột | Kiểu Dữ Liệu | Ràng Buộc | Ý Nghĩa / Mô Tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | ID ghi chú |
| `user_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK -> users(id)` | Người tạo highlight |
| `book_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK -> books(id)` | Thuộc cuốn sách nào |
| `quote` | `TEXT` | `NOT NULL` | Đoạn trích dẫn nguyên văn |
| `note` | `TEXT` | `NULL` | Ghi chú hoặc cảm nhận cá nhân |
| `page` | `INT UNSIGNED` | `NOT NULL` | Trang sách được đánh dấu |
| `chapter_id` | `BIGINT UNSIGNED` | `NULL`, `FK -> book_chapters(id)` | Thuộc chương nào |
| `highlight_color` | `VARCHAR(7)` | `NOT NULL`, `DEFAULT '#FFCA3A'` | Mã màu highlight (HEX, VD: #FFCA3A, #087E8B) |
| `created_at` | `TIMESTAMP` | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP` | Ngày tạo |

---

### 3.12. Bảng `reading_sessions` (Phiên đọc sách & Thời gian đọc)
Ghi nhận từng phiên đọc thực tế để tính thời gian đọc trung bình cho Publisher & Dashboard.

| Tên Cột | Kiểu Dữ Liệu | Ràng Buộc | Ý Nghĩa / Mô Tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | ID phiên đọc |
| `user_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK -> users(id)` | Độc giả |
| `book_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK -> books(id)` | Cuốn sách đang đọc |
| `started_at` | `TIMESTAMP` | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP` | Thời điểm bắt đầu mở sách |
| `ended_at` | `TIMESTAMP` | `NULL` | Thời điểm đóng sách |
| `duration_minutes`| `INT UNSIGNED` | `NOT NULL`, `DEFAULT 0` | Tổng thời lượng đọc (phút) |
| `pages_read` | `INT UNSIGNED` | `NOT NULL`, `DEFAULT 0` | Số trang đã lật trong phiên |

---

### 3.13. Bảng `reading_goals` (Mục tiêu rèn luyện đọc sách hàng ngày)
Phục vụ Widget "Mục tiêu hôm nay" trên trang Thư viện.

| Tên Cột | Kiểu Dữ Liệu | Ràng Buộc | Ý Nghĩa / Mô Tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | ID mục tiêu |
| `user_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK -> users(id)` | Người dùng đặt mục tiêu |
| `daily_minutes` | `INT UNSIGNED` | `NOT NULL`, `DEFAULT 20` | Mục tiêu thời gian đọc trong ngày (phút) |
| `date` | `DATE` | `NOT NULL` | Ngày ghi nhận (YYYY-MM-DD) |
| `achieved_minutes`| `INT UNSIGNED` | `NOT NULL`, `DEFAULT 0` | Số phút đã đọc được trong ngày |
| `is_completed` | `TINYINT(1)` | `NOT NULL`, `DEFAULT 0` | Trạng thái đạt mục tiêu (0: Chưa, 1: Đã đạt) |

---

### 3.14. Bảng `activation_codes` (Mã kích hoạt sách điện tử)
Phát hành mã cào cho sách in giấy để người mua kích hoạt đọc bản số trên Readly.

| Tên Cột | Kiểu Dữ Liệu | Ràng Buộc | Ý Nghĩa / Mô Tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | ID mã kích hoạt |
| `book_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK -> books(id)` | Sách được gán mã |
| `publisher_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK -> publishers(id)` | NXB phát hành mã |
| `code` | `VARCHAR(20)` | `NOT NULL`, `UNIQUE` | Chuỗi mã kích hoạt (VD: RDL-Y7KP-9H2M) |
| `status` | `ENUM('unused','used','expired')` | `NOT NULL`, `DEFAULT 'unused'` | Trạng thái: Chưa dùng / Đã dùng / Hết hạn |
| `used_by` | `BIGINT UNSIGNED` | `NULL`, `FK -> users(id)` | Người dùng đã kích hoạt |
| `used_at` | `TIMESTAMP` | `NULL` | Thời điểm kích hoạt thành công |
| `expires_at` | `TIMESTAMP` | `NULL` | Hạn chót sử dụng mã |
| `created_at` | `TIMESTAMP` | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP` | Ngày sinh mã |

---

### 3.15. Bảng `promotions` (Chiến dịch khuyến mãi)
Quản lý các chương trình Flash Sale & Voucher do Nhà xuất bản hoặc Admin tổ chức.

| Tên Cột | Kiểu Dữ Liệu | Ràng Buộc | Ý Nghĩa / Mô Tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | ID chiến dịch |
| `publisher_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK -> publishers(id)` | NXB tổ chức |
| `name` | `VARCHAR(200)` | `NOT NULL` | Tên chiến dịch (VD: Ưu đãi sách mùa hè) |
| `type` | `ENUM('flash_sale','voucher')` | `NOT NULL` | Hình thức khuyến mãi |
| `discount_percent`| `TINYINT UNSIGNED` | `NOT NULL` | Mức giảm theo phần trăm (1% - 100%) |
| `max_uses` | `INT UNSIGNED` | `NULL` | Giới hạn số lượt sử dụng tối đa |
| `used_count` | `INT UNSIGNED` | `NOT NULL`, `DEFAULT 0` | Số lượt đã sử dụng thực tế |
| `start_date` | `DATE` | `NOT NULL` | Ngày bắt đầu áp dụng |
| `end_date` | `DATE` | `NOT NULL` | Ngày kết thúc |
| `is_active` | `TINYINT(1)` | `NOT NULL`, `DEFAULT 1` | Trạng thái kích hoạt (0: Tắt, 1: Bật) |
| `created_at` | `TIMESTAMP` | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP` | Ngày tạo chiến dịch |

---

### 3.16. Bảng `promotion_books` (Liên kết Sách — Khuyến mãi)
Bảng trung gian thiết lập mối quan hệ Nhiều - Nhiều (N:N) giữa `promotions` và `books`.

| Tên Cột | Kiểu Dữ Liệu | Ràng Buộc | Ý Nghĩa / Mô Tả |
| :--- | :--- | :--- | :--- |
| `promotion_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK -> promotions(id)` | ID chiến dịch khuyến mãi |
| `book_id` | `BIGINT UNSIGNED` | `NOT NULL`, `FK -> books(id)` | ID sách được áp dụng khuyến mãi |

*(Khóa chính là cặp `PRIMARY KEY (promotion_id, book_id)`)*

---

### 3.17. Bảng `newsletter_subscribers` (Đăng ký nhận bản tin)
Thu thập danh sách email đăng ký nhận thông tin sách mới và ưu đãi từ chân trang.

| Tên Cột | Kiểu Dữ Liệu | Ràng Buộc | Ý Nghĩa / Mô Tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY`, `AUTO_INCREMENT` | ID bản ghi |
| `email` | `VARCHAR(190)` | `NOT NULL`, `UNIQUE` | Địa chỉ email đăng ký |
| `is_active` | `TINYINT(1)` | `NOT NULL`, `DEFAULT 1` | Trạng thái nhận tin (1: Đang nhận, 0: Đã hủy) |
| `subscribed_at` | `TIMESTAMP` | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP` | Thời điểm đăng ký |
| `unsubscribed_at` | `TIMESTAMP` | `NULL` | Thời điểm hủy đăng ký (nếu có) |

---

## 4. SƠ ĐỒ QUAN HỆ THỰC THỂ (ERD DIAGRAM)

```text
=============================================================================================
                           SƠ ĐỒ QUAN HỆ DATABASE (ERD DIAGRAM)
=============================================================================================

                               +--------------------------+
                               |  newsletter_subscribers  |
                               +--------------------------+

     +-----------------------+              +------------------------+
     |         users         |---- 1:1 ---->|       publishers       |
     +-----------------------+              +------------------------+
     | id (PK)               |              | id (PK)                |
     | name, email, phone    |              | user_id (FK -> users)  |
     | password_hash         |              | company_name, logo     |
     | role (enum)           |              | is_verified            |
     | social_provider/id    |              +-----------+------------+
     +---+---+---+---+---+---+                          |
         |   |   |   |   |                              | 1:N
         |   |   |   |   |                              v
         |   |   |   |   |                  +------------------------+      +------------------+
         |   |   |   |   |                  |         books          |--N:N-| promotion_books  |
         |   |   |   |   |                  +------------------------+      +------------------+
         |   |   |   |   |                  | id (PK)                |      | promotion_id(FK) |
         |   |   |   |   |                  | publisher_id (FK)      |      | book_id (FK)     |
         |   |   |   |   |                  | category_id (FK)       |      +--------+---------+
         |   |   |   |   |                  | title, slug, author    |               |
         |   |   |   |   |                  | list_price, sale_price |               v
         |   |   |   |   |                  | status, avg_rating     |      +------------------+
         |   |   |   |   |                  +---+----+----+----+-----+      |    promotions    |
         |   |   |   |   |                      |    |    |    |            +------------------+
         |   |   |   |   |                      |    |    |    |            | id (PK)          |
         |   |   |   |   |                      |    |    |    |            | publisher_id(FK) |
         |   |   |   |   |                      |    |    |    |            | discount_percent |
         |   |   |   |   |                      |    |    |    |            +------------------+
         |   |   |   |   |                      |    |    |    |
         |   |   |   |   |                      |    |    |    +--- 1:N --->+------------------+
         |   |   |   |   |                      |    |    |                 |  book_chapters   |
         |   |   |   |   |                      |    |    |                 +------------------+
         |   |   |   |   |                      |    |    |                 | id (PK)          |
         |   |   |   |   |                      |    |    |                 | book_id (FK)     |
         |   |   |   |   |                      |    |    |                 | chapter_number   |
         |   |   |   |   |                      |    |    |                 +------------------+
         |   |   |   |   |                      |    |    |
         |   |   |   |   |                      |    |    +--- N:1 -------->+------------------+
         |   |   |   |   |                      |    |                      |    categories    |
         |   |   |   |   |                      |    |                      +------------------+
         |   |   |   |   |                      |    |                      | id (PK)          |
         |   |   |   |   |                      |    |                      | parent_id (Self) |
         |   |   |   |   |                      |    |                      | name, slug       |
         |   |   |   |   |                      |    |                      +------------------+
         |   |   |   |   |                      |    |
         |   |   |   |   |                      |    +--- 1:N ------------->+--------------------+
         |   |   |   |   |                      |                           |  activation_codes  |
         |   |   |   |   |                      |                           +--------------------+
         |   |   |   |   |                      |                           | id (PK)            |
         |   |   |   |   |                      |                           | code (UNIQUE)      |
         |   |   |   |   |                      |                           | book_id (FK)       |
         |   |   |   |   |                      |                           | publisher_id (FK)  |
         |   |   |   |   |                      |                           | used_by (FK->User) |
         |   |   |   |   |                      |                           +--------------------+
         |   |   |   |   |                      |
         v   |   |   v   v                      v
    +--------+   |  +------------+       +--------------+
    |reviews |   |  | user_books |       |  favorites   |
    +--------+   |  +------------+       +--------------+
    |user_id |   |  |user_id (FK)|       |user_id (FK)  |
    |book_id |   |  |book_id (FK)|       |book_id (FK)  |
    |rating  |   |  |progress    |       +--------------+
    +--------+   |  |status      |
                 |  +------------+
                 |
                 +-------------------+--------------------+
                 |                   |                    |
                 v                   v                    v
        +------------------+  +--------------------+  +---------------+
        |    highlights    |  |  reading_sessions  |  | reading_goals |
        +------------------+  +--------------------+  +---------------+
        | user_id (FK)     |  | user_id (FK)       |  | user_id (FK)  |
        | book_id (FK)     |  | book_id (FK)       |  | daily_minutes |
        | quote, page      |  | duration_minutes   |  | date, achieved|
        | highlight_color  |  | started_at         |  +---------------+
        +------------------+  +--------------------+

        +------------------+          +------------------+
        |      orders      |-- 1:N -->|   order_items    |
        +------------------+          +------------------+
        | id (PK)          |          | id (PK)          |
        | user_id (FK)     |          | order_id (FK)    |
        | order_code       |          | book_id (FK)     |
        | final_total      |          | unit_price       |
        | status           |          | promotion_id(FK) |
        +------------------+          +------------------+
```

---

## 5. MA TRẬN KHÓA NGOẠI & QUAN HỆ (FOREIGN KEYS)

Hệ thống thiết lập 25 ràng buộc khóa ngoại (Foreign Keys) nhằm đảm bảo tính toàn vẹn dữ liệu (Referential Integrity):

| # | Bảng Chứa Khóa Ngoại | Cột Khóa Ngoại | Bảng Tham Chiếu (Parent) | Hành Động ON DELETE | Ý Nghĩa Thực Tế |
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | `publishers` | `user_id` | `users(id)` | `CASCADE` | Xóa user NXB thì xóa luôn hồ sơ NXB |
| 2 | `categories` | `parent_id` | `categories(id)` | `SET NULL` | Xóa danh mục cha thì danh mục con chuyển thành danh mục gốc |
| 3 | `books` | `publisher_id` | `publishers(id)` | `SET NULL` | Giữ lại sách khi NXB ngừng hoạt động |
| 4 | `books` | `category_id` | `categories(id)` | `SET NULL` | Xóa danh mục không làm mất sách |
| 5 | `book_chapters` | `book_id` | `books(id)` | `CASCADE` | Xóa sách thì xóa toàn bộ mục lục chương |
| 6 | `orders` | `user_id` | `users(id)` | `RESTRICT` | Không cho xóa user nếu đã phát sinh đơn hàng |
| 7 | `promotions` | `publisher_id` | `publishers(id)` | `CASCADE` | Xóa NXB thì xóa các chiến dịch khuyến mãi của NXB đó |
| 8 | `order_items` | `order_id` | `orders(id)` | `CASCADE` | Xóa đơn hàng thì xóa chi tiết đơn hàng |
| 9 | `order_items` | `book_id` | `books(id)` | `RESTRICT` | Không được xóa sách nếu sách đã có trong đơn hàng |
| 10 | `order_items` | `promotion_id` | `promotions(id)` | `SET NULL` | Xóa khuyến mãi thì giữ nguyên lịch sử đơn hàng |
| 11 | `promotion_books` | `promotion_id`| `promotions(id)` | `CASCADE` | Xóa khuyến mãi thì xóa liên kết sách |
| 12 | `promotion_books` | `book_id` | `books(id)` | `CASCADE` | Xóa sách thì gỡ sách ra khỏi khuyến mãi |
| 13 | `reviews` | `user_id` | `users(id)` | `CASCADE` | Xóa user thì xóa các đánh giá của họ |
| 14 | `reviews` | `book_id` | `books(id)` | `CASCADE` | Xóa sách thì xóa các đánh giá của sách đó |
| 15 | `favorites` | `user_id` | `users(id)` | `CASCADE` | Xóa user thì xóa danh sách yêu thích |
| 16 | `favorites` | `book_id` | `books(id)` | `CASCADE` | Xóa sách thì xóa khỏi wishlist của người dùng |
| 17 | `activation_codes`| `book_id` | `books(id)` | `RESTRICT` | Không xóa sách khi đang có mã kích hoạt |
| 18 | `activation_codes`| `publisher_id`| `publishers(id)` | `RESTRICT` | Không xóa NXB khi đang lưu hành mã kích hoạt |
| 19 | `activation_codes`| `used_by` | `users(id)` | `SET NULL` | Xóa user thì mã đã kích hoạt vẫn lưu lịch sử |
| 20 | `user_books` | `user_id` | `users(id)` | `CASCADE` | Xóa user thì xóa tủ sách cá nhân |
| 21 | `user_books` | `book_id` | `books(id)` | `RESTRICT` | Không xóa sách gốc nếu người dùng đã sở hữu |
| 22 | `user_books` | `order_id` | `orders(id)` | `SET NULL` | Liên kết nguồn gốc mua hàng |
| 23 | `user_books` | `activation_code_id` | `activation_codes(id)` | `SET NULL` | Liên kết nguồn gốc kích hoạt từ mã cào |
| 24 | `highlights` | `user_id` | `users(id)` | `CASCADE` | Xóa user thì xóa toàn bộ ghi chú của họ |
| 25 | `highlights` | `book_id` | `books(id)` | `CASCADE` | Xóa sách thì xóa ghi chú liên quan |

---

## 6. CHIẾN LƯỢC ĐÁNH CHỈ MỤC (INDEXES OPTIMIZATION)

| Bảng | Tên Index | Các Cột Đánh Index | Loại Index | Mục Đích Tối Ưu Truy Vấn |
| :--- | :--- | :--- | :--- | :--- |
| `users` | `uk_users_email` | `(email)` | `UNIQUE` | Tra cứu đăng nhập bằng Email cực nhanh |
| `users` | `uk_users_phone` | `(phone)` | `UNIQUE` | Tra cứu đăng nhập bằng Số điện thoại |
| `users` | `uk_users_social` | `(social_provider, social_id)` | `UNIQUE` | Đăng nhập Google/Facebook 1 chạm |
| `users` | `idx_users_role` | `(role)` | `INDEX` | Lọc danh sách Độc giả / NXB / Admin |
| `books` | `uk_books_slug` | `(slug)` | `UNIQUE` | Tải trang chi tiết sách theo URL SEO |
| `books` | `uk_books_isbn` | `(isbn)` | `UNIQUE` | Quản lý kiểm kê sách chuẩn quốc tế |
| `books` | `idx_books_status` | `(status)` | `INDEX` | Lọc sách đã xuất bản ra Storefront, sách chờ duyệt cho Admin |
| `books` | `idx_books_category`| `(category_id)` | `INDEX` | Lọc sách theo thể loại ở `BookListPage.php` |
| `books` | `idx_books_publisher`| `(publisher_id)` | `INDEX` | Tải danh sách sản phẩm trong Publisher Dashboard |
| `books` | `idx_books_rating` | `(avg_rating)` | `INDEX` | Lọc và sắp xếp sách theo điểm đánh giá (4★+, 5★) |
| `books` | `idx_books_sold` | `(total_sold)` | `INDEX` | Sắp xếp sách bán chạy (Best Seller) |
| `books` | `ft_books_search` | `(title, author, description)` | `FULLTEXT` | Tìm kiếm toàn văn thông minh tại `SearchResultsPage.php` |
| `orders` | `uk_orders_code` | `(order_code)` | `UNIQUE` | Tra cứu đơn hàng nhanh theo mã giao dịch |
| `orders` | `idx_orders_user` | `(user_id)` | `INDEX` | Tải lịch sử mua hàng của độc giả |
| `orders` | `idx_orders_status_created` | `(status, created_at)` | `INDEX` | Báo cáo doanh thu & biểu đồ theo mốc thời gian |
| `user_books` | `uk_ub_user_book`| `(user_id, book_id)` | `UNIQUE` | Đảm bảo mỗi độc giả chỉ sở hữu 1 bản của cuốn sách |
| `user_books` | `idx_ub_reading_status` | `(user_id, reading_status)` | `INDEX` | Chuyển tab Đang đọc / Chưa đọc / Đã xong tại Library |
| `reviews` | `uk_reviews_user_book` | `(user_id, book_id)` | `UNIQUE` | Mỗi độc giả chỉ đánh giá 1 lần cho mỗi cuốn sách |
| `favorites` | `uk_favorites_user_book` | `(user_id, book_id)` | `UNIQUE` | Chống trùng lặp nút Yêu thích |
| `activation_codes`| `uk_activation_code` | `(code)` | `UNIQUE` | Kích hoạt mã sách tức thì |
| `reading_goals` | `uk_rg_user_date`| `(user_id, date)` | `UNIQUE` | Mỗi ngày chỉ có 1 bản ghi mục tiêu/người |

---

## 7. MÃ NGUỒN SQL HOÀN CHỈNH KHỞI TẠO DATABASE

```sql
-- =============================================================================
-- ONLINE eBOOK STORE & READER (READLY) — FULL DATABASE INITIALIZATION
-- Hệ quản trị: MySQL 8.4+
-- Bộ ký tự: utf8mb4 / Collation: utf8mb4_unicode_ci
-- =============================================================================

CREATE DATABASE IF NOT EXISTS `ebook_store`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `ebook_store`;

-- Tắt kiểm tra khóa ngoại tạm thời để khởi tạo theo thứ tự
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- 1. BẢNG USERS (Người dùng)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id`                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`              VARCHAR(120)    NOT NULL,
  `email`             VARCHAR(190)    NOT NULL,
  `phone`             VARCHAR(20)     NULL,
  `password_hash`     VARCHAR(255)    NOT NULL,
  `role`              ENUM('customer', 'publisher', 'admin') NOT NULL DEFAULT 'customer',
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

-- -----------------------------------------------------------------------------
-- DỮ LIỆU MẪU BAN ĐẦU (SEED DATA)
-- -----------------------------------------------------------------------------

-- 1. Danh mục chuẩn theo giao diện
INSERT INTO `categories` (`name`, `slug`, `sort_order`) VALUES
  ('Văn học',       'van-hoc',       1),
  ('Kinh tế',       'kinh-te',       2),
  ('Tâm lý',        'tam-ly',        3),
  ('Kỹ năng sống',  'ky-nang-song',  4),
  ('Thiếu nhi',     'thieu-nhi',     5),
  ('Ngoại ngữ',     'ngoai-ngu',     6),
  ('Khoa học',       'khoa-hoc',      7),
  ('Kinh doanh',    'kinh-doanh',    8),
  ('Giáo dục',      'giao-duc',      9);

-- 2. Tài khoản mẫu Admin & Publisher
INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`) VALUES
  (1, 'Admin Readly', 'admin@readly.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
  (2, 'NXB Kim Đồng', 'nxb@kimdong.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'publisher'),
  (3, 'Độc Giả Demo', 'reader@readly.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer');

-- 3. Hồ sơ NXB Kim Đồng
INSERT INTO `publishers` (`id`, `user_id`, `company_name`, `company_email`, `is_verified`) VALUES
  (1, 2, 'NXB Kim Đồng', 'nxb@kimdong.vn', 1);

-- 4. Sách mẫu khớp với HomePage, BookDetail & Publisher Dashboard
INSERT INTO `books` (`id`, `publisher_id`, `category_id`, `title`, `slug`, `author`, `description`, `list_price`, `digital_price`, `status`, `avg_rating`, `total_sold`, `total_readers`) VALUES
  (1, 1, 1, 'Nhà giả kim', 'nha-gia-kim', 'Paulo Coelho', 'Chuyến phiêu lưu kỳ diệu của Santiago theo đuổi giấc mơ tìm kho báu ở Kim tự tháp Ai Cập.', 129000, 89000, 'published', 4.8, 12500, 12500),
  (2, 1, 1, 'Cây cam ngọt của tôi', 'cay-cam-ngot-cua-toi', 'José Mauro de Vasconcelos', 'Hành trình khám phá thế giới đầy yêu thương và nước mắt của cậu bé Zezé.', 139000, 95000, 'published', 4.9, 18200, 18200),
  (3, 1, 3, 'Tư duy nhanh và chậm', 'tu-duy-nhanh-va-cham', 'Daniel Kahneman', 'Khám phá hai hệ thống tư duy chi phối mọi quyết định của con người.', 199000, 159000, 'published', 4.7, 8900, 8900),
  (4, 1, 4, 'Ikigai', 'ikigai', 'Héctor García', 'Bí mật sống trường thọ và hạnh phúc của người Nhật.', 99000, 79000, 'published', 4.6, 15300, 15300);
```
