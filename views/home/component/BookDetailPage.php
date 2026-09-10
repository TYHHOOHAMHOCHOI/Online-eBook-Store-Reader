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

<div class="book-detail-page">

    <div class="container">


        <div class="breadcrumb">
            <a href="/home">Trang chủ</a>
            <span>/</span>

            <?php if (!empty($book['category_id'])): ?>
                <a href="/home?view=book-list&category=<?= (int)$book['category_id'] ?>">
                    <?= e((string)($book['category_name'] ?? 'Danh mục')) ?>
                </a>
                <span>/</span>
            <?php endif; ?>

            <span><?= e((string)$book['title']) ?></span>
        </div>


        

        <div class="book-detail">

          

            <div class="book-detail-cover">

                <?php if ($coverPath !== ''): ?>

                    <img
                        src="<?= e($coverPath) ?>"
                        alt="<?= e((string)$book['title']) ?>"
                    >

                <?php else: ?>

                    <div class="book-cover <?= e($coverColor) ?>">
                        <span><?= e((string)$book['title']) ?></span>
                    </div>

                <?php endif; ?>

            </div>


          

            <div class="book-detail-content">

                <div class="book-detail-category">
                    <?= e((string)($book['category_name'] ?? 'Sách điện tử')) ?>
                </div>

                <h1 class="book-detail-title">
                    <?= e((string)$book['title']) ?>
                </h1>

                <p class="book-detail-author">
                    Tác giả:
                    <strong><?= e((string)($book['author'] ?? 'Đang cập nhật')) ?></strong>
                </p>


                

                <div class="book-detail-rating">

                    <span class="rating-stars">
                        ★
                    </span>

                    <strong>
                        <?= number_format($rating, 1) ?>
                    </strong>

                    <span>
                        (<?= $totalReviews ?> đánh giá)
                    </span>

                    <span>
                        • <?= number_format($totalReaders) ?> lượt đọc
                    </span>

                </div>


               

                <div class="book-detail-price">

                    <strong>
                        <?= formatPrice($currentPrice) ?>
                    </strong>

                    <?php if ($isDiscounted): ?>

                        <span class="old-price">
                            <?= formatPrice($listPrice) ?>
                        </span>

                    <?php endif; ?>

                </div>


                

                <div class="book-detail-info">

                    <div>
                        <span>Nhà xuất bản</span>
                        <strong>
                            <?= e((string)($book['publisher_name'] ?? 'Đang cập nhật')) ?>
                        </strong>
                    </div>

                    <div>
                        <span>Năm xuất bản</span>
                        <strong>
                            <?= $publishYear > 0 ? $publishYear : 'Đang cập nhật' ?>
                        </strong>
                    </div>

                    <div>
                        <span>Số trang</span>
                        <strong>
                            <?= $pages > 0 ? number_format($pages) : 'Đang cập nhật' ?>
                        </strong>
                    </div>

                    <div>
                        <span>Định dạng</span>
                        <strong>
                            <?= e($fileFormat) ?>
                        </strong>
                    </div>

                </div>


            

                <div class="book-detail-actions">

                    <a
                        href="/library?read=<?= $bookId ?>"
                        class="btn btn-primary"
                    >
                        📖 Đọc thử
                    </a>


                    <form method="POST" style="display:inline;">

                        <input
                            type="hidden"
                            name="toggle_favorite"
                            value="1"
                        >

                        <button
                            type="submit"
                            class="btn btn-outline"
                        >
                            <?= $isFavorite ? '♥ Đã yêu thích' : '♡ Yêu thích' ?>
                        </button>

                    </form>


                    <button
                        type="button"
                        class="btn btn-outline"
                        onclick="shareBook()"
                    >
                        ↗ Chia sẻ
                    </button>

                </div>

            </div>

        </div>


        <!-- Mô tả -->

        <div class="book-detail-description">

            <h2>Giới thiệu sách</h2>

            <div>
                <?= nl2br(e((string)($book['description'] ?? 'Chưa có mô tả cho sách này.'))) ?>
            </div>

        </div>


    

        <?php if (!empty($relatedBooks)): ?>

            <section class="related-books">

                <div class="section-heading">

                    <h2>Sách liên quan</h2>

                    <a href="/home?view=book-list&category=<?= (int)$book['category_id'] ?>">
                        Xem tất cả
                    </a>

                </div>


                <div class="book-grid">

                    <?php foreach ($relatedBooks as $related): ?>

                        <?php
                        $relatedCover = trim(
                            (string)($related['cover_path'] ?? '')
                        );

                        $relatedColor = trim(
                            (string)($related['cover_color'] ?? 'blue')
                        );

                        $relatedPrice = $related['sale_price'] !== null
                            && (float)$related['sale_price'] > 0
                            ? (float)$related['sale_price']
                            : (float)($related['digital_price'] ?? 0);
                        ?>

                        <article class="book-card">

                            <a
                                href="/home?view=book-detail&book=<?= (int)$related['id'] ?>"
                                class="book-card-cover"
                            >

                                <?php if ($relatedCover !== ''): ?>

                                    <img
                                        src="<?= e($relatedCover) ?>"
                                        alt="<?= e((string)$related['title']) ?>"
                                    >

                                <?php else: ?>

                                    <div class="book-cover <?= e($relatedColor) ?>">
                                        <span>
                                            <?= e((string)$related['title']) ?>
                                        </span>
                                    </div>

                                <?php endif; ?>

                            </a>


                            <div class="book-card-content">

                                <div class="book-card-category">
                                    <?= e((string)($related['category_name'] ?? 'Sách')) ?>
                                </div>

                                <h3 class="book-card-title">

                                    <a href="/home?view=book-detail&book=<?= (int)$related['id'] ?>">
                                        <?= e((string)$related['title']) ?>
                                    </a>

                                </h3>

                                <p class="book-card-author">
                                    <?= e((string)($related['author'] ?? '')) ?>
                                </p>

                                <div class="book-card-bottom">

                                    <strong>
                                        <?= formatPrice($relatedPrice) ?>
                                    </strong>

                                    <span>
                                        ★ <?= number_format(
                                            (float)($related['avg_rating'] ?? 0),
                                            1
                                        ) ?>
                                    </span>

                                </div>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            </section>

        <?php endif; ?>

    </div>

</div>


<script>
function shareBook() {
    const url = window.location.href;

    if (navigator.clipboard) {
        navigator.clipboard.writeText(url)
            .then(() => {
                alert('Đã sao chép liên kết sách!');
            })
            .catch(() => {
                alert(url);
            });
    } else {
        alert(url);
    }
}
</script>