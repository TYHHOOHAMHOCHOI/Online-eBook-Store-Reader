<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/app.php';

$app = config('app');
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = rtrim($path, '/') ?: '/';

if ($path === '/') {
    $pageTitle = 'Admin Dashboard';
    require BASE_PATH . '/views/dashboard/admin/admin_dashboard.php';
    exit;
}

if ($path === '/publisher-dashboard') {
    $pageTitle = 'Publisher Dashboard';
    require BASE_PATH . '/views/dashboard/publisher/publisher_dashboard.php';
    exit;
}

http_response_code(404);
$pageTitle = 'Không tìm thấy trang';
require BASE_PATH . '/views/404.php';
