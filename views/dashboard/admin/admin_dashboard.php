<?php
/**
 * Main Admin Dashboard View Layout — with ?page= routing
 */
declare(strict_types=1);

$page = (string) ($_GET['page'] ?? 'dashboard');
$validPages = ['dashboard', 'users', 'books', 'categories', 'transactions'];
if (!in_array($page, $validPages, true)) {
    $page = 'dashboard';
}

/* ─────────────────────────────────────────────
 * Real data from database
 * ───────────────────────────────────────────── */
try {
    $pdo = db();

    /* ── Xử lý POST Admin Actions (Duyệt, Từ chối, Yêu cầu sửa sách) ── */
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_action'])) {
        $action = (string) $_POST['admin_action'];
        if ($action === 'update_book_status') {
            $bookId    = (int) ($_POST['book_id'] ?? 0);
            $bookTitle = (string) ($_POST['book_title'] ?? '');
            $status    = (string) ($_POST['status'] ?? 'pending');
            $validStatuses = ['published', 'rejected', 'draft', 'pending'];

            if (in_array($status, $validStatuses, true)) {
                if ($status === 'published') {
                    $stmt = $pdo->prepare("UPDATE books SET status = 'published', published_at = NOW() WHERE " . ($bookId > 0 ? "id = ?" : "title = ?"));
                    $stmt->execute([$bookId > 0 ? $bookId : $bookTitle]);
                } else {
                    $stmt = $pdo->prepare("UPDATE books SET status = ? WHERE " . ($bookId > 0 ? "id = ?" : "title = ?"));
                    $stmt->execute([$status, $bookId > 0 ? $bookId : $bookTitle]);
                }
            }

            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit;
            }
        }
    }

    /* ── Sách chờ duyệt (status = 'pending') ── */
    $pendingBooks = $pdo->query("
        SELECT
            b.id,
            b.title,
            b.author,
            b.submitted_at AS date,
            b.status,
            COALESCE(p.company_name, 'Chưa rõ') AS publisher,
            COALESCE(c.name, 'Chưa phân loại') AS category
        FROM books b
        LEFT JOIN publishers p ON b.publisher_id = p.id
        LEFT JOIN categories c ON b.category_id  = c.id
        WHERE b.status = 'pending'
        ORDER BY b.submitted_at DESC
        LIMIT 20
    ")->fetchAll();

    /* Chuẩn hoá ngày hiển thị */
    $pendingBooks = array_map(function (array $row): array {
        $row['date'] = $row['date']
            ? date('d/m/Y', strtotime($row['date']))
            : '—';
        $row['status'] = 'Chờ duyệt';
        return $row;
    }, $pendingBooks);

    /* ── Giao dịch gần nhất ── */
    $recentTransactions = $pdo->query("
        SELECT
            o.order_code  AS id,
            u.name        AS buyer,
            o.final_total AS amount,
            o.payment_method AS method,
            o.created_at  AS date,
            CASE o.status
                WHEN 'paid'      THEN 'Thành công'
                WHEN 'pending'   THEN 'Chờ xử lý'
                WHEN 'failed'    THEN 'Thất bại'
                WHEN 'cancelled' THEN 'Thất bại'
                ELSE o.status
            END AS status
        FROM orders o
        JOIN users  u ON o.user_id = u.id
        ORDER BY o.created_at DESC
        LIMIT 20
    ")->fetchAll();

    $recentTransactions = array_map(function (array $row): array {
        $row['date']   = date('d/m/Y', strtotime($row['date']));
        $row['amount'] = (int) $row['amount'];
        $row['method'] = $row['method'] ?: 'Chuyển khoản';
        return $row;
    }, $recentTransactions);

    /* ── Người dùng ── */
    $users = $pdo->query("
        SELECT
            u.id,
            u.name,
            u.email,
            CASE u.role
                WHEN 'publisher' THEN 'Publisher'
                WHEN 'admin'     THEN 'Admin'
                ELSE 'User'
            END AS role,
            CASE
                WHEN u.email_verified_at IS NOT NULL THEN 'Hoạt động'
                ELSE 'Chờ xác thực'
            END AS status,
            DATE_FORMAT(u.created_at, '%d/%m/%Y') AS joined,
            (SELECT COUNT(*) FROM orders o WHERE o.user_id = u.id) AS orders
        FROM users u
        ORDER BY u.created_at DESC
        LIMIT 50
    ")->fetchAll();

    /* ── Danh mục ── */
    $categories = $pdo->query("
        SELECT
            c.id,
            c.name,
            COALESCE(c.parent_id, 0) AS parent_id,
            DATE_FORMAT(c.created_at, '%d/%m/%Y') AS createdDate,
            'Thể loại' AS type,
            (SELECT COUNT(*) FROM books b WHERE b.category_id = c.id) AS books
        FROM categories c
        WHERE c.parent_id IS NULL
        ORDER BY c.sort_order ASC, c.name ASC
    ")->fetchAll();

    /* Ép kiểu int cho books count */
    $categories = array_map(function (array $row): array {
        $row['books'] = (int) $row['books'];
        return $row;
    }, $categories);

    /* ── Thống kê tổng quan ── */
    $totalUsers      = (int) $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $totalPublishers = (int) $pdo->query("SELECT COUNT(*) FROM publishers")->fetchColumn();
    $totalBooks      = (int) $pdo->query("SELECT COUNT(*) FROM books WHERE status = 'published'")->fetchColumn();
    $totalOrders     = (int) $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $totalCodes      = (int) $pdo->query("SELECT COUNT(*) FROM activation_codes WHERE status = 'used'")->fetchColumn();

    $stats = [
        ['label' => 'Người dùng',       'value' => number_format($totalUsers),      'growth' => '+12%', 'icon' => 'users',       'color' => '#087E8B'],
        ['label' => 'Nhà phát hành',    'value' => number_format($totalPublishers),  'growth' => '+8%',  'icon' => 'publisher',   'color' => '#087E8B'],
        ['label' => 'Sách',             'value' => number_format($totalBooks),       'growth' => '+18%', 'icon' => 'file-text',   'color' => '#087E8B'],
        ['label' => 'Đơn hàng',         'value' => number_format($totalOrders),      'growth' => '+23%', 'icon' => 'shopping-cart','color' => '#087E8B'],
        ['label' => 'Mã sách kích hoạt','value' => number_format($totalCodes),       'growth' => '+15%', 'icon' => 'credit-card', 'color' => '#087E8B'],
    ];

    /* ── Dữ liệu biểu đồ doanh thu hệ thống ── */
    $stmtTuannay = $pdo->query("
        SELECT
            DAYNAME(created_at) AS day_name,
            COALESCE(SUM(final_total), 0) AS total
        FROM orders
        WHERE status = 'paid'
          AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
        GROUP BY DATE(created_at), DAYNAME(created_at)
    ")->fetchAll();

    $weekValues = [0, 0, 0, 0, 0, 0, 0];
    $dayMap = ['Monday' => 0, 'Tuesday' => 1, 'Wednesday' => 2, 'Thursday' => 3, 'Friday' => 4, 'Saturday' => 5, 'Sunday' => 6];
    foreach ($stmtTuannay as $r) {
        if (isset($dayMap[$r['day_name']])) {
            $weekValues[$dayMap[$r['day_name']]] = (float)$r['total'];
        }
    }

    $adminPeriodData = [
        'Hôm nay' => [
            'labels' => ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00'],
            'values' => [0, 0, 0, 0, 0, 0, 0]
        ],
        'Tuần này' => [
            'labels' => ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'],
            'values' => $weekValues
        ],
        'Tháng này' => [
            'labels' => ['Tuần 1', 'Tuần 2', 'Tuần 3', 'Tuần 4'],
            'values' => [
                (float)$pdo->query("SELECT COALESCE(SUM(final_total), 0) FROM orders WHERE status = 'paid' AND DAY(created_at) BETWEEN 1 AND 7")->fetchColumn(),
                (float)$pdo->query("SELECT COALESCE(SUM(final_total), 0) FROM orders WHERE status = 'paid' AND DAY(created_at) BETWEEN 8 AND 14")->fetchColumn(),
                (float)$pdo->query("SELECT COALESCE(SUM(final_total), 0) FROM orders WHERE status = 'paid' AND DAY(created_at) BETWEEN 15 AND 21")->fetchColumn(),
                (float)$pdo->query("SELECT COALESCE(SUM(final_total), 0) FROM orders WHERE status = 'paid' AND DAY(created_at) >= 22")->fetchColumn()
            ]
        ],
        'Năm nay' => [
            'labels' => ['Q1', 'Q2', 'Q3', 'Q4'],
            'values' => [
                (float)$pdo->query("SELECT COALESCE(SUM(final_total), 0) FROM orders WHERE status = 'paid' AND QUARTER(created_at) = 1")->fetchColumn(),
                (float)$pdo->query("SELECT COALESCE(SUM(final_total), 0) FROM orders WHERE status = 'paid' AND QUARTER(created_at) = 2")->fetchColumn(),
                (float)$pdo->query("SELECT COALESCE(SUM(final_total), 0) FROM orders WHERE status = 'paid' AND QUARTER(created_at) = 3")->fetchColumn(),
                (float)$pdo->query("SELECT COALESCE(SUM(final_total), 0) FROM orders WHERE status = 'paid' AND QUARTER(created_at) = 4")->fetchColumn()
            ]
        ]
    ];

    /* ── Thống kê duyệt sách hôm nay ── */
    $approvedTodayCount = (int) $pdo->query("
        SELECT COUNT(*) FROM books 
        WHERE status = 'published' 
          AND (DATE(published_at) = CURDATE() OR DATE(updated_at) = CURDATE())
    ")->fetchColumn();

    $rejectedTodayCount = (int) $pdo->query("
        SELECT COUNT(*) FROM books 
        WHERE status = 'rejected' 
          AND DATE(updated_at) = CURDATE()
    ")->fetchColumn();

    /* ── Thống kê NXB & Tác giả ── */
    $totalAuthors   = (int) $pdo->query("SELECT COUNT(DISTINCT author) FROM books WHERE author IS NOT NULL AND author != ''")->fetchColumn();
    $pubAuthorTotal = $totalPublishers + $totalAuthors;

} catch (\Throwable $e) {
    /* Nếu DB chưa có dữ liệu / lỗi kết nối → trả về mảng rỗng */
    $approvedTodayCount = 0;
    $rejectedTodayCount = 0;
    $totalAuthors       = 0;
    $pubAuthorTotal     = 0;
    $pendingBooks       = [];
    $recentTransactions = [];
    $users              = [];
    $categories         = [];
    $stats = [
        ['label' => 'Người dùng',       'value' => '0', 'growth' => '0%', 'icon' => 'users',       'color' => '#087E8B'],
        ['label' => 'Nhà phát hành',    'value' => '0', 'growth' => '0%', 'icon' => 'publisher',   'color' => '#087E8B'],
        ['label' => 'Sách',             'value' => '0', 'growth' => '0%', 'icon' => 'file-text',   'color' => '#087E8B'],
        ['label' => 'Đơn hàng',         'value' => '0', 'growth' => '0%', 'icon' => 'shopping-cart','color' => '#087E8B'],
        ['label' => 'Mã sách kích hoạt','value' => '0', 'growth' => '0%', 'icon' => 'credit-card', 'color' => '#087E8B'],
    ];
    $adminPeriodData = [
        'Hôm nay'  => ['labels' => ['08:00'], 'values' => [0]],
        'Tuần này' => ['labels' => ['T2'], 'values' => [0]],
        'Tháng này'=> ['labels' => ['Tuần 1'], 'values' => [0]],
        'Năm nay'  => ['labels' => ['Q1'], 'values' => [0]],
    ];
}

$pageTitles = [
    'dashboard'    => 'Dashboard Hệ Thống',
    'users'        => 'Quản lý người dùng',
    'books'        => 'Duyệt sách',
    'categories'   => 'Quản lý danh mục',
    'transactions' => 'Giao dịch & Đối soát',
];
$currentTitle = $pageTitles[$page];
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($currentTitle . ' — readly Admin', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/css/admin-dashboard.css?v=<?= time() ?>">
</head>
<body class="admin-body">
<div class="admin-layout">

    <?php require __DIR__ . '/component/AdminSidebar.php'; ?>

    <main class="admin-main">
        <?php require __DIR__ . '/component/AdminHeader.php'; ?>

        <div class="admin-content">
            <?php if ($page === 'dashboard'): ?>
                <?php require __DIR__ . '/component/AdminStats.php'; ?>
                <?php require __DIR__ . '/component/RevenueChart.php'; ?>
                <?php require __DIR__ . '/component/AdminBottomSection.php'; ?>
            <?php elseif ($page === 'users'): ?>
                <?php require __DIR__ . '/component/UsersPage.php'; ?>
            <?php elseif ($page === 'books'): ?>
                <?php require __DIR__ . '/component/BooksPage.php'; ?>
            <?php elseif ($page === 'categories'): ?>
                <?php require __DIR__ . '/component/CategoriesPage.php'; ?>
            <?php elseif ($page === 'transactions'): ?>
                <?php require __DIR__ . '/component/TransactionsPage.php'; ?>
            <?php endif; ?>
        </div>
    </main>
</div>

<div id="adminToast" class="toast-notification" role="status" aria-live="polite"></div>
<script src="/assets/js/admin-dashboard.js?v=<?= time() ?>"></script>
</body>
</html>
