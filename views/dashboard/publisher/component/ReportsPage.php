<div class="page-intro">
  <div><p>Phân tích chi tiết doanh thu và hoạt động của nhà xuất bản.</p></div>
  <div><select class="report-range"><option>Tháng này</option><option>Quý này</option><option>Năm nay</option></select><button class="primary-button" type="button" data-export="publisher-report">⇩ Xuất báo cáo</button></div>
</div>

<div class="stat-grid compact-stats">
  <article class="stat-card">
    <span>Tổng doanh thu</span>
    <b><?= number_format($overviewStats['revenue'] ?? 0, 0, ',', '.') ?><small>₫</small></b>
  </article>
  <article class="stat-card">
    <span>Lượt mua & đọc</span>
    <b><?= number_format($overviewStats['downloads'] ?? 0)<small>lượt</small></b>
  </article>
  <article class="stat-card yellow">
    <span>Tổng số sách</span>
    <b><?= count($books)<small>cuốn</small></b>
  </article>
  <article class="stat-card">
    <span>Mã kích hoạt đã dùng</span>
    <b><?= number_format($overviewStats['codes_used'] ?? 0)<small>mã</small></b>
  </article>
</div>

<article class="panel report-chart"><h2>Doanh thu theo tháng</h2><canvas id="report-chart" height="290"></canvas></article>
<div class="dashboard-grid report-grid">
  <article class="panel top-books">
    <h2>Sản phẩm của bạn</h2>
    <?php if (empty($books)): ?>
      <p style="color:#94a3b8;padding:1rem;">Chưa có sản phẩm nào.</p>
    <?php else: ?>
      <?php foreach (array_slice($books, 0, 5) as $index => $book): ?>
        <div>
          <b><?= $index + 1 ?></b>
          <span>
            <strong><?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?></strong>
            <small><?= htmlspecialchars($book['author'], ENT_QUOTES, 'UTF-8') ?></small>
          </span>
          <em><?= $book['digitalPrice'] ?>₫</em>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </article>
  <article class="panel category-panel">
    <h2>Phân bố sản phẩm</h2>
    <div class="category-donut"></div>
    <dl>
      <div><dt><i class="teal"></i>Sách điện tử</dt><dd>100%</dd></div>
    </dl>
  </article>
</div>
