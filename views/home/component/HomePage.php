<?php


$pdo = db();



$stmt = $pdo->query("
    SELECT
        id,
        title,
        author,
        cover_path,
        cover_color,
        digital_price,
        sale_price,
        avg_rating,
        total_readers,
        total_sold
    FROM books
    WHERE status = 'published'
    ORDER BY id DESC
    LIMIT 4
");

$favoriteBooks = $stmt->fetchAll();



// Fetch categories dynamically
$catStmt = $pdo->query("SELECT id, name FROM categories ORDER BY sort_order ASC");
$homeCategories = $catStmt->fetchAll();

$selectedCatId = isset($_GET['category']) ? (int)$_GET['category'] : 0;

if ($selectedCatId > 0) {
    $bsStmt = $pdo->prepare("
        SELECT id, title, author, cover_path, cover_color, digital_price, sale_price, avg_rating, total_readers, total_sold
        FROM books
        WHERE status = 'published' AND category_id = ?
        ORDER BY total_sold DESC, id DESC
        LIMIT 8
    ");
    $bsStmt->execute([$selectedCatId]);
    $bestSellerBooks = $bsStmt->fetchAll();
} else {
    $bsStmt = $pdo->query("
        SELECT id, title, author, cover_path, cover_color, digital_price, sale_price, avg_rating, total_readers, total_sold
        FROM books
        WHERE status = 'published'
        ORDER BY total_sold DESC, id DESC
        LIMIT 8
    ");
    $bestSellerBooks = $bsStmt->fetchAll();
}

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
            include __DIR__ . '/BookCards.php';
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

        <div class="category-tabs" id="bestSellerCategoryTabs">

            <button
                type="button"
                onclick="loadCategoryBooks(0, this)"
                class="category-tab <?= $selectedCatId === 0 ? 'active' : '' ?>"
                style="background:none; border:none; cursor:pointer; font-family:inherit;"
            >
                Tất cả
            </button>

            <?php foreach ($homeCategories as $cat): ?>
                <button
                    type="button"
                    onclick="loadCategoryBooks(<?= (int)$cat['id'] ?>, this)"
                    class="category-tab <?= $selectedCatId === (int)$cat['id'] ? 'active' : '' ?>"
                    style="background:none; border:none; cursor:pointer; font-family:inherit;"
                >
                    <?= e($cat['name']) ?>
                </button>
            <?php endforeach; ?>

        </div>

    </div>

    <div class="best-seller-grid favorite-grid" id="bestSellerGrid" style="transition: opacity 0.2s;">

        <?php if (!empty($bestSellerBooks)): ?>
            <?php
            $books = $bestSellerBooks;
            include __DIR__ . '/BookCards.php';
            ?>
        <?php else: ?>
            <p style="grid-column: 1/-1; text-align: center; color: #6B7280; padding: 40px 0;">Chưa có sách thuộc thể loại này.</p>
        <?php endif; ?>

    </div>

    <script>
    function loadCategoryBooks(catId, btnEl) {
        const tabs = document.querySelectorAll('#bestSellerCategoryTabs .category-tab');
        tabs.forEach(t => t.classList.remove('active'));
        if (btnEl) btnEl.classList.add('active');

        const grid = document.getElementById('bestSellerGrid');
        if (!grid) return;

        grid.style.opacity = '0.4';

        fetch('/api/get_books_by_category?category=' + catId)
            .then(res => res.text())
            .then(html => {
                grid.innerHTML = html;
                grid.style.opacity = '1';
            })
            .catch(err => {
                console.error('Lỗi tải thể loại:', err);
                grid.style.opacity = '1';
            });
    }
    </script>

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
            include __DIR__ . '/BookCards.php';
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