<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. KẾT NỐI DATABASE (PDO)
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

$current_user_id = $_SESSION['user_id'] ?? 1;

// 2. LẤY BỘ LỌC VÀ SẮP XẾP TỪ URL
$filter = $_GET['filter'] ?? 'all';
$sort = $_GET['sort'] ?? 'recent'; // recent | title | progress

// 3. TRUY VẤN DANH SÁCH SÁCH TRONG THƯ VIỆN
$sql = "
    SELECT 
        b.id, b.title, b.author, b.cover_image,
        COALESCE(ub.progress_percent, 0) AS progress,
        COALESCE(ub.reading_status, 'unread') AS reading_status,
        ub.updated_at, ub.acquired_at
    FROM user_books ub
    INNER JOIN books b ON ub.book_id = b.id
    WHERE ub.user_id = :user_id
";

if (in_array($filter, ['reading', 'unread', 'completed'])) {
    $sql .= " AND ub.reading_status = :filter_status";
}

// Xử lý Sắp xếp
switch ($sort) {
    case 'title':
        $sql .= " ORDER BY b.title ASC";
        break;
    case 'progress':
        $sql .= " ORDER BY ub.progress_percent DESC";
        break;
    case 'recent':
    default:
        $sql .= " ORDER BY ub.updated_at DESC, ub.acquired_at DESC";
        break;
}

$stmtBooks = $pdo->prepare($sql);
$params = [':user_id' => $current_user_id];
if (in_array($filter, ['reading', 'unread', 'completed'])) {
    $params[':filter_status'] = $filter;
}
$stmtBooks->execute($params);
$books = $stmtBooks->fetchAll();

