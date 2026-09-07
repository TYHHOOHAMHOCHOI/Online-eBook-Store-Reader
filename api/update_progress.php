<?php
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'db';
$dbname = 'ebook_store';
$username = 'ebook_user';
$password = 'ebook_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $user_id = $_SESSION['user_id'] ?? 1;
    $book_id = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
    $progress = isset($_POST['progress']) ? (int)$_POST['progress'] : 0;

    if ($book_id > 0) {
        // Tự động xác định trạng thái đọc dựa trên tiến độ %
        $status = 'reading';
        if ($progress <= 0) {
            $status = 'unread';
        } elseif ($progress >= 100) {
            $status = 'completed';
        }

        // Cập nhật hoặc Thêm mới vào bảng user_books
        $sql = "
            INSERT INTO user_books (user_id, book_id, progress_percent, reading_status, acquired_at) 
            VALUES (:user_id, :book_id, :progress, :status, NOW())
            ON DUPLICATE KEY UPDATE 
                progress_percent = :progress_up,
                reading_status = :status_up
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':book_id' => $book_id,
            ':progress' => $progress,
            ':status' => $status,
            ':progress_up' => $progress,
            ':status_up' => $status
        ]);

        echo json_encode(['success' => true, 'message' => 'Cập nhật tiến độ thành công!']);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ!']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}