<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. KẾT NỐI DATABASE
$host = 'db';
$dbname = 'ebook_store';
$username = 'ebook_user';
$password = 'ebook_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . htmlspecialchars($e->getMessage()));
}

$user_id = $_SESSION['user_id'] ?? 1;
$book_id = $_GET['book_id'] ?? 0;

if (!$book_id) {
    die("Không tìm thấy mã sách!");
}

// 2. LẤY THÔNG TIN SÁCH VÀ ĐƯỜNG DẪN FILE EPUB
$stmt = $pdo->prepare("
    SELECT b.id, b.title, b.file_path, COALESCE(ub.progress_percent, 0) AS progress_percent
    FROM books b
    LEFT JOIN user_books ub ON b.id = ub.book_id AND ub.user_id = :user_id
    WHERE b.id = :book_id
");
$stmt->execute([':user_id' => $user_id, ':book_id' => $book_id]);
$book = $stmt->fetch();

if (!$book || empty($book['file_path'])) {
    die("Sách không tồn tại hoặc chưa được cập nhật file EPUB!");
}

// 3. CẬP NHẬT TRẠNG THÁI "ĐANG ĐỌC" NẾU LÀ LẦN ĐẦU MỞ SÁCH
$updateStmt = $pdo->prepare("
    UPDATE user_books 
    SET reading_status = 'reading', updated_at = CURRENT_TIMESTAMP 
    WHERE user_id = :user_id AND book_id = :book_id AND reading_status = 'unread'
");
$updateStmt->execute([':user_id' => $user_id, ':book_id' => $book_id]);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đọc sách: <?= htmlspecialchars($book['title']) ?></title>
    
    <!-- Nhúng thư viện ePub.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.5/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/epubjs/dist/epub.min.js"></script>
    
    <style>
        body { margin: 0; padding: 0; font-family: system-ui, sans-serif; background: #f9fafb; display: flex; flex-direction: column; height: 100vh; }
        .toolbar { background: #fff; padding: 12px 24px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1); z-index: 10; }
        .toolbar a { text-decoration: none; color: #087E8B; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;}
        .book-title { font-weight: bold; color: #1f2937; margin: 0; font-size: 1.1rem; }
        .progress { font-size: 0.9rem; color: #6b7280; font-weight: 500; }
        
        #viewer { flex: 1; width: 100%; max-width: 900px; margin: 0 auto; background: #fff; position: relative; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        
        .navigation { display: flex; justify-content: center; gap: 16px; padding: 16px; background: #fff; border-top: 1px solid #e5e7eb; }
        .btn { padding: 10px 24px; background: #087E8B; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 0.95rem; transition: background 0.2s; }
        .btn:hover { background: #065f46; }
    </style>
</head>
<body>

    <div class="toolbar">
        <a href="main.php"><span>←</span> Quay lại Thư viện</a>
        <h1 class="book-title"><?= htmlspecialchars($book['title']) ?></h1>
        <div class="progress" id="progress-text">Tiến độ: <?= (int)$book['progress_percent'] ?>%</div>
    </div>

    <!-- Khung render nội dung sách -->
    <div id="viewer"></div>

    <div class="navigation">
        <button id="prev" class="btn">← Trang trước</button>
        <button id="next" class="btn">Trang sau →</button>
    </div>

    <script>
        // Lấy đường dẫn file từ Database
        const epubUrl = "<?= '/' . ltrim($book['file_path'], '/') ?>";
        const bookId = <?= (int)$book['id'] ?>;

        // Khởi tạo trình đọc
        const book = ePub(epubUrl);
        const rendition = book.renderTo("viewer", {
            width: "100%",
            height: "100%",
            spread: "none"
        });

        rendition.display();

        book.ready.then(() => {
            return book.locations.generate(1600);
        });

        function updateProgress() {
            const currentLocation = rendition.currentLocation();
            if (currentLocation && currentLocation.start) {
                const percentage = Math.round(book.locations.percentageFromCfi(currentLocation.start.cfi) * 100);
                const displayPercent = percentage > 0 ? percentage : 1;
                
                document.getElementById("progress-text").innerText = `Tiến độ: ${displayPercent}%`;

                // Gọi tới API update_progress.php ở thư mục gốc (api)
                fetch('/api/update_progress.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        book_id: bookId,
                        progress_percent: displayPercent
                    })
                }).catch(err => console.error("Lỗi đồng bộ:", err));
            }
        }

        document.getElementById("prev").addEventListener("click", () => {
            rendition.prev();
            updateProgress();
        });

        document.getElementById("next").addEventListener("click", () => {
            rendition.next();
            updateProgress();
        });

        document.addEventListener("keyup", (e) => {
            if (e.key === "ArrowLeft") { rendition.prev(); updateProgress(); }
            if (e.key === "ArrowRight") { rendition.next(); updateProgress(); }
        });
    </script>
</body>
</html>