// 4. LẤY DỮ LIỆU HOẠT ĐỘNG GẦN ĐÂY (3 cuốn đọc gần nhất)
$stmtRecent = $pdo->prepare("
    SELECT b.id, b.title, b.author, b.cover_image, ub.progress_percent, ub.updated_at
    FROM user_books ub
    INNER JOIN books b ON ub.book_id = b.id
    WHERE ub.user_id = ? AND ub.reading_status = 'reading'
    ORDER BY ub.updated_at DESC LIMIT 3
");
$stmtRecent->execute([$current_user_id]);
$recentBooks = $stmtRecent->fetchAll();

// 5. THỐNG KÊ MỤC TIÊU ĐỌC SÁCH HÔM NAY (Ví dụ: 13/20 phút = 65%)
$targetMinutes = 20;
$readMinutesToday = 13; // Lấy từ DB hoặc tính toán thời gian session
$goalPercent = min(100, round(($readMinutesToday / $targetMinutes) * 100));
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thư viện của bạn - Readly</title>
    <style>
        :root { --primary-color: #087E8B; --bg-light: #f9fafb; }
        body { font-family: system-ui, -apple-system, sans-serif; background: var(--bg-light); margin: 0; padding: 20px; color: #1f2937; }
        .container { max-width: 1100px; margin: 0 auto; }
        
        /* Banner Thư viện & Widget Mục tiêu */
        .library-header { display: flex; justify-content: space-between; align-items: center; background: #fff; padding: 24px; border-radius: 16px; border: 1px solid #e5e7eb; margin-bottom: 24px; }
        .goal-card { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 16px 24px; display: flex; align-items: center; gap: 20px; }
        .goal-circle { width: 60px; height: 60px; border-radius: 50%; background: conic-gradient(#10b981 <?= $goalPercent ?>%, #e5e7eb 0); display: flex; align-items: center; justify-content: center; position: relative; }
        .goal-circle-inner { width: 46px; height: 46px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.85rem; color: #047857; }

        /* Thanh điều hướng Lọc & Sắp xếp */
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
        .tabs { display: flex; gap: 8px; }
        .tab-btn { text-decoration: none; padding: 8px 16px; border-radius: 20px; color: #4b5563; font-weight: 500; background: #fff; border: 1px solid #e5e7eb; font-size: 0.9rem; }
        .tab-btn.active { background: var(--primary-color); color: #fff; border-color: var(--primary-color); }
        .sort-select { padding: 8px 12px; border-radius: 8px; border: 1px solid #d1d5db; background: #fff; font-size: 0.9rem; }

        /* Lưới Sách & Card Sách */
        .section-title { font-size: 1.2rem; font-weight: 700; margin: 24px 0 12px 0; }
        .book-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
        .book-card { background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; overflow: hidden; display: flex; flex-direction: column; }
        .book-cover { width: 100%; height: 230px; object-fit: cover; background: #f3f4f6; }
        .book-body { padding: 14px; display: flex; flex-direction: column; flex: 1; }
        .book-title { font-size: 0.95rem; font-weight: 700; margin: 0 0 4px 0; line-clamp: 2; display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden; }
        .book-author { font-size: 0.8rem; color: #6b7280; margin-bottom: 10px; }
        .progress-bar-bg { width: 100%; height: 5px; background: #e5e7eb; border-radius: 3px; overflow: hidden; margin-top: auto; }
        .progress-bar-fill { height: 100%; background: var(--primary-color); }
        .btn-read { display: block; text-align: center; background: var(--primary-color); color: #fff; text-decoration: none; padding: 8px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        
        <!-- HEADER & WIDGET MỤC TIÊU HÔM NAY -->
        <div class="library-header">
            <div>
                <h1 style="margin: 0 0 6px 0; font-size: 1.6rem;">Thư viện của bạn</h1>
                <p style="margin: 0; color: #6b7280; font-size: 0.95rem;">Chào mừng trở lại, hôm nay bạn muốn đọc gì?</p>
            </div>
            
            <div class="goal-card">
                <div>
                    <strong style="display: block; font-size: 0.9rem; color: #065f46;">Mục tiêu hôm nay</strong>
                    <span style="font-size: 0.8rem; color: #047857;">Bạn đã đọc <b><?= $readMinutesToday ?> phút</b> trong ngày hôm nay.</span>
                </div>
                <div class="goal-circle">
                    <div class="goal-circle-inner"><?= $goalPercent ?>%</div>
                </div>
            </div>
        </div>

        <!-- MỤC HOẠT ĐỘNG GẦN ĐÂY -->
        <?php if (!empty($recentBooks)): ?>
            <div class="section-title">⏱️ Hoạt động gần đây</div>
            <div class="book-grid" style="margin-bottom: 30px;">
                <?php foreach ($recentBooks as $rb): ?>
                    <div class="book-card" style="border-left: 4px solid var(--primary-color);">
                        <div class="book-body">
                            <h4 class="book-title"><?= htmlspecialchars($rb['title']) ?></h4>
                            <p class="book-author"><?= htmlspecialchars($rb['author']) ?></p>
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill" style="width: <?= (int)$rb['progress_percent'] ?>%;"></div>
                            </div>
                            <a href="?read=<?= $rb['id'] ?>" class="btn-read">Đọc tiếp (<?= (int)$rb['progress_percent'] ?>%)</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- THANH CÔNG CỤ LỌC & SẮP XẾP -->
        <div class="toolbar">
            <div class="tabs">
                <a href="?filter=all&sort=<?= $sort ?>" class="tab-btn <?= $filter === 'all' ? 'active' : '' ?>">Tất cả</a>
                <a href="?filter=reading&sort=<?= $sort ?>" class="tab-btn <?= $filter === 'reading' ? 'active' : '' ?>">Đang đọc</a>
                <a href="?filter=unread&sort=<?= $sort ?>" class="tab-btn <?= $filter === 'unread' ? 'active' : '' ?>">Chưa đọc</a>
                <a href="?filter=completed&sort=<?= $sort ?>" class="tab-btn <?= $filter === 'completed' ? 'active' : '' ?>">Đã hoàn thành</a>
            </div>

            <div>
                <label for="sort" style="font-size: 0.85rem; color: #6b7280; font-weight: 500;">Sắp xếp theo:</label>
                <select id="sort" class="sort-select" onchange="location = this.value;">
                    <option value="?filter=<?= $filter ?>&sort=recent" <?= $sort === 'recent' ? 'selected' : '' ?>>Gần đây nhất</option>
                    <option value="?filter=<?= $filter ?>&sort=title" <?= $sort === 'title' ? 'selected' : '' ?>>Tên sách (A-Z)</option>
                    <option value="?filter=<?= $filter ?>&sort=progress" <?= $sort === 'progress' ? 'selected' : '' ?>>Tiến độ đọc</option>
                </select>
            </div>
        </div>

        <!-- DANH SÁCH SÁCH -->
        <div class="book-grid">
            <?php if (empty($books)): ?>
                <p style="grid-column: 1/-1; color: #6b7280; text-align: center; padding: 40px;">Không tìm thấy cuốn sách nào trong mục này.</p>
            <?php else: ?>
                <?php foreach ($books as $b): ?>
                    <div class="book-card">
                        <img src="<?= htmlspecialchars(!empty($b['cover_image']) ? '/' . ltrim($b['cover_image'], '/') : 'https://via.placeholder.com/200x300') ?>" class="book-cover" alt="<?= htmlspecialchars($b['title']) ?>">
                        <div class="book-body">
                            <h3 class="book-title"><?= htmlspecialchars($b['title']) ?></h3>
                            <p class="book-author"><?= htmlspecialchars($b['author']) ?></p>
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill" style="width: <?= (int)$b['progress'] ?>%;"></div>
                            </div>
                            <a href="?read=<?= $b['id'] ?>" class="btn-read"><?= $b['progress'] > 0 ? 'Đọc tiếp' : 'Bắt đầu đọc' ?></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>