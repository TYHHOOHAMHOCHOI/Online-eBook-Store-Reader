<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();

require dirname(__DIR__) . '/bootstrap/app.php';

$app = config('app');
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = rtrim($path, '/') ?: '/';

if ($path === '/') {
    // admin_dashboard.php sets $currentTitle itself via ?page= routing
    $pageTitle = 'Admin Dashboard';
    require BASE_PATH . '/views/dashboard/admin/admin_dashboard.php';
    exit;
}

if ($path === '/register') {
    $pageTitle = 'Đăng ký - Readly';
    $_GET['view'] = 'register';
    require BASE_PATH . '/views/home/home.php';
    exit;
}

if ($path === '/login' || $path === '/publisher-login') {
    $pageTitle = ($path === '/publisher-login') ? 'Đăng nhập Nhà phát hành - Readly' : 'Đăng nhập - Readly';
    $_GET['view'] = 'login';
    require BASE_PATH . '/views/home/home.php';
    exit;
}

if ($path === '/logout') {
    require BASE_PATH . '/views/auth/logout.php';
    exit;
}

if ($path === '/home') {
    $pageTitle = 'Readly Home';
    require BASE_PATH . '/views/home/home.php';
    exit;
}

if ($path === '/library') {
    $pageTitle = 'Thư viện - Readly';
    require BASE_PATH . '/views/library/main.php';
    exit;
}

if ($path === '/publisher-dashboard') {
    $pageTitle = 'Publisher Dashboard';
    require BASE_PATH . '/views/dashboard/publisher/publisher_dashboard.php';
    exit;
}

if ($path === '/api/get_books_by_category') {
    require BASE_PATH . '/api/get_books_by_category.php';
    exit;
}

if ($path === '/api/update_progress') {
    require BASE_PATH . '/api/update_progress.php';
    exit;
}

http_response_code(404);
$pageTitle = 'Không tìm thấy trang';
require BASE_PATH . '/views/404.php';