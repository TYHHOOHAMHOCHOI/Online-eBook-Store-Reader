<?php

declare(strict_types=1);

$page = (string) ($_GET['page'] ?? 'overview');
$pages = [
    'overview' => ['title' => 'Tổng quan', 'file' => null],
    'products' => ['title' => 'Sản phẩm sách', 'file' => 'ProductsPage.php'],
    'codes' => ['title' => 'Mã kích hoạt', 'file' => 'ActivationCodesPage.php'],
    'promotions' => ['title' => 'Khuyến mãi', 'file' => 'PromotionsPage.php'],
    'reports' => ['title' => 'Báo cáo', 'file' => 'ReportsPage.php'],
    'add-book' => ['title' => 'Thêm sách mới', 'file' => 'AddBookForm.php'],
];

if (!isset($pages[$page])) {
    $page = 'overview';
}

/* ─────────────────────────────────────────────
 * Real data from database
 * ───────────────────────────────────────────── */
try {
    $pdo = db();

    /* Xác định publisher hiện tại từ session */
    $publisherUserId = $_SESSION['user_id'] ?? null;

    if ($publisherUserId) {
        $stmtPub = $pdo->prepare("SELECT id FROM publishers WHERE user_id = ? LIMIT 1");
        $stmtPub->execute([$publisherUserId]);
        $publisherId = $stmtPub->fetchColumn() ?: null;
    } else {
        /* Fallback: lấy publisher đầu tiên trong DB */
        $publisherId = $pdo->query("SELECT id FROM publishers LIMIT 1")->fetchColumn() ?: null;
    }

    /* ── Lấy thông tin tài khoản Publisher hiển thị ở sidebar ── */
    $publisherName  = 'NXB Kim Đồng';
    $publisherEmail = 'nxb@kimdong.vn';
    if ($publisherId) {
        $stmtPubInfo = $pdo->prepare("
            SELECT p.company_name, COALESCE(p.company_email, u.email) AS email
            FROM publishers p
            JOIN users u ON p.user_id = u.id
            WHERE p.id = ?
        ");
        $stmtPubInfo->execute([$publisherId]);
        $pubInfo = $stmtPubInfo->fetch();
        if ($pubInfo) {
            $publisherName  = $pubInfo['company_name'];
            $publisherEmail = $pubInfo['email'];
        }
    }

    /* ── Xử lý POST Actions (Sinh mã kích hoạt, Tạo khuyến mãi) ── */
    $publisherFlashMessage = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['publisher_action'])) {
        $action = $_POST['publisher_action'];
        if ($action === 'generate_codes' && $publisherId) {
            $bookId   = (int)($_POST['book_id'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 10);
            if ($bookId > 0 && $quantity > 0) {
                $stmtCode = $pdo->prepare("INSERT INTO activation_codes (book_id, publisher_id, code, status, created_at) VALUES (?, ?, ?, 'unused', NOW())");
                for ($i = 0; $i < $quantity; $i++) {
                    $code = 'RDL-' . strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 4)) . '-' . strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 4));
                    try {
                        $stmtCode->execute([$bookId, $publisherId, $code]);
                    } catch (\Throwable $e) {}
                }
                $publisherFlashMessage = "✅ Đã sinh thành công {$quantity} mã kích hoạt mới!";
            }
        } elseif ($action === 'create_promotion' && $publisherId) {
            $name      = trim($_POST['name'] ?? '');
            $type      = ($_POST['type'] ?? '') === 'Flash Sale' ? 'flash_sale' : 'voucher';
            $discount  = (int)($_POST['discount'] ?? 10);
            $startDate = $_POST['start_date'] ?? date('Y-m-d');
            $endDate   = $_POST['end_date'] ?? date('Y-m-d', strtotime('+30 days'));
            $selectedBookIds = $_POST['book_ids'] ?? [];

            if (!empty($name)) {
                $stmtPromo = $pdo->prepare("INSERT INTO promotions (publisher_id, name, type, discount_percent, max_uses, start_date, end_date, is_active, created_at) VALUES (?, ?, ?, ?, 1000, ?, ?, 1, NOW())");
                $stmtPromo->execute([$publisherId, $name, $type, $discount, $startDate, $endDate]);
                $promoId = $pdo->lastInsertId();

                if ($promoId && is_array($selectedBookIds)) {
                    $stmtPb = $pdo->prepare("INSERT INTO promotion_books (promotion_id, book_id) VALUES (?, ?)");
                    foreach ($selectedBookIds as $bId) {
                        try {
                            $stmtPb->execute([$promoId, (int)$bId]);
                        } catch (\Throwable $e) {}
                    }
                }
                $publisherFlashMessage = "🎉 Đã tạo thành công chiến dịch khuyến mãi '{$name}'!";
            }
        }
    }

    /* ── Danh sách sách của publisher ── */
    if ($publisherId) {
        $stmtBooks = $pdo->prepare("
            SELECT
                b.id,
                b.title,
                b.author,
                b.list_price      AS listPrice,
                b.digital_price   AS digitalPrice,
                b.avg_rating      AS rating,
                b.total_reviews   AS reviews,
                b.status,
                b.cover_path      AS cover,
                b.cover_color     AS coverColor,
                COALESCE(c.name, 'Chưa phân loại') AS category,
                NULL AS campaign,
                NULL AS type
            FROM books b
            LEFT JOIN categories c ON b.category_id = c.id
            WHERE b.publisher_id = ?
            ORDER BY b.created_at DESC
        ");
        $stmtBooks->execute([$publisherId]);
        $books = $stmtBooks->fetchAll();

        /* Chuẩn hoá giá trị hiển thị */
        $books = array_map(function (array $b): array {
            $b['listPrice']    = number_format((float)$b['listPrice'],    0, ',', '.');
            $b['digitalPrice'] = number_format((float)$b['digitalPrice'], 0, ',', '.');
            $b['rating']       = number_format((float)$b['rating'], 1);
            $b['reviews']      = (int) $b['reviews'];
            return $b;
        }, $books);
    } else {
        $books = [];
    }

    /* ── Dữ liệu biểu đồ doanh thu / lượt tải ── */
    /* 7 ngày qua */
    $rows7d = $pdo->query("
        SELECT
            DATE_FORMAT(o.created_at, '%d/%m') AS label,
            COALESCE(SUM(o.final_total) / 1000000, 0) AS revenue,
            COUNT(DISTINCT oi.id) AS downloads
        FROM orders o
        JOIN order_items oi ON oi.order_id = o.id
        " . ($publisherId ? "JOIN books b ON oi.book_id = b.id AND b.publisher_id = $publisherId" : "") . "
        WHERE o.created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
          AND o.status = 'paid'
        GROUP BY DATE(o.created_at)
        ORDER BY DATE(o.created_at)
    ")->fetchAll();

    /* Tháng này theo tuần */
    $rowsMonth = $pdo->query("
        SELECT
            CONCAT('Tuần ', WEEK(o.created_at) - WEEK(DATE_FORMAT(CURDATE(),'%Y-%m-01')) + 1) AS label,
            COALESCE(SUM(o.final_total) / 1000000, 0) AS revenue,
            COUNT(DISTINCT oi.id) AS downloads
        FROM orders o
        JOIN order_items oi ON oi.order_id = o.id
        " . ($publisherId ? "JOIN books b ON oi.book_id = b.id AND b.publisher_id = $publisherId" : "") . "
        WHERE MONTH(o.created_at) = MONTH(CURDATE())
          AND YEAR(o.created_at)  = YEAR(CURDATE())
          AND o.status = 'paid'
        GROUP BY WEEK(o.created_at)
        ORDER BY WEEK(o.created_at)
    ")->fetchAll();

    /* Năm nay theo tháng */
    $rowsYear = $pdo->query("
        SELECT
            DATE_FORMAT(o.created_at, 'T%m') AS label,
            COALESCE(SUM(o.final_total) / 1000000, 0) AS revenue,
            COUNT(DISTINCT oi.id) AS downloads
        FROM orders o
        JOIN order_items oi ON oi.order_id = o.id
        " . ($publisherId ? "JOIN books b ON oi.book_id = b.id AND b.publisher_id = $publisherId" : "") . "
        WHERE YEAR(o.created_at) = YEAR(CURDATE())
          AND o.status = 'paid'
        GROUP BY MONTH(o.created_at)
        ORDER BY MONTH(o.created_at)
    ")->fetchAll();

    $buildPeriod = function (array $rows): array {
        return [
            'labels'    => array_column($rows, 'label'),
            'revenue'   => array_map(fn($r) => round((float)$r['revenue'], 1), $rows),
            'downloads' => array_map(fn($r) => (int)$r['downloads'], $rows),
        ];
    };

    $periodData = [
        '7days' => $buildPeriod($rows7d),
        'month' => $buildPeriod($rowsMonth),
        'year'  => $buildPeriod($rowsYear),
    ];

    /* ── Danh sách mã kích hoạt ── */
    $activationCodesList = [];
    if ($publisherId) {
        $stmtCodesList = $pdo->prepare("
            SELECT
                ac.code,
                b.title AS book_title,
                ac.status,
                u.email AS user_email,
                DATE_FORMAT(ac.created_at, '%d/%m/%Y') AS created_at
            FROM activation_codes ac
            JOIN books b ON ac.book_id = b.id
            LEFT JOIN users u ON ac.used_by = u.id
            WHERE ac.publisher_id = ?
            ORDER BY ac.created_at DESC
            LIMIT 50
        ");
        $stmtCodesList->execute([$publisherId]);
        $activationCodesList = $stmtCodesList->fetchAll();
    }

    /* ── Danh sách khuyến mãi ── */
    $promotionsList = [];
    if ($publisherId) {
        $stmtPromos = $pdo->prepare("
            SELECT
                p.id,
                p.name,
                p.type,
                p.discount_percent,
                p.max_uses,
                p.used_count,
                DATE_FORMAT(p.start_date, '%d/%m/%Y') AS start_date,
                DATE_FORMAT(p.end_date, '%d/%m/%Y') AS end_date,
                (SELECT GROUP_CONCAT(b.title SEPARATOR ', ')
                 FROM promotion_books pb
                 JOIN books b ON pb.book_id = b.id
                 WHERE pb.promotion_id = p.id) AS book_titles
            FROM promotions p
            WHERE p.publisher_id = ?
            ORDER BY p.created_at DESC
        ");
        $stmtPromos->execute([$publisherId]);
        $promotionsList = $stmtPromos->fetchAll();
    }

    /* Đảm bảo không có mảng rỗng gây lỗi chart JS */
    foreach ($periodData as $k => $v) {
        if (empty($v['labels'])) {
            $periodData[$k] = ['labels' => ['—'], 'revenue' => [0], 'downloads' => [0]];
        }
    }

} catch (\Throwable $e) {
    $books               = [];
    $activationCodesList = [];
    $promotionsList      = [];
    $periodData = [
        '7days' => ['labels' => ['—'], 'revenue' => [0], 'downloads' => [0]],
        'month' => ['labels' => ['—'], 'revenue' => [0], 'downloads' => [0]],
        'year'  => ['labels' => ['—'], 'revenue' => [0], 'downloads' => [0]],
    ];
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="/assets/css/publisher-dashboard.css?v=<?= time() ?>">
</head>
<body class="publisher-body">
  <div class="publisher-layout">
    <aside class="publisher-sidebar">
      <a class="publisher-brand" href="/publisher-dashboard">
        <span class="brand-mark">R</span>
        <span><strong>readly</strong><small>for Publisher</small></span>
      </a>

      <nav class="publisher-nav" aria-label="Điều hướng Publisher">
        <?php foreach (['overview' => ['▦', 'Tổng quan'], 'products' => ['▤', 'Sản phẩm sách'], 'codes' => ['⌁', 'Mã kích hoạt'], 'promotions' => ['✦', 'Khuyến mãi'], 'reports' => ['▧', 'Báo cáo']] as $key => [$icon, $label]): ?>
          <a class="publisher-nav-item <?= $page === $key ? 'is-active' : '' ?>" href="/publisher-dashboard?page=<?= $key ?>">
            <span><?= $icon ?></span><?= $label ?>
          </a>
        <?php endforeach; ?>
      </nav>

      <div class="publisher-account">
        <span class="account-avatar"><?= mb_substr(htmlspecialchars($publisherName, ENT_QUOTES, 'UTF-8'), 0, 3, 'UTF-8') ?></span>
        <span>
          <strong><?= htmlspecialchars($publisherName, ENT_QUOTES, 'UTF-8') ?></strong>
          <small><?= htmlspecialchars($publisherEmail, ENT_QUOTES, 'UTF-8') ?></small>
        </span>
      </div>
      <a class="logout-button" href="/logout" style="display:flex;align-items:center;gap:6px;text-decoration:none;">↪ Đăng xuất</a>
    </aside>

    <main class="publisher-main">
      <header class="publisher-header">
        <div>
          <?php if ($page === 'add-book'): ?>
            <a class="breadcrumb" href="/publisher-dashboard">Tổng quan</a><span class="breadcrumb"> / </span>
          <?php endif; ?>
          <h1><?= htmlspecialchars($pages[$page]['title'], ENT_QUOTES, 'UTF-8') ?></h1>
        </div>
        <div class="header-actions">
          <label class="quick-search">⌕<input type="search" placeholder="Tìm kiếm nhanh..." data-book-search></label>
          <button class="icon-button" type="button" data-toast="Bạn có 3 thông báo mới." aria-label="Thông báo">♢<i></i></button>
          <?php if ($page !== 'add-book'): ?>
            <a class="primary-button" href="/publisher-dashboard?page=add-book">＋ Thêm sách mới</a>
          <?php endif; ?>
        </div>
      </header>

      <section class="publisher-content">
        <?php if (!empty($publisherFlashMessage)): ?>
          <div style="padding: 12px 20px; margin-bottom: 20px; border-radius: 8px; font-weight: 600; background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;">
            <?= htmlspecialchars($publisherFlashMessage, ENT_QUOTES, 'UTF-8') ?>
          </div>
        <?php endif; ?>
        <?php if ($page === 'overview'):
            /* ── Overview stats từ DB ── */
            try {
                $pdo = $pdo ?? db();
                $overviewStats = [];

                if ($publisherId ?? null) {
                    /* Doanh thu tháng này */
                    $stmtRev = $pdo->prepare("
                        SELECT COALESCE(SUM(o.final_total), 0)
                        FROM orders o
                        JOIN order_items oi ON oi.order_id = o.id
                        JOIN books b ON oi.book_id = b.id
                        WHERE b.publisher_id = ?
                          AND o.status = 'paid'
                          AND MONTH(o.created_at) = MONTH(CURDATE())
                          AND YEAR(o.created_at)  = YEAR(CURDATE())
                    ");
                    $stmtRev->execute([$publisherId]);
                    $overviewStats['revenue'] = (int) $stmtRev->fetchColumn();

                    /* Lượt tải tháng này */
                    $stmtDl = $pdo->prepare("
                        SELECT COUNT(DISTINCT oi.id)
                        FROM order_items oi
                        JOIN books b ON oi.book_id = b.id
                        JOIN orders o ON oi.order_id = o.id
                        WHERE b.publisher_id = ?
                          AND o.status = 'paid'
                          AND MONTH(o.created_at) = MONTH(CURDATE())
                          AND YEAR(o.created_at)  = YEAR(CURDATE())
                    ");
                    $stmtDl->execute([$publisherId]);
                    $overviewStats['downloads'] = (int) $stmtDl->fetchColumn();

                    /* Mã kích hoạt */
                    $stmtCodes = $pdo->prepare("
                        SELECT
                            COUNT(*) AS total,
                            SUM(status = 'used')   AS used_count,
                            SUM(status = 'unused') AS unused_count
                        FROM activation_codes
                        WHERE publisher_id = ?
                    ");
                    $stmtCodes->execute([$publisherId]);
                    $codeStats = $stmtCodes->fetch();
                    $overviewStats['codes_total']  = (int)($codeStats['total']       ?? 0);
                    $overviewStats['codes_used']   = (int)($codeStats['used_count']  ?? 0);
                    $overviewStats['codes_unused'] = (int)($codeStats['unused_count']?? 0);
                } else {
                    $overviewStats = ['revenue' => 0, 'downloads' => 0, 'codes_total' => 0, 'codes_used' => 0, 'codes_unused' => 0];
                }

                $donutPct = $overviewStats['codes_total'] > 0
                    ? round($overviewStats['codes_used'] / $overviewStats['codes_total'] * 100, 2)
                    : 0;
            } catch (\Throwable $e) {
                $overviewStats = ['revenue' => 0, 'downloads' => 0, 'codes_total' => 0, 'codes_used' => 0, 'codes_unused' => 0];
                $donutPct = 0;
            }
        ?>
          <div class="stat-grid">
            <article class="stat-card">
                <span>Doanh số bán sách</span>
                <b><?= number_format($overviewStats['revenue'], 0, ',', '.') ?><small>₫</small></b>
                <em>Tháng này</em>
            </article>
            <article class="stat-card">
                <span>Số lượt tải/đọc</span>
                <b><?= number_format($overviewStats['downloads']) ?><small>lượt</small></b>
                <em>Tháng này</em>
            </article>
            <article class="stat-card yellow">
                <span>Tổng sách xuất bản</span>
                <b><?= count($books) ?><small>cuốn</small></b>
                <em>Của nhà phát hành</em>
            </article>
            <article class="stat-card">
                <span>Mã đã kích hoạt</span>
                <b><?= number_format($overviewStats['codes_used']) ?><small>mã</small></b>
                <em>/ <?= number_format($overviewStats['codes_total']) ?> mã phát hành</em>
            </article>
          </div>

          <div class="dashboard-grid">
            <article class="panel chart-panel">
              <div class="panel-title">
                <h2>Tổng quan doanh thu &amp; lượt tải</h2>
                <div class="period-switcher"><button class="is-selected" data-period="7days">7 ngày qua</button><button data-period="month">Tháng này</button><button data-period="year">Năm nay</button></div>
              </div>
              <canvas id="revenue-chart" height="290" data-chart='<?= htmlspecialchars(json_encode($periodData, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>'></canvas>
              <div class="chart-legend"><span><i class="teal"></i>Doanh thu (triệu ₫)</span><span><i class="yellow-dot"></i>Lượt tải</span></div>
            </article>
            <article class="panel activation-summary">
              <h2>Trạng thái mã kích hoạt</h2>
              <div class="donut" style="--used:<?= $donutPct ?>">
                  <strong><?= number_format($overviewStats['codes_total']) ?></strong>
                  <span>Tổng mã</span>
              </div>
              <dl>
                  <div><dt><i class="teal"></i>Đã sử dụng</dt><dd><?= number_format($overviewStats['codes_used']) ?></dd></div>
                  <div><dt><i class="gray-dot"></i>Chưa sử dụng</dt><dd><?= number_format($overviewStats['codes_unused']) ?></dd></div>
              </dl>
              <a class="outline-button" href="/publisher-dashboard?page=codes">Sinh mã tự động</a>
              <button class="outline-button" type="button" data-export="activation-codes">⇩ Xuất Excel</button>
            </article>
          </div>


          <article class="panel">
            <div class="panel-title"><h2>Sản phẩm và Khuyến mãi gần đây</h2><a href="/publisher-dashboard?page=products">Xem tất cả</a></div>
            <?php $compact = true; require __DIR__ . '/component/ProductsPage.php'; ?>
          </article>
        <?php else: ?>
          <?php require __DIR__ . '/component/' . $pages[$page]['file']; ?>
        <?php endif; ?>
      </section>
    </main>
  </div>

  <div class="toast" role="status" aria-live="polite"></div>
  <script type="module" src="/assets/js/publisher-dashboard.js?v=<?= time() ?>"></script>
</body>
</html>
