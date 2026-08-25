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



// THƯỜNG HỢP A: Người dùng chọn một cuốn sách sẵn có trên hệ thống để thêm vào Thư viện cá nhân
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_library') {
    $book_id_to_add = (int)$_POST['book_id'];

    // Kiểm tra xem sách đã có trong thư viện người dùng chưa
    $checkStmt = $pdo->prepare("SELECT id FROM user_books WHERE user_id = ? AND book_id = ?");
    $checkStmt->execute([$current_user_id, $book_id_to_add]);

    if ($checkStmt->rowCount() === 0) {
        // Thêm vào bảng user_books
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

// TRƯỜNG HỢP B: Tạo mới hẳn một cuốn sách vào CSDL rồi tự động lưu vào Thư viện (Dành cho Form nhập nhanh)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_and_add_book') {
    $title  = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $color  = $_POST['color'] ?? '#087E8B';

    if (!empty($title) && !empty($author)) {
        // 1. Chèn vào bảng `books`
        $stmtBook = $pdo->prepare("INSERT INTO books (title, author, created_at) VALUES (?, ?, NOW())");
        $stmtBook->execute([$title, $author]);
        $new_book_id = $pdo->lastInsertId();

        // 2. Chèn tiếp vào bảng `user_books` cho user hiện tại
        $stmtUserBook = $pdo->prepare("
            INSERT INTO user_books (user_id, book_id, reading_status, progress_percent, acquired_at) 
            VALUES (?, ?, 'unread', 0, NOW())
        ");
        $stmtUserBook->execute([$current_user_id, $new_book_id]);

        $message = "Đã tạo sách mới và lưu vào Thư viện cá nhân!";
        $messageType = "success";
    } else {
        $message = "Vui lòng nhập đầy đủ tên sách và tác giả!";
        $messageType = "error";
    }
}



// Lấy danh sách $books của user từ bảng `user_books` kết hợp với bảng `books`
$queryBooks = "
    SELECT 
        b.id, 
        b.title, 
        b.author, 
        COALESCE(ub.progress_percent, 0) AS progress,
        '#087E8B' AS color, -- Màu mặc định nếu bảng books chưa có cột color
        ub.reading_status
    FROM user_books ub
    INNER JOIN books b ON ub.book_id = b.id
    WHERE ub.user_id = ?
    ORDER BY ub.acquired_at DESC
";
$stmtBooks = $pdo->prepare($queryBooks);
$stmtBooks->execute([$current_user_id]);
$books = $stmtBooks->fetchAll();

// Lấy danh sách $recentActivities từ bảng `highlights` kết hợp bảng `books`
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
    // Nếu bảng highlights chưa có dữ liệu, để mảng rỗng để không bị lỗi giao diện
    $recentActivities = [];
}

if (isset($_GET['read'])) {
    $bookId = (int)$_GET['read'];
    
    // Tìm thông tin sách trực tiếp từ Database
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
        
        <!-- Hiển thị thông báo phản hồi (thành công / lỗi) -->
        <?php if (!empty($message)): ?>
            <div style="padding: 12px 20px; margin: 15px 0; border-radius: 8px; font-weight: 500; 
                background-color: <?= $messageType === 'success' ? '#d1fae5' : ($messageType === 'warning' ? '#fef3c7' : '#fee2e2') ?>; 
                color: <?= $messageType === 'success' ? '#065f46' : ($messageType === 'warning' ? '#92400e' : '#991b1b') ?>;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Form nhanh để bạn test trực tiếp chức năng Thêm Sách vào Database -->
        <div style="background: #ffffff; padding: 20px; border-radius: 10px; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border: 1px solid #e5e7eb;">
            <h3 style="margin-top: 0; color: #102A43; font-size: 1.1rem; margin-bottom: 12px;">➕ Thêm nhanh sách mới vào Thư viện cá nhân</h3>
            <form action="" method="POST" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                <input type="hidden" name="action" value="create_and_add_book">
                <input type="text" name="title" placeholder="Tên sách..." required style="padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px; flex: 2; min-width: 180px;">
                <input type="text" name="author" placeholder="Tác giả..." required style="padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px; flex: 1.5; min-width: 150px;">
                <button type="submit" style="background: #087E8B; color: white; padding: 8px 18px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                    Lưu vào CSDL
                </button>
            </form>
        </div>

        <!-- Các Component giữ nguyên cấu trúc cũ -->
        <?php include __DIR__ . '/component/LibraryHeader.php'; ?>
        <?php include __DIR__ . '/component/LibraryOverview.php'; ?>
        <?php include __DIR__ . '/component/LibraryBooks.php'; ?>
        <?php include __DIR__ . '/component/RecentActivities.php'; ?>
        <?php include __DIR__ . '/component/LibraryFooter.php'; ?>
    </div>
</body>
</html>