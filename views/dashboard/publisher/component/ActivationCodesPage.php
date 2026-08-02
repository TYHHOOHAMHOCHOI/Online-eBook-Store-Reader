<div class="page-intro">
  <div><p>Theo dõi và phân phối mã kích hoạt cho từng đầu sách.</p></div>
  <div>
    <button class="outline-button" type="button" data-export="activation-codes">⇩ Xuất Excel</button>
    <button class="primary-button" type="button" data-generate-codes>Sinh mã mới</button>
  </div>
</div>

<div class="stat-grid compact-stats">
  <article class="stat-card"><span>Tổng mã đã phát hành</span><b>2.000</b><em>↗ +250 <i>trong tháng này</i></em></article>
  <article class="stat-card"><span>Đã kích hoạt</span><b>1.247</b><em>62,4% <i>tổng số mã</i></em></article>
  <article class="stat-card yellow"><span>Còn khả dụng</span><b>753</b><em>↗ +18,7% <i>so với tháng trước</i></em></article>
</div>

<article class="panel">
  <div class="filter-bar">
    <label>⌕ <input type="search" placeholder="Tìm mã kích hoạt" data-code-search></label>
    <select><option>Tất cả trạng thái</option><option>Đã sử dụng</option><option>Chưa sử dụng</option></select>
    <select>
      <option>Tất cả sản phẩm</option>
      <?php foreach ($books as $book): ?>
        <option><?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="table-wrap">
    <table class="product-table">
      <thead><tr><th>Mã kích hoạt</th><th>Sản phẩm</th><th>Trạng thái</th><th>Người dùng</th><th>Ngày tạo</th></tr></thead>
      <tbody data-codes-table>
        <tr><td>RDL-Y7KP-9H2M</td><td>Đắc Nhân Tâm</td><td><span class="status active">Đã sử dụng</span></td><td>nguyen.an@example.com</td><td>24/07/2026</td></tr>
        <tr><td>RDL-Q8MT-4XKA</td><td>Tâm Lý Học Hành Vi</td><td><span class="status pending">Chưa sử dụng</span></td><td>—</td><td>24/07/2026</td></tr>
        <tr><td>RDL-H5NZ-7CPR</td><td>Nghệ Thuật Bán Hàng</td><td><span class="status active">Đã sử dụng</span></td><td>minh.t@example.com</td><td>23/07/2026</td></tr>
      </tbody>
    </table>
  </div>
</article>
