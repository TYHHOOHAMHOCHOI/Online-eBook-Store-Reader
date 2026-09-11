<?php

// Helper function — defined once, used by all included components
if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

$view = $_GET['view'] ?? 'home';

/*
|--------------------------------------------------------------------------
| LOGIN / REGISTER
|--------------------------------------------------------------------------
| Xử lý trước khi xuất HTML để header('Location: ...') hoạt động bình thường.
*/

if ($view === 'login') {
    include __DIR__ . '/component/Login.php';
}

if ($view === 'register') {
    include __DIR__ . '/component/Register.php';
}

?>
<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        <?= e($pageTitle ?? 'Readly - Online eBook Store & Reader') ?>
    </title>

    <link
        rel="stylesheet"
        href="/assets/css/home.css"
    >

</head>

<body class="home-page">

<header class="home-header">

    <div class="home-container header-inner">

        <!-- Logo -->

        <div class="header-logo">

            <a
                href="/home"
                style="display:flex;align-items:center;gap:8px;text-decoration:none;color:inherit;"
            >

                <div class="logo-mark">
                    <span>R</span>
                </div>

                <span class="logo-text">
                    readly
                </span>

            </a>

        </div>


        <nav class="header-nav">

            <a href="/home">
                Khám phá
            </a>

            <a href="/library">
                Thư viện
            </a>

            <a href="/home?view=register">
                Kích hoạt
            </a>

        </nav>


        <div class="header-actions">

            <form
                action="/home"
                method="GET"
                class="search-wrapper"
            >

                <input
                    type="hidden"
                    name="view"
                    value="search"
                >

                <input
                    type="text"
                    name="q"
                    placeholder="Tìm tên sách, tác giả..."
                    value="<?= e($_GET['q'] ?? '') ?>"
                >

            </form>


            <a
                href="/home?view=login"
                class="login-link"
            >
                Đăng nhập
            </a>


            <a
                href="/home?view=register"
                class="outline-button"
                style="text-decoration:none;padding:6px 14px;font-size:14px;"
            >
                Đăng ký
            </a>


            <a
                href="/home?view=book-list"
                class="primary-button"
                style="text-decoration:none;display:inline-block;"
            >
                Đọc ngay
            </a>

        </div>

    </div>

</header>

<main>

<?php

switch ($view) {

    case 'book-detail':

        include __DIR__ . '/component/BookDetailPage.php';

        break;


    case 'book-list':

        include __DIR__ . '/component/BookListPage.php';

        break;


    case 'search':

        include __DIR__ . '/component/SearchResultsPage.php';

        break;


    case 'login':
    case 'register':

        /*
         * Login/Register đã được xử lý ở phía trên.
         * Không include lại ở đây.
         */

        break;


    case 'home':

    default:

        include __DIR__ . '/component/HomePage.php';

        break;

}

?>

</main>


<?php

include __DIR__ . '/component/Footer.php';

?>

</body>

</html>