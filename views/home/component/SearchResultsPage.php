<?php

$pdo = db();



$keyword = trim($_GET['q'] ?? '');

$page = isset($_GET['page'])
    ? max(1, (int) $_GET['page'])
    : 1;

$perPage = 8;

$books = [];

$totalBooks = 0;
$totalPages = 1;



if ($keyword !== '') {



    $countSql = "
        SELECT COUNT(*)
        FROM books b

        LEFT JOIN categories c
            ON c.id = b.category_id

        WHERE b.status = 'published'

        AND (
            b.title LIKE :keyword1
            OR b.author LIKE :keyword2
            OR b.description LIKE :keyword3
            OR c.name LIKE :keyword4
        )
    ";

    $countStmt = $pdo->prepare($countSql);

    $searchKeyword = '%' . $keyword . '%';

    $countStmt->execute([
        'keyword1' => $searchKeyword,
        'keyword2' => $searchKeyword,
        'keyword3' => $searchKeyword,
        'keyword4' => $searchKeyword,
    ]);

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

        WHERE b.status = 'published'

        AND (
            b.title LIKE :keyword1
            OR b.author LIKE :keyword2
            OR b.description LIKE :keyword3
            OR c.name LIKE :keyword4
        )

        ORDER BY
            b.total_sold DESC,
            b.avg_rating DESC,
            b.created_at DESC

        LIMIT :limit
        OFFSET :offset
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(
        ':keyword1',
        $searchKeyword
    );

    $stmt->bindValue(
        ':keyword2',
        $searchKeyword
    );

    $stmt->bindValue(
        ':keyword3',
        $searchKeyword
    );

    $stmt->bindValue(
        ':keyword4',
        $searchKeyword
    );

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
}

function searchPageUrl(int $pageNumber): string
{
    return '/home?' . http_build_query([
        'view' => 'search',
        'q' => $_GET['q'] ?? '',
        'page' => $pageNumber,
    ]);
}

?>

<div class="page-wrapper">

    <a
        href="/home"
        class="back-link"
        style="text-decoration:none;"
    >

        <span>
            &#10094;
        </span>

        Quay lại

    </a>

    <div
        style="
            margin-bottom:30px;
        "
    >

        <?php if ($keyword !== ''): ?>

            <h1 class="page-title">

                Kết quả tìm kiếm

            </h1>


            <p class="page-subtitle">

                Kết quả cho:
                <strong>
                    "<?= e($keyword) ?>"
                </strong>

            </p>

        <?php else: ?>

            <h1 class="page-title">

                Tìm kiếm sách

            </h1>


            <p class="page-subtitle">

                Nhập tên sách hoặc tác giả để tìm kiếm.

            </p>

        <?php endif; ?>

    </div>

    <form
        method="GET"
        action="/home"
        class="search-results-form"
    >

        <input
            type="hidden"
            name="view"
            value="search"
        >


        <div
            style="
                display:flex;
                gap:10px;
                width:100%;
                max-width:700px;
            "
        >

            <input
                type="text"
                name="q"
                value="<?= e($keyword) ?>"
                placeholder="Tìm kiếm sách, tác giả..."
                autocomplete="off"
                style="
                    flex:1;
                    padding:13px 16px;
                    border:1px solid #d1d5db;
                    border-radius:10px;
                    outline:none;
                    font-size:15px;
                "
            >


            <button
                type="submit"
                class="primary-button"
                style="
                    border:none;
                    cursor:pointer;
                "
            >

                🔍 Tìm kiếm

            </button>

        </div>

    </form>
    <?php if ($keyword !== ''): ?>

        <?php if ($books): ?>

            <div
                style="
                    margin:25px 0 15px;
                    color:#64748b;
                "
            >

                Tìm thấy
                <strong>
                    <?= number_format($totalBooks) ?>
                </strong>
                kết quả.

            </div>


            <div class="booklist-grid">

                <?php foreach ($books as $book): ?>

                    <?php

                    $bookId = (int) $book['id'];

                    $price =
                        $book['sale_price'];

                    if (
                        $price === null ||
                        (float) $price <= 0
                    ) {
                        $price =
                            $book['digital_price'];
                    }

                    $coverColor =
                        $book['cover_color']
                        ?: 'cover-alchemist';

                    ?>

                    <a
                        href="/home?view=book-detail&book=<?= $bookId ?>"
                        class="booklist-card"
                        style="text-decoration:none;color:inherit;"
                    >


                     

                        <div
                            class="booklist-cover <?= e(
                                $coverColor
                            ) ?>"
                        >

                            <?php if (
                                !empty(
                                    $book['cover_path']
                                )
                            ): ?>

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
                                    <?= e(
                                        $book['title']
                                    ) ?>
                                </h2>

                                <p>
                                    <?= e(
                                        $book['author']
                                    ) ?>
                                </p>

                            </div>

                        </div>


                        

                        <div class="booklist-card-body">

                            <h3>
                                <?= e(
                                    $book['title']
                                ) ?>
                            </h3>


                            <p class="card-author">

                                <?= e(
                                    $book['author']
                                ) ?>

                            </p>


                            <?php if (
                                !empty(
                                    $book['category_name']
                                )
                            ): ?>

                                <div
                                    style="
                                        font-size:12px;
                                        color:#64748b;
                                        margin:6px 0;
                                    "
                                >

                                    <?= e(
                                        $book[
                                            'category_name'
                                        ]
                                    ) ?>

                                </div>

                            <?php endif; ?>


                            <div
                                class="card-price-row"
                            >

                                <span
                                    class="card-price"
                                >

                                    <?= number_format(
                                        (float) $price,
                                        0,
                                        ',',
                                        '.'
                                    ) ?>đ

                                </span>


                                <span
                                    class="card-rating"
                                >

                                    ⭐
                                    <?= number_format(
                                        (float)
                                        $book[
                                            'avg_rating'
                                        ],
                                        1
                                    ) ?>

                                </span>

                            </div>


                            <div class="card-sold">

                                <?= number_format(
                                    (int)
                                    $book[
                                        'total_sold'
                                    ]
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
                                searchPageUrl(
                                    $page - 1
                                )
                            ) ?>"
                        >
                            Trước
                        </a>

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
                                searchPageUrl($i)
                            ) ?>"
                        >
                            <?= $i ?>
                        </a>

                    <?php endfor; ?>


                    <?php if (
                        $page < $totalPages
                    ): ?>

                        <a
                            class="pag-btn"
                            href="<?= e(
                                searchPageUrl(
                                    $page + 1
                                )
                            ) ?>"
                        >
                            Sau
                        </a>

                    <?php endif; ?>

                </div>

            <?php endif; ?>


        <?php else: ?>

            <div
                style="
                    text-align:center;
                    padding:70px 20px;
                "
            >

                <div
                    style="
                        font-size:48px;
                        margin-bottom:15px;
                    "
                >
                    🔎
                </div>


                <h2>
                    Không tìm thấy sách
                </h2>


                <p
                    style="
                        color:#64748b;
                        margin-top:8px;
                    "
                >

                    Không có kết quả phù hợp với
                    <strong>
                        "<?= e($keyword) ?>"
                    </strong>

                </p>


                <a
                    href="/home?view=book-list"
                    class="primary-button"
                    style="
                        display:inline-block;
                        margin-top:20px;
                        text-decoration:none;
                    "
                >

                    Xem tất cả sách

                </a>

            </div>

        <?php endif; ?>

    <?php endif; ?>

</div>