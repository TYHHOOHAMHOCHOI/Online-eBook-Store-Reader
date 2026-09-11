<?php
require_once dirname(__DIR__) . '/bootstrap/app.php';

if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

header('Content-Type: text/html; charset=utf-8');

$pdo = db();
$catId = isset($_GET['category']) ? (int)$_GET['category'] : 0;

if ($catId > 0) {
    $stmt = $pdo->prepare("
        SELECT id, title, author, cover_path, cover_color, digital_price, sale_price, avg_rating, total_readers, total_sold
        FROM books
        WHERE status = 'published' AND category_id = ?
        ORDER BY total_sold DESC, id DESC
        LIMIT 8
    ");
    $stmt->execute([$catId]);
} else {
    $stmt = $pdo->query("
        SELECT id, title, author, cover_path, cover_color, digital_price, sale_price, avg_rating, total_readers, total_sold
        FROM books
        WHERE status = 'published'
        ORDER BY total_sold DESC, id DESC
        LIMIT 8
    ");
}

$books = $stmt->fetchAll();

if (empty($books)) {
    echo '<p style="grid-column: 1/-1; text-align: center; color: #6B7280; padding: 40px 0;">Chưa có sách thuộc thể loại này.</p>';
    exit;
}

include dirname(__DIR__) . '/views/home/component/BookCards.php';
