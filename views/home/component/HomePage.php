<?php


$pdo = db();



$stmt = $pdo->query("
    SELECT
        id,
        title,
        author,
        price AS digital_price,
        0 AS sale_price,
        0 AS avg_rating,
        0 AS total_readers,
        0 AS total_sold,
        '' AS cover_color
    FROM books
    WHERE status = 'published'
    ORDER BY id DESC
    LIMIT 4
");

$favoriteBooks = $stmt->fetchAll();



$stmt = $pdo->query("
    SELECT
        id,
        title,
        author,
        price AS digital_price,
        0 AS sale_price,
        0 AS avg_rating,
        0 AS total_readers,
        0 AS total_sold,
        '' AS cover_color
    FROM books
    WHERE status = 'published'
    ORDER BY id DESC
    LIMIT 8
");

$bestSellerBooks = $stmt->fetchAll();



$recommendedBooks = $bestSellerBooks;

?>

<section class="hero-section home-container">

    <div class="hero-box">

        <div class="hero-grid">

            <div class="hero-content">

                <p class="hero-label">
                    Sách hay không phải để lướt qua.
                </p>

                <h1>
                    Khám phá cuốn sách đúng lúc bạn cần
                </h1>

                <p class="hero-description">
                    Từ những câu chuyện chữa lành đến kiến thức giúp bạn
                    tiến xa hơn — tất cả trong một thư viện đọc thật dễ chịu.
                </p>

                <div class="hero-action">

                    <a
                        href="/library"
                        class="primary-yellow-button"
                        style="text-decoration:none;"
                    >
                        <span class="font-semibold">
                            Bắt đầu đọc
                        </span>

                        <span>→</span>
                    </a>

                    <p>
                        Hơn 20.000 tựa sách đang chờ bạn
                    </p>

                </div>

            </div>

            <div class="hero-book-area">

                <div class="hero-book">

                    <div class="hero-book-content">

                        <div class="hero-book-title">
                            ĐỌC — MỞ RA MỘT THẾ GIỚI MỚI
                        </div>

                        <div class="hero-book-line"></div>

                    </div>

                    <div class="reader-badge">
                        32K+ độc giả
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<section class="home-section home-container">

    <div class="section-heading">

        <div>

            <h2>
                Sách được yêu thích
            </h2>

            <p>
                Những lựa chọn được cộng đồng Readly yêu mến nhất tuần này.
            </p>

        </div>

        <a
            href="/home?view=book-list"
            class="outline-button"
            style="text-decoration:none;"
        >
            Xem danh sách
            <span>→</span>
        </a>

    </div>

    <div class="favorite-grid">

        <?php if ($favoriteBooks): ?>

            <?php
            $books = $favoriteBooks;
            include _DIR_ . '/BookCards.php';
            ?>

        <?php else: ?>

            <p>
                Chưa có sách để hiển thị.
            </p>

        <?php endif; ?>

    </div>

</section>

<section class="home-section home-container best-seller-section">

    <div class="section-title-block">

        <h2>
            Best Seller
        </h2>

        <p>
            Những cuốn sách bán chạy nhất trong tháng
        </p>

        <div class="category-tabs">

            <a
                href="/home?view=book-list&category=1"
                class="category-tab active"
            >
                Văn học
            </a>

            <a
                href="/home?view=book-list&category=2"
                class="category-tab"
            >
                Kinh tế
            </a>

            <a
                href="/home?view=book-list&category=3"
                class="category-tab"
            >
                Tâm lý
            </a>

            <a
                href="/home?view=book-list&category=4"
                class="category-tab"
            >
                Kỹ năng sống
            </a>

            <a
                href="/home?view=book-list&category=5"
                class="category-tab"
            >
                Thiếu nhi
            </a>

            <a
                href="/home?view=book-list&category=6"
                class="category-tab"
            >
                Ngoại ngữ
            </a>

            <a
                href="/home?view=book-list&category=7"
                class="category-tab"
            >
                Khoa học
            </a>

        </div>

    </div>

    <div class="best-seller-grid">

        <?php foreach ($bestSellerBooks as $book): ?>

            <a
                href="/home?view=book-detail&book=<?= (int) $book['id']; ?>"
                class="book-card"
                style="text-decoration:none;color:inherit;"
            >

                <div class="book-cover favorite-cover">

                    <div class="book-cover-content">

                        <div class="favorite-cover-title">
                            <?= e($book['title']); ?>
                        </div>

                        <div class="favorite-cover-author">
                            <?= e($book['author']); ?>
                        </div>

                    </div>

                </div>

                <div class="book-card-content">

                    <h3>
                        <?= e($book['title']); ?>
                    </h3>

                    <p class="book-author">
                        <?= e($book['author']); ?>
                    </p>

                    <div class="book-rating-row">

                        <div class="book-rating">

                            <span>
                                ★
                            </span>

                            <span style="font-weight:600;">
                                <?= number_format(
                                    (float) $book['avg_rating'],
                                    1
                                ); ?>
                            </span>

                        </div>

                        <span class="book-readers">
                            • <?= number_format(
                                (int) $book['total_readers']
                            ); ?> đã đọc
                        </span>

                    </div>

                    <div class="book-price">

                        <?php
                        $price = $book['sale_price']
                            ?: $book['digital_price'];
                        ?>

                        <?= number_format(
                            (int) $price,
                            0,
                            ',',
                            '.'
                        ); ?>đ

                    </div>

                </div>

            </a>

        <?php endforeach; ?>

    </div>

    <div class="pagination-row">

        <div class="pagination">

            <a href="/home?view=book-list&page=1">
                01
            </a>

            <a href="/home?view=book-list&page=2">
                02
            </a>

            <a href="/home?view=book-list&page=3">
                03
            </a>

        </div>

        <a
            href="/home?view=book-list"
            class="view-all-link"
            style="text-decoration:none;"
        >
            Xem tất cả
            <span>→</span>
        </a>

    </div>

</section>

<section class="recommendation-section">

    <div class="home-container">

        <div class="recommendation-header">

            <div>

                <h2>
                    Gợi ý cho bạn
                </h2>

                <p>
                    Chọn lọc từ những điều bạn đã lưu và hay đọc.
                </p>

            </div>

            <div class="personal-badge">

                <div>
                    Dành riêng cho bạn
                </div>

                <div>
                    Cập nhật hôm nay
                </div>

            </div>

        </div>

        <div class="recommended-grid">

            <?php
            $books = array_slice($recommendedBooks, 0, 4);
            include _DIR_ . '/BookCards.php';
            ?>

        </div>

        <div class="recommendation-footer">

            <a
                href="/home?view=book-list"
                class="outline-button"
                style="text-decoration:none;"
            >
                Xem tất cả
                <span>→</span>
            </a>

        </div>

    </div>

</section>