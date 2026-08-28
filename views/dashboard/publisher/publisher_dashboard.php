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

$books = [
    ['id' => 1, 'title' => 'Đắc Nhân Tâm', 'author' => 'Dale Carnegie', 'listPrice' => '129.000', 'digitalPrice' => '89.000', 'campaign' => 'Flash Sale', 'type' => 'sale', 'rating' => '4.8', 'reviews' => 234, 'cover' => 'https://images.unsplash.com/photo-1604342162684-0cb7869cc445?auto=format&fit=crop&w=200&q=80'],
    ['id' => 2, 'title' => 'Nghệ Thuật Bán Hàng', 'author' => 'Brian Tracy', 'listPrice' => '159.000', 'digitalPrice' => '119.000', 'campaign' => 'Voucher -25%', 'type' => 'voucher', 'rating' => '4.5', 'reviews' => 187, 'cover' => 'https://images.unsplash.com/photo-1554774853-aae0a22c8aa4?auto=format&fit=crop&w=200&q=80'],
    ['id' => 3, 'title' => 'Tâm Lý Học Hành Vi', 'author' => 'Daniel Kahneman', 'listPrice' => '179.000', 'digitalPrice' => '149.000', 'campaign' => null, 'type' => null, 'rating' => '4.9', 'reviews' => 312, 'cover' => 'https://images.unsplash.com/photo-1534289855405-ab820a118fc1?auto=format&fit=crop&w=200&q=80'],
    ['id' => 4, 'title' => 'Kỹ Năng Lãnh Đạo', 'author' => 'John C. Maxwell', 'listPrice' => '199.000', 'digitalPrice' => '169.000', 'campaign' => 'Flash Sale', 'type' => 'sale', 'rating' => '4.6', 'reviews' => 156, 'cover' => 'https://images.unsplash.com/photo-1761498465932-fd1dac8f6f77?auto=format&fit=crop&w=200&q=80'],
    ['id' => 5, 'title' => 'Tư Duy Phản Biện', 'author' => 'Tom Chatfield', 'listPrice' => '139.000', 'digitalPrice' => '99.000', 'campaign' => 'Voucher -30%', 'type' => 'voucher', 'rating' => '4.7', 'reviews' => 203, 'cover' => 'https://images.unsplash.com/photo-1711185898226-beea7eee0611?auto=format&fit=crop&w=200&q=80'],
];

$periodData = [
    '7days' => ['labels' => ['18/7', '19/7', '20/7', '21/7', '22/7', '23/7', '24/7'], 'revenue' => [45, 52, 48, 61, 55, 67, 72], 'downloads' => [120, 145, 132, 178, 165, 192, 210]],
    'month' => ['labels' => ['Tuần 1', 'Tuần 2', 'Tuần 3', 'Tuần 4'], 'revenue' => [210, 265, 245, 310], 'downloads' => [580, 690, 650, 790]],
    'year' => ['labels' => ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'], 'revenue' => [245, 280, 320, 290, 350, 380, 420], 'downloads' => [860, 920, 1100, 1060, 1250, 1320, 1450]],
];
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

      <div class="publisher-account"><span class="account-avatar">NXB</span><span><strong>NXB Kim Đồng</strong><small>nxb@kimdong.vn</small></span></div>
      <button class="logout-button" type="button" data-toast="Chức năng đăng xuất sẽ được kết nối cùng hệ thống đăng nhập.">↪ Đăng xuất</button>
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
        <?php if ($page === 'overview'): ?>
          <div class="stat-grid">
            <article class="stat-card"><span>Doanh số bán sách</span><b>158.450.000<small>₫</small></b><em>↗ +12,5% <i>so với tuần trước</i></em></article>
            <article class="stat-card"><span>Số lượt tải/đọc</span><b>2.847<small>lượt</small></b><em>↗ +8,3% <i>so với tuần trước</i></em></article>
            <article class="stat-card yellow"><span>Thời gian đọc TB</span><b>45<small>phút/phiên</small></b><em>↗ +5,2% <i>so với tuần trước</i></em></article>
            <article class="stat-card"><span>Mã đã kích hoạt</span><b>1.247<small>mã</small></b><em>↗ +18,7% <i>so với tuần trước</i></em></article>
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
              <div class="donut" style="--used:62.35"><strong>2.000</strong><span>Tổng mã</span></div>
              <dl><div><dt><i class="teal"></i>Đã sử dụng</dt><dd>1.247</dd></div><div><dt><i class="gray-dot"></i>Chưa sử dụng</dt><dd>753</dd></div></dl>
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
