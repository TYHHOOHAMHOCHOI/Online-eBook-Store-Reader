<?php

declare(strict_types=1);

if (!function_exists('e')) {
    function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('formatPrice')) {
    function formatPrice(float|int|null $price): string
    {
        return number_format((float)$price, 0, ',', '.') . ' đ';
    }
}

$bookId = (int)($_GET['book'] ?? 0);

if ($bookId <= 0) {
    echo '<div class="container"><p>Không tìm thấy sách.</p></div>';
    return;
}

$db = db();



$userId = (int)($_SESSION['user_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_favorite'])) {

    if ($userId <= 0) {
        header('Location: /home?view=login');
        exit;
    }

    $check = $db->prepare("
        SELECT id
        FROM favorites
        WHERE user_id = ? AND book_id = ?
        LIMIT 1
    ");

    $check->execute([$userId, $bookId]);
    $favorite = $check->fetch(PDO::FETCH_ASSOC);

    if ($favorite) {
        $delete = $db->prepare("
            DELETE FROM favorites
            WHERE user_id = ? AND book_id = ?
        ");

        $delete->execute([$userId, $bookId]);
    } else {
        $insert = $db->prepare("
            INSERT INTO favorites (user_id, book_id, created_at)
            VALUES (?, ?, NOW())
        ");

        $insert->execute([$userId, $bookId]);
    }

    header('Location: /home?view=book-detail&book=' . $bookId);
    exit;
}

/*
|--------------------------------------------------------------------------
| XỬ LÝ MUA SÁCH BẰNG SỐ DƯ TÀI KHOẢN
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy_book'])) {
    if ($userId <= 0) {
        header('Location: /home?view=login');
        exit;
    }

    $stmtPrice = $db->prepare("SELECT digital_price, sale_price FROM books WHERE id = ? LIMIT 1");
    $stmtPrice->execute([$bookId]);
    $priceData = $stmtPrice->fetch(PDO::FETCH_ASSOC);

    $dPrice = (float)($priceData['digital_price'] ?? 0);
    $sPrice = $priceData['sale_price'] !== null ? (float)$priceData['sale_price'] : 0;
    $targetPrice = $sPrice > 0 ? $sPrice : $dPrice;

    $userBalance = (float)($_SESSION['user_balance'] ?? 0);

    if ($userBalance < $targetPrice) {
        $_SESSION['buy_error'] = 'Số dư tài khoản không đủ để mua sách (' . number_format($targetPrice, 0, ',', '.') . 'đ). Vui lòng nạp thêm tiền!';
    } else {
        $_SESSION['user_balance'] = $userBalance - $targetPrice;

        try {
            $db->prepare("UPDATE users SET balance = GREATEST(0, COALESCE(balance, 0) - ?) WHERE id = ?")
               ->execute([$targetPrice, $userId]);

            try {
                $db->exec("CREATE TABLE IF NOT EXISTS user_books (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    user_id BIGINT UNSIGNED NOT NULL,
                    book_id BIGINT UNSIGNED NOT NULL,
                    acquired_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY uk_ub_user_book (user_id, book_id)
                )");
            } catch (Throwable $t) {}

            $db->prepare("INSERT INTO user_books (user_id, book_id, acquired_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE acquired_at = NOW()")
               ->execute([$userId, $bookId]);

            $_SESSION['buy_success'] = 'Chúc mừng! Bạn đã mua sách thành công và đã được thêm vào Thư viện của bạn.';
        } catch (Throwable $t) {
            error_log("Buy book DB error: " . $t->getMessage());
        }
    }

    header('Location: /home?view=book-detail&book=' . $bookId);
    exit;
}


$stmt = $db->prepare("
    SELECT
        b.*,
        c.name AS category_name,
        p.company_name AS publisher_name
    FROM books b
    LEFT JOIN categories c
        ON c.id = b.category_id
    LEFT JOIN publishers p
        ON p.id = b.publisher_id
    WHERE b.id = ?
      AND b.status = 'published'
    LIMIT 1
");

$stmt->execute([$bookId]);

$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    echo '
        <div class="container">
            <p>Không tìm thấy sách hoặc sách chưa được xuất bản.</p>
            <a href="/home">Quay lại trang chủ</a>
        </div>
    ';
    return;
}


$isFavorite = false;

if ($userId > 0) {
    $favoriteStmt = $db->prepare("
        SELECT id
        FROM favorites
        WHERE user_id = ? AND book_id = ?
        LIMIT 1
    ");

    $favoriteStmt->execute([$userId, $bookId]);

    $isFavorite = (bool)$favoriteStmt->fetchColumn();
}

$isOwned = false;
if ($userId > 0) {
    try {
        $ownedStmt = $db->prepare("SELECT id FROM user_books WHERE user_id = ? AND book_id = ? LIMIT 1");
        $ownedStmt->execute([$userId, $bookId]);
        $isOwned = (bool)$ownedStmt->fetchColumn();
    } catch (Throwable $t) {}
}

$digitalPrice = (float)($book['digital_price'] ?? 0);
$salePrice = $book['sale_price'] !== null
    ? (float)$book['sale_price']
    : 0;

$listPrice = (float)($book['list_price'] ?? 0);

$currentPrice = $salePrice > 0
    ? $salePrice
    : $digitalPrice;

$isDiscounted = $salePrice > 0 && $listPrice > $salePrice;


$relatedStmt = $db->prepare("
    SELECT
        b.*,
        c.name AS category_name
    FROM books b
    LEFT JOIN categories c
        ON c.id = b.category_id
    WHERE b.category_id = ?
      AND b.id <> ?
      AND b.status = 'published'
    ORDER BY b.total_readers DESC, b.id DESC
    LIMIT 4
");

$relatedStmt->execute([
    (int)$book['category_id'],
    $bookId
]);

$relatedBooks = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);

$coverPath = trim((string)($book['cover_path'] ?? ''));

$coverColor = trim((string)($book['cover_color'] ?? 'blue'));

$rating = (float)($book['avg_rating'] ?? 0);

$totalReviews = (int)($book['total_reviews'] ?? 0);

$totalReaders = (int)($book['total_readers'] ?? 0);

$publishYear = (int)($book['publish_year'] ?? 0);

$pages = (int)($book['pages'] ?? 0);

$fileFormat = strtoupper(trim((string)($book['file_format'] ?? 'EPUB')));
?>

<div class="page-wrapper home-container" style="padding-top: 32px; padding-bottom: 64px;">

    <a href="/home" class="back-link" style="display:inline-flex; align-items:center; gap:6px; margin-bottom:24px; color:#6B7280; text-decoration:none; font-size:14px; font-weight:600;">
        <span>&#10094;</span> Quay lại
    </a>

    <div class="detail-grid">

        <!-- Cột bìa sách -->
        <div class="detail-cover-col">
            <div class="detail-cover-box <?= e($coverColor) ?>" style="position:relative; overflow:hidden; background:var(--readly-primary-dark, #075A64);">
                <?php if ($coverPath !== ''): ?>
                    <img
                        src="<?= e($coverPath) ?>"
                        alt="<?= e((string)$book['title']) ?>"
                        style="width:100%; height:100%; object-fit:cover; position:absolute; inset:0; border-radius:16px;"
                    >
                <?php else: ?>
                    <div>
                        <h1><?= e((string)$book['title']) ?></h1>
                        <p><?= e((string)($book['author'] ?? '')) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="detail-actions">
                <?php if ($isOwned): ?>
                    <a href="/library?read=<?= $bookId ?>" class="btn-buy" style="background:#10B981; color:#ffffff; text-decoration:none;">
                        ✓ Đã sở hữu — Đọc ngay
                    </a>
                <?php else: ?>
                    <form method="POST" style="width:100%;">
                        <input type="hidden" name="buy_book" value="1">
                        <button type="submit" class="btn-buy" style="width:100%;">
                            🛒 Mua ngay — <?= formatPrice($currentPrice) ?>
                        </button>
                    </form>
                <?php endif; ?>

                <a href="/library?read=<?= $bookId ?>" class="btn-read-trial">
                    📖 Đọc thử
                </a>

                <div class="btn-row">
                    <form method="POST" style="flex:1;">
                        <input type="hidden" name="toggle_favorite" value="1">
                        <button type="submit" class="btn-secondary" style="width:100%;">
                            <?= $isFavorite ? '♥ Đã thích' : '❤️ Yêu thích' ?>
                        </button>
                    </form>

                    <button type="button" class="btn-secondary" onclick="shareBook()">
                        🔗 Chia sẻ
                    </button>
                </div>
            </div>
        </div>

        <!-- Cột thông tin -->
        <div class="detail-info-col">

            <h1 class="detail-book-title"><?= e((string)$book['title']) ?></h1>
            <p class="detail-book-author">Tác giả: <strong><?= e((string)($book['author'] ?? 'Đang cập nhật')) ?></strong></p>

            <div class="rating-row">
                <span class="rating-star">★</span>
                <span class="rating-value"><?= number_format($rating, 1) ?></span>
                <span class="rating-count">(<?= $totalReviews ?> đánh giá)</span>
                <span class="rating-sep">•</span>
                <span class="rating-readers"><?= number_format($totalReaders) ?> độc giả</span>
            </div>

            <!-- Giá -->
            <div style="margin-bottom: 24px; display:flex; align-items:baseline; gap:12px;">
                <span style="font-size: 32px; font-weight: 800; color: var(--readly-primary, #087E8B);">
                    <?= formatPrice($currentPrice) ?>
                </span>
                <?php if ($isDiscounted): ?>
                    <span style="font-size: 18px; color: #9CA3AF; text-decoration: line-through;">
                        <?= formatPrice($listPrice) ?>
                    </span>
                <?php endif; ?>
            </div>

            <!-- Thông tin chi tiết -->
            <div class="detail-section">
                <h3 class="detail-section-title">Thông tin chi tiết</h3>
                <div class="info-table">
                    <div class="info-row">
                        <span class="info-label">Nhà xuất bản:</span>
                        <span class="info-value"><?= e((string)($book['publisher_name'] ?? 'Đang cập nhật')) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Năm xuất bản:</span>
                        <span class="info-value"><?= $publishYear > 0 ? $publishYear : 'Đang cập nhật' ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Số trang:</span>
                        <span class="info-value"><?= $pages > 0 ? number_format($pages) . ' trang' : 'Đang cập nhật' ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Định dạng:</span>
                        <span class="info-value"><?= e($fileFormat) ?></span>
                    </div>
                </div>
            </div>

            <!-- Giới thiệu -->
            <div class="detail-section">
                <h3 class="detail-section-title">Giới thiệu sách</h3>
                <p class="detail-description"><?= nl2br(e((string)($book['description'] ?? 'Chưa có mô tả cho sách này.'))) ?></p>
            </div>

            <!-- Đặc điểm nổi bật -->
            <div class="detail-section">
                <div class="features-box">
                    <h3 class="detail-section-title">Đặc điểm nổi bật</h3>
                    <ul class="features-list">
                        <li><span class="feature-check">✓</span><span>Đọc trên mọi thiết bị: điện thoại, máy tính bảng, máy tính</span></li>
                        <li><span class="feature-check">✓</span><span>Đánh dấu trang, ghi chú và tra cứu từ điển ngay trong sách</span></li>
                        <li><span class="feature-check">✓</span><span>Tải về để đọc offline, không cần kết nối internet</span></li>
                        <li><span class="feature-check">✓</span><span>Cập nhật và hỗ trợ miễn phí trọn đời</span></li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

    <!-- Sách liên quan -->
    <?php if (!empty($relatedBooks)): ?>
        <div class="related-section">
            <h3>Sách liên quan</h3>
            <div class="recommended-grid">
                <?php
                $books = $relatedBooks;
                include __DIR__ . '/BookCards.php';
                ?>
            </div>
        </div>
    <?php endif; ?>

</div>


<script>
function shareBook() {
    const url = window.location.href;

    if (navigator.clipboard) {
        navigator.clipboard.writeText(url)
            .then(() => {
                if (typeof showToast === 'function') {
                    showToast('Đã sao chép liên kết sách!', 'success');
                }
            })
            .catch(() => {
                if (typeof showToast === 'function') {
                    showToast(url, 'info');
                }
            });
    }
}
</script>

<?php if (!empty($_SESSION['buy_error'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof showToast === 'function') {
                showToast(<?= json_encode($_SESSION['buy_error']) ?>, 'error');
            }
            if (typeof openDepositModal === 'function') {
                setTimeout(openDepositModal, 600);
            }
        });
    </script>
    <?php unset($_SESSION['buy_error']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['buy_success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof showToast === 'function') {
                showToast(<?= json_encode($_SESSION['buy_success']) ?>, 'success');
            }
        });
    </script>
    <?php unset($_SESSION['buy_success']); ?>
<?php endif; ?>