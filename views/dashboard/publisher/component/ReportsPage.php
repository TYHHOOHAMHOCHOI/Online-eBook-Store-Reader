<div class="page-intro">
  <div><p>Phân tích chi tiết doanh thu và hoạt động của nhà xuất bản.</p></div>
  <div><select class="report-range"><option>Tháng này</option><option>Quý này</option><option>Năm nay</option></select><button class="primary-button" type="button" data-export="publisher-report">⇩ Xuất báo cáo</button></div>
</div>

<div class="stat-grid compact-stats">
  <article class="stat-card"><span>Tổng doanh thu</span><b>642.212.000<small>₫</small></b><em>↗ +18,5% <i>so với kỳ trước</i></em></article>
  <article class="stat-card"><span>Tổng độc giả</span><b>8.547<small>người</small></b><em>↗ +12,3% <i>so với kỳ trước</i></em></article>
  <article class="stat-card yellow"><span>Sách đã bán</span><b>5.218<small>cuốn</small></b><em>↗ +15,7% <i>so với kỳ trước</i></em></article>
  <article class="stat-card"><span>Thời gian đọc TB</span><b>52<small>phút</small></b><em>↗ +8,2% <i>so với kỳ trước</i></em></article>
</div>

<article class="panel report-chart"><h2>Doanh thu theo tháng</h2><canvas id="report-chart" height="290"></canvas></article>
<div class="dashboard-grid report-grid">
  <article class="panel top-books">
    <h2>Top sản phẩm bán chạy</h2>
    <?php foreach ($books as $index => $book): ?>
      <div><b><?= $index + 1 ?></b><span><strong><?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?></strong><small><?= [1247, 1456, 892, 678, 945][$index] ?> cuốn đã bán</small></span><em><?= number_format([110983000, 216944000, 106148000, 114582000, 93555000][$index], 0, ',', '.') ?>₫</em></div>
    <?php endforeach; ?>
  </article>
  <article class="panel category-panel">
    <h2>Phân bố thể loại</h2><div class="category-donut"></div>
    <dl><div><dt><i class="teal"></i>Kỹ năng sống</dt><dd>35%</dd></div><div><dt><i class="yellow-dot"></i>Kinh doanh</dt><dd>28%</dd></div><div><dt><i class="pink-dot"></i>Tâm lý học</dt><dd>22%</dd></div><div><dt><i class="green-dot"></i>Giáo dục</dt><dd>15%</dd></div></dl>
  </article>
</div>
