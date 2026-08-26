<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==========================================
// 1. KẾT NỐI CƠ SỞ DỮ LIỆU (DATABASE PDO)
// ==========================================
$host = 'db';
$dbname = 'ebook_store';
$username = 'ebook_user';
$password = 'ebook_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("<div style='color:red; padding:15px; background:#fee2e2; border-radius:5px;'>
            <strong>Lỗi kết nối CSDL:</strong> " . $e->getMessage() . "<br>
            <em>Vui lòng kiểm tra lại Docker hoặc thông tin kết nối Adminer.</em>
         </div>");
}

// Giả lập ID người dùng hiện tại (Sau này khi có Đăng nhập sẽ đổi thành $_SESSION['user_id'])
$current_user_id = $_SESSION['user_id'] ?? 1;
$message = "";
$messageType = "success";

// Xử lý Thêm sách có sẵn vào Thư viện cá nhân
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_library') {
    $book_id_to_add = (int)$_POST['book_id'];

    // Kiểm tra xem sách đã có trong thư viện người dùng chưa
    $checkStmt = $pdo->prepare("SELECT id FROM user_books WHERE user_id = ? AND book_id = ?");
    $checkStmt->execute([$current_user_id, $book_id_to_add]);

    if ($checkStmt->rowCount() === 0) {
        $insertStmt = $pdo->prepare("
            INSERT INTO user_books (user_id, book_id, reading_status, progress_percent, acquired_at) 
            VALUES (?, ?, 'unread', 0, NOW())
        ");
        if ($insertStmt->execute([$current_user_id, $book_id_to_add])) {
            $message = "Đã thêm sách vào thư viện thành công!";
            $messageType = "success";
        }
    } else {
        $message = "Sách này đã tồn tại trong thư viện của bạn rồi!";
        $messageType = "warning";
    }
}

// Lấy danh sách $books của user từ bảng `user_books`
$queryBooks = "
    SELECT 
        b.id, 
        b.title, 
        b.author, 
        COALESCE(ub.progress_percent, 0) AS progress,
        '#087E8B' AS color,
        ub.reading_status
    FROM user_books ub
    INNER JOIN books b ON ub.book_id = b.id
    WHERE ub.user_id = ?
    ORDER BY ub.acquired_at DESC
";
$stmtBooks = $pdo->prepare($queryBooks);
$stmtBooks->execute([$current_user_id]);
$books = $stmtBooks->fetchAll();

// Lấy danh sách $recentActivities từ bảng `highlights`
$recentActivities = [];
try {
    $queryActivities = "
        SELECT 
            h.id, 
            h.content AS quote, 
            b.title AS book, 
            COALESCE(h.page_number, 1) AS page, 
            '#FFCA3A' AS highlightColor
        FROM highlights h
        INNER JOIN books b ON h.book_id = b.id
        WHERE h.user_id = ?
        ORDER BY h.created_at DESC
        LIMIT 5
    ";
    $stmtAct = $pdo->prepare($queryActivities);
    $stmtAct->execute([$current_user_id]);
    $recentActivities = $stmtAct->fetchAll();
} catch (Exception $e) {
    $recentActivities = [];
}

// Xử lý mở trình đọc sách
if (isset($_GET['read'])) {
    $bookId = (int)$_GET['read'];
    
    $stmtReader = $pdo->prepare("
        SELECT b.*, COALESCE(ub.progress_percent, 0) AS progress 
        FROM books b 
        LEFT JOIN user_books ub ON b.id = ub.book_id AND ub.user_id = ?
        WHERE b.id = ?
    ");
    $stmtReader->execute([$current_user_id, $bookId]);
    $selectedBook = $stmtReader->fetch();

    if ($selectedBook) {
        include __DIR__ . '/component/BookReader.php';
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thư Viện - Readly</title>
    <link rel="stylesheet" href="/assets/css/library.css">
</head>
<body>
    <div class="library-container">
        
        <?php if (!empty($message)): ?>
            <div style="padding: 12px 20px; margin: 15px 0; border-radius: 8px; font-weight: 500; 
                background-color: <?= $messageType === 'success' ? '#d1fae5' : ($messageType === 'warning' ? '#fef3c7' : '#fee2e2') ?>; 
                color: <?= $messageType === 'success' ? '#065f46' : ($messageType === 'warning' ? '#92400e' : '#991b1b') ?>;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php include __DIR__ . '/component/LibraryHeader.php'; ?>
        <?php include __DIR__ . '/component/LibraryOverview.php'; ?>
        <?php include __DIR__ . '/component/LibraryBooks.php'; ?>
        <?php include __DIR__ . '/component/RecentActivities.php'; ?>
        <?php include __DIR__ . '/component/LibraryFooter.php'; ?>
    </div>
</body>
</html>