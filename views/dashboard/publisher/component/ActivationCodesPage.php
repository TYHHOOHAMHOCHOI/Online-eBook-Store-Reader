<div class="page-intro">
  <div><p>Theo dõi và phân phối mã kích hoạt cho từng đầu sách.</p></div>
  <div style="display:flex;gap:10px;align-items:center;">
    <button class="outline-button" type="button" data-export="activation-codes">⇩ Xuất Excel</button>
    <form method="post" action="/publisher-dashboard?page=codes" style="display:inline-flex;gap:8px;align-items:center;">
      <input type="hidden" name="publisher_action" value="generate_codes">
      <select name="book_id" required style="padding:6px 10px;border:1px solid #cbd5e1;border-radius:6px;font-size:0.85rem;background:#fff;">
        <option value="">Chọn sản phẩm</option>
        <?php foreach ($books as $b): ?>
          <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['title'], ENT_QUOTES, 'UTF-8') ?></option>
        <?php endforeach; ?>
      </select>
      <input type="number" name="quantity" value="10" min="1" max="100" title="Số lượng mã" style="width:60px;padding:6px;border:1px solid #cbd5e1;border-radius:6px;font-size:0.85rem;background:#fff;">
      <button class="primary-button" type="submit">⚡ Sinh mã mới</button>
    </form>
  </div>
</div>

<div class="stat-grid compact-stats">
  <article class="stat-card">
    <span>Tổng mã đã phát hành</span>
    <b><?= number_format($overviewStats['codes_total'] ?? 0) ?></b>
  </article>
  <article class="stat-card">
    <span>Đã kích hoạt</span>
    <b><?= number_format($overviewStats['codes_used'] ?? 0) ?></b>
    <em><?= ($overviewStats['codes_total'] ?? 0) > 0 ? round(($overviewStats['codes_used'] / $overviewStats['codes_total']) * 100, 1) : 0 ?>% tổng số mã</em>
  </article>
  <article class="stat-card yellow">
    <span>Còn khả dụng</span>
    <b><?= number_format($overviewStats['codes_unused'] ?? 0) ?></b>
  </article>
</div>

<article class="panel">
  <div class="filter-bar">
    <label>⌕ <input type="search" placeholder="Tìm mã kích hoạt" data-code-search></label>
    <select><option value="">Tất cả trạng thái</option><option value="used">Đã sử dụng</option><option value="unused">Chưa sử dụng</option></select>
    <select>
      <option value="">Tất cả sản phẩm</option>
      <?php foreach ($books as $book): ?>
        <option value="<?= $book['id'] ?>"><?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="table-wrap">
    <table class="product-table">
      <thead><tr><th>Mã kích hoạt</th><th>Sản phẩm</th><th>Trạng thái</th><th>Người dùng</th><th>Ngày tạo</th></tr></thead>
      <tbody data-codes-table>
        <?php if (empty($activationCodesList)): ?>
          <tr><td colSpan="5" style="text-align:center;color:#94a3b8;padding:2rem;">Chưa có mã kích hoạt nào trong hệ thống.</td></tr>
        <?php else: ?>
          <?php foreach ($activationCodesList as $codeItem): ?>
            <tr>
              <td><code><?= htmlspecialchars($codeItem['code'], ENT_QUOTES, 'UTF-8') ?></code></td>
              <td><?= htmlspecialchars($codeItem['book_title'], ENT_QUOTES, 'UTF-8') ?></td>
              <td>
                <?php if ($codeItem['status'] === 'used'): ?>
                  <span class="status active">Đã sử dụng</span>
                <?php else: ?>
                  <span class="status pending">Chưa sử dụng</span>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($codeItem['user_email'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($codeItem['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</article>

