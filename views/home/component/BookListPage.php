<?php


$pdo = db();


$categoryId = isset($_GET['category'])
    ? (int) $_GET['category']
    : 0;

$priceFilter = $_GET['price'] ?? '';

$ratingFilter = isset($_GET['rating'])
    ? (float) $_GET['rating']
    : 0;

$sort = $_GET['sort'] ?? 'best';

$page = isset($_GET['page'])
    ? max(1, (int) $_GET['page'])
    : 1;

$perPage = 8;



$categoryStmt = $pdo->query("
    SELECT
        id,
        name
    FROM categories
    ORDER BY sort_order ASC, id ASC
");

$categories = $categoryStmt->fetchAll();



$where = [
    "b.status = 'published'"
];

$params = [];



if ($categoryId > 0) {

    $where[] = "b.category_id = :category_id";

    $params['category_id'] = $categoryId;
}



if ($ratingFilter > 0) {

    $where[] = "b.avg_rating >= :rating";

    $params['rating'] = $ratingFilter;
}



switch ($priceFilter) {

    case 'under_50':

        $where[] = "
            COALESCE(NULLIF(b.sale_price, 0), b.digital_price) < 50000
        ";

        break;


    case '50_100':

        $where[] = "
            COALESCE(NULLIF(b.sale_price, 0), b.digital_price)
            BETWEEN 50000 AND 100000
        ";

        break;


    case '100_150':

        $where[] = "
            COALESCE(NULLIF(b.sale_price, 0), b.digital_price)
            BETWEEN 100000 AND 150000
        ";

        break;


    case 'over_150':

        $where[] = "
            COALESCE(NULLIF(b.sale_price, 0), b.digital_price) > 150000
        ";

        break;
}



$orderBy = match ($sort) {

    'rating' => 'b.avg_rating DESC, b.total_reviews DESC',

    'newest' => 'b.created_at DESC',

    'price_asc' => '
        COALESCE(NULLIF(b.sale_price, 0), b.digital_price) ASC
    ',

    'price_desc' => '
        COALESCE(NULLIF(b.sale_price, 0), b.digital_price) DESC
    ',

    default => 'b.total_sold DESC, b.total_readers DESC'

};



$whereSql = implode(' AND ', $where);

$countSql = "
    SELECT COUNT(*)
    FROM books b
    WHERE {$whereSql}
";

$countStmt = $pdo->prepare($countSql);

foreach ($params as $key => $value) {
    $countStmt->bindValue(
        ':' . $key,
        $value
    );
}

$countStmt->execute();

$totalBooks = (int) $countStmt->fetchColumn();

$totalPages = max(
    1,
    (int) ceil($totalBooks / $perPage)
);

$page = min(
    $page,
    $totalPages
);

$offset = ($page - 1) * $perPage;



$sql = "
    SELECT
        b.id,
        b.title,
        b.author,
        b.description,
        b.cover_path,
        b.cover_color,
        b.list_price,
        b.digital_price,
        b.sale_price,
        b.avg_rating,
        b.total_reviews,
        b.total_readers,
        b.total_sold,
        b.category_id,
        c.name AS category_name

    FROM books b

    LEFT JOIN categories c
        ON c.id = b.category_id

    WHERE {$whereSql}

    ORDER BY {$orderBy}

    LIMIT :limit
    OFFSET :offset
";

$stmt = $pdo->prepare($sql);

foreach ($params as $key => $value) {

    $stmt->bindValue(
        ':' . $key,
        $value
    );
}

$stmt->bindValue(
    ':limit',
    $perPage,
    PDO::PARAM_INT
);

$stmt->bindValue(
    ':offset',
    $offset,
    PDO::PARAM_INT
);

$stmt->execute();

$books = $stmt->fetchAll();



function bookListUrl(
    int $pageNumber,
    array $extra = []
): string {

    $query = array_merge(
        $_GET,
        [
            'view' => 'book-list',
            'page' => $pageNumber
        ],
        $extra
    );

    return '/home?' . http_build_query($query);
}

?>

<div class="page-wrapper">

    <a href="/home" class="back-link">

        <span>&#10094;</span>

        Quay lại

    </a>

    <div style="margin-bottom:32px;">

        <h1 class="page-title">
            Danh sách sách
        </h1>

        <p class="page-subtitle">

            Hiển thị
            <?= number_format($totalBooks) ?>
            cuốn sách

        </p>

    </div>


    <div class="list-layout">

        <aside class="filter-sidebar">

            <div class="filter-box">

                <h3 class="filter-box-title">
                    ⚙️ Bộ lọc
                </h3>

                <div>

                    <span class="filter-label">
                        Thể loại
                    </span>


                    <?php foreach ($categories as $category): ?>

                        <label class="filter-option">

                            <input
                                type="radio"
                                name="category_filter"
                                value="<?= (int) $category['id'] ?>"
                                <?= $categoryId === (int) $category['id']
                                    ? 'checked'
                                    : ''
                                ?>
                                onchange="
                                    window.location.href =
                                    '<?= e(
                                        bookListUrl(
                                            1,
                                            [
                                                'category' =>
                                                    (int) $category['id']
                                            ]
                                        )
                                    ) ?>';
                                "
                            >

                            <span>
                                <?= e($category['name']) ?>
                            </span>

                        </label>

                    <?php endforeach; ?>


                    <?php if ($categoryId > 0): ?>

                        <a
                            href="<?= e(
                                bookListUrl(
                                    1,
                                    [
                                        'category' => 0
                                    ]
                                )
                            ) ?>"
                            class="filter-clear"
                        >
                            Xóa lọc thể loại
                        </a>

                    <?php endif; ?>

                </div>

                <div class="filter-group">

                    <span class="filter-label">
                        Giá
                    </span>


                    <label class="filter-option">

                        <input
                            type="radio"
                            name="price_filter"
                            <?= $priceFilter === 'under_50'
                                ? 'checked'
                                : ''
                            ?>
                            onchange="
                                window.location.href =
                                '<?= e(
                                    bookListUrl(
                                        1,
                                        [
                                            'price' => 'under_50'
                                        ]
                                    )
                                ) ?>';
                            "
                        >

                        <span>
                            Dưới 50k
                        </span>

                    </label>


                    <label class="filter-option">

                        <input
                            type="radio"
                            name="price_filter"
                            <?= $priceFilter === '50_100'
                                ? 'checked'
                                : ''
                            ?>
                            onchange="
                                window.location.href =
                                '<?= e(
                                    bookListUrl(
                                        1,
                                        [
                                            'price' => '50_100'
                                        ]
                                    )
                                ) ?>';
                            "
                        >

                        <span>
                            50k - 100k
                        </span>

                    </label>


                    <label class="filter-option">

                        <input
                            type="radio"
                            name="price_filter"
                            <?= $priceFilter === '100_150'
                                ? 'checked'
                                : ''
                            ?>
                            onchange="
                                window.location.href =
                                '<?= e(
                                    bookListUrl(
                                        1,
                                        [
                                            'price' => '100_150'
                                        ]
                                    )
                                ) ?>';
                            "
                        >

                        <span>
                            100k - 150k
                        </span>

                    </label>


                    <label class="filter-option">

                        <input
                            type="radio"
                            name="price_filter"
                            <?= $priceFilter === 'over_150'
                                ? 'checked'
                                : ''
                            ?>
                            onchange="
                                window.location.href =
                                '<?= e(
                                    bookListUrl(
                                        1,
                                        [
                                            'price' => 'over_150'
                                        ]
                                    )
                                ) ?>';
                            "
                        >

                        <span>
                            Trên 150k
                        </span>

                    </label>

                </div>

                <div class="filter-group">

                    <span class="filter-label">
                        Đánh giá
                    </span>


                    <label class="filter-option">

                        <input
                            type="radio"
                            name="rating_filter"
                            <?= $ratingFilter == 5
                                ? 'checked'
                                : ''
                            ?>
                            onchange="
                                window.location.href =
                                '<?= e(
                                    bookListUrl(
                                        1,
                                        [
                                            'rating' => 5
                                        ]
                                    )
                                ) ?>';
                            "
                        >

                        <span>
                            5★
                        </span>

                    </label>


                    <label class="filter-option">

                        <input
                            type="radio"
                            name="rating_filter"
                            <?= $ratingFilter == 4
                                ? 'checked'
                                : ''
                            ?>
                            onchange="
                                window.location.href =
                                '<?= e(
                                    bookListUrl(
                                        1,
                                        [
                                            'rating' => 4
                                        ]
                                    )
                                ) ?>';
                            "
                        >

                        <span>
                            4★ trở lên
                        </span>

                    </label>


                    <label class="filter-option">

                        <input
                            type="radio"
                            name="rating_filter"
                            <?= $ratingFilter == 3
                                ? 'checked'
                                : ''
                            ?>
                            onchange="
                                window.location.href =
                                '<?= e(
                                    bookListUrl(
                                        1,
                                        [
                                            'rating' => 3
                                        ]
                                    )
                                ) ?>';
                            "
                        >

                        <span>
                            3★ trở lên
                        </span>

                    </label>

                </div>
                <?php if (
                    $categoryId > 0 ||
                    $priceFilter !== '' ||
                    $ratingFilter > 0
                ): ?>

                    <a
                        href="/home?view=book-list"
                        class="filter-reset"
                    >
                        Xóa tất cả bộ lọc
                    </a>

                <?php endif; ?>

            </div>

        </aside>
        <div class="booklist-area">


            <!-- SORT -->

            <div
                style="
                    display:flex;
                    justify-content:flex-end;
                    margin-bottom:20px;
                "
            >

                <form method="GET" action="/home">

                    <input
                        type="hidden"
                        name="view"
                        value="book-list"
                    >

                    <?php if ($categoryId > 0): ?>

                        <input
                            type="hidden"
                            name="category"
                            value="<?= $categoryId ?>"
                        >

                    <?php endif; ?>


                    <?php if ($priceFilter !== ''): ?>

                        <input
                            type="hidden"
                            name="price"
                            value="<?= e($priceFilter) ?>"
                        >

                    <?php endif; ?>


                    <?php if ($ratingFilter > 0): ?>

                        <input
                            type="hidden"
                            name="rating"
                            value="<?= $ratingFilter ?>"
                        >

                    <?php endif; ?>


                    <select
                        name="sort"
                        onchange="this.form.submit()"
                        style="
                            padding:10px 14px;
                            border:1px solid #d1d5db;
                            border-radius:8px;
                            background:white;
                        "
                    >

                        <option
                            value="best"
                            <?= $sort === 'best'
                                ? 'selected'
                                : ''
                            ?>
                        >
                            Bán chạy nhất
                        </option>

                        <option
                            value="rating"
                            <?= $sort === 'rating'
                                ? 'selected'
                                : ''
                            ?>
                        >
                            Đánh giá cao
                        </option>

                        <option
                            value="newest"
                            <?= $sort === 'newest'
                                ? 'selected'
                                : ''
                            ?>
                        >
                            Mới nhất
                        </option>

                        <option
                            value="price_asc"
                            <?= $sort === 'price_asc'
                                ? 'selected'
                                : ''
                            ?>
                        >
                            Giá thấp → cao
                        </option>

                        <option
                            value="price_desc"
                            <?= $sort === 'price_desc'
                                ? 'selected'
                                : ''
                            ?>
                        >
                            Giá cao → thấp
                        </option>

                    </select>

                </form>

            </div>

            <div class="booklist-grid">

                <?php if (!$books): ?>

                    <div
                        style="
                            grid-column:1/-1;
                            text-align:center;
                            padding:60px 20px;
                        "
                    >

                        <h3>
                            Không tìm thấy sách
                        </h3>

                        <p>
                            Thử thay đổi bộ lọc của bạn.
                        </p>

                        <a
                            href="/home?view=book-list"
                            class="primary-button"
                            style="
                                display:inline-block;
                                margin-top:15px;
                                text-decoration:none;
                            "
                        >
                            Xem tất cả sách
                        </a>

                    </div>

                <?php endif; ?>


                <?php foreach ($books as $book): ?>

                    <?php

                    $bookId = (int) $book['id'];

                    $price = $book['sale_price'];

                    if (
                        $price === null ||
                        (float) $price <= 0
                    ) {
                        $price = $book['digital_price'];
                    }

                    $coverColor =
                        $book['cover_color']
                        ?: 'cover-alchemist';

                    ?>


                    <a
                        href="/home?view=book-detail&book=<?= $bookId ?>"
                        class="booklist-card"
                    >

                       

                        <div
                            class="booklist-cover <?= e($coverColor) ?>"
                        >

                            <?php if (!empty($book['cover_path'])): ?>

                                <img
                                    src="<?= e(
                                        $book['cover_path']
                                    ) ?>"
                                    alt="<?= e(
                                        $book['title']
                                    ) ?>"
                                    style="
                                        position:absolute;
                                        inset:0;
                                        width:100%;
                                        height:100%;
                                        object-fit:cover;
                                    "
                                >

                            <?php endif; ?>


                            <div
                                style="
                                    position:relative;
                                    z-index:2;
                                "
                            >

                                <h2>
                                    <?= e($book['title']) ?>
                                </h2>

                                <p>
                                    <?= e($book['author']) ?>
                                </p>

                            </div>

                        </div>


                        

                        <div class="booklist-card-body">

                            <h3>
                                <?= e($book['title']) ?>
                            </h3>


                            <p class="card-author">
                                <?= e($book['author']) ?>
                            </p>


                            <?php if (!empty($book['category_name'])): ?>

                                <div
                                    style="
                                        font-size:12px;
                                        color:#64748b;
                                        margin:6px 0;
                                    "
                                >
                                    <?= e(
                                        $book['category_name']
                                    ) ?>
                                </div>

                            <?php endif; ?>


                            <div class="card-price-row">

                                <div>

                                    <span class="card-price">

                                        <?= number_format(
                                            (float) $price,
                                            0,
                                            ',',
                                            '.'
                                        ) ?>đ

                                    </span>


                                    <?php if (
                                        $book['sale_price'] !== null &&
                                        (float) $book['sale_price'] > 0 &&
                                        (float) $book['list_price'] >
                                        (float) $book['sale_price']
                                    ): ?>

                                        <span
                                            class="card-original-price"
                                        >
                                            <?= number_format(
                                                (float) $book['list_price'],
                                                0,
                                                ',',
                                                '.'
                                            ) ?>đ
                                        </span>

                                    <?php endif; ?>

                                </div>


                                <span class="card-rating">

                                    ⭐
                                    <?= number_format(
                                        (float) $book['avg_rating'],
                                        1
                                    ) ?>

                                </span>

                            </div>


                            <div class="card-sold">

                                <?= number_format(
                                    (int) $book['total_sold']
                                ) ?>

                                đã bán

                            </div>

                        </div>

                    </a>

                <?php endforeach; ?>

            </div>
            <?php if ($totalPages > 1): ?>

                <div class="pagination-bar">

                    <?php if ($page > 1): ?>

                        <a
                            class="pag-btn"
                            href="<?= e(
                                bookListUrl($page - 1)
                            ) ?>"
                        >
                            Trước
                        </a>

                    <?php else: ?>

                        <span class="pag-btn disabled">
                            Trước
                        </span>

                    <?php endif; ?>


                    <?php for (
                        $i = 1;
                        $i <= $totalPages;
                        $i++
                    ): ?>

                        <a
                            class="pag-btn <?= $i === $page
                                ? 'active'
                                : ''
                            ?>"
                            href="<?= e(
                                bookListUrl($i)
                            ) ?>"
                        >
                            <?= $i ?>
                        </a>

                    <?php endfor; ?>


                    <?php if ($page < $totalPages): ?>

                        <a
                            class="pag-btn"
                            href="<?= e(
                                bookListUrl($page + 1)
                            ) ?>"
                        >
                            Sau
                        </a>

                    <?php else: ?>

                        <span class="pag-btn disabled">
                            Sau
                        </span>

                    <?php endif; ?>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>