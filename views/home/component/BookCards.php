<?php


if (!isset($books) || !is_array($books)) {
    $stmt = db()->query("
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
        ORDER BY created_at DESC
        LIMIT 4
    ");

    $books = $stmt->fetchAll();
}

foreach ($books as $book):

    $bookId = (int) ($book['id'] ?? 0);

    $title = $book['title'] ?? 'Chưa có tên';

    $author = $book['author'] ?? 'Chưa có tác giả';

    $rating = (float) ($book['avg_rating'] ?? 0);

    $readers = (int) ($book['total_readers'] ?? 0);


    $salePrice = $book['sale_price'] ?? null;

    $digitalPrice = $book['digital_price'] ?? 0;

    if ($salePrice !== null && (float) $salePrice > 0) {
        $price = (float) $salePrice;
    } else {
        $price = (float) $digitalPrice;
    }

    $coverPath = trim((string) ($book['cover_path'] ?? ''));

    $coverColor = trim((string) ($book['cover_color'] ?? 'cover-alchemist'));

    if ($coverColor === '') {
        $coverColor = 'cover-alchemist';
    }


    $badge = '';

    $badgeClass = '';

    if ($book['total_sold'] >= 100) {
        $badge = 'Bán chạy';
        $badgeClass = 'badge-red';
    } elseif ($rating >= 4.8) {
        $badge = 'Yêu thích';
        $badgeClass = 'badge-pink';
    } elseif ($book['total_readers'] >= 1000) {
        $badge = 'Top 10';
        $badgeClass = 'badge-teal';
    }
?>

<a
    href="/home?view=book-detail&book=<?= $bookId ?>"
    class="book-card favorite-card"
    style="display:block;text-decoration:none;color:inherit;"
>
    <div class="book-cover favorite-cover <?= e($coverColor) ?>">

        <?php if ($coverPath !== ''): ?>

            <img
                src="<?= e($coverPath) ?>"
                alt="<?= e($title) ?>"
                style="
                    width:100%;
                    height:100%;
                    object-fit:cover;
                    position:absolute;
                    inset:0;
                    z-index:1;
                "
            >

        <?php endif; ?>


        <div
            class="book-cover-content"
            style="position:relative;z-index:2;"
        >

            <div class="favorite-cover-title">
                <?= e($title) ?>
            </div>

            <div class="favorite-cover-author">
                <?= e($author) ?>
            </div>

        </div>


        <?php if ($badge !== ''): ?>

            <div class="book-badge <?= e($badgeClass) ?>">

                <?= e($badge) ?>

            </div>

        <?php endif; ?>

    </div>
    <div class="book-card-content">

        <h3>
            <?= e($title) ?>
        </h3>


        <p class="book-author">
            <?= e($author) ?>
        </p>


        <!-- Rating -->

        <div class="book-rating-row">

            <div class="book-rating">

                <svg
                    class="star-icon"
                    viewBox="0 0 20 20"
                    aria-hidden="true"
                >

                    <path
                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                    ></path>

                </svg>


                <span style="font-weight:600;">
                    <?= number_format($rating, 1) ?>
                </span>

            </div>


            <span class="book-readers">

                • <?= number_format($readers) ?> đã đọc

            </span>

        </div>


        <!-- Price -->

        <div class="book-price">

            <?= number_format($price, 0, ',', '.') ?>đ

        </div>

    </div>

</a>

<?php endforeach; ?>