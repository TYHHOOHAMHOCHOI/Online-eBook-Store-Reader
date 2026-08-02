<?php

$isCompact = isset($compact) && $compact === true;
?>

<?php if (!$isCompact): ?>
  <div class="page-intro">
    <div>
      <p>Quản lý danh mục, giá bán và trạng thái xuất bản của bạn.</p>
    </div>
    <a class="primary-button" href="/publisher-dashboard?page=add-book">＋ Thêm sách mới</a>
  </div>

  <div class="filter-bar">
    <label>⌕ <input type="search" placeholder="Tìm theo tên hoặc tác giả" data-book-search></label>
    <select><option>Tất cả trạng thái</option><option>Đang xuất bản</option><option>Bản nháp</option></select>
    <select><option>Tất cả thể loại</option><option>Kỹ năng sống</option><option>Kinh doanh</option></select>
  </div>
<?php endif; ?>

<div class="table-wrap">
  <table class="product-table">
    <thead>
      <tr>
        <th>Sản phẩm</th>
        <th>Giá niêm yết</th>
        <th>Giá điện tử</th>
        <th>Chiến dịch</th>
        <th>Đánh giá</th>
        <th><span class="sr-only">Hành động</span></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($books as $book): ?>
        <tr data-book-row data-search="<?= htmlspecialchars($book['title'] . ' ' . $book['author'], ENT_QUOTES, 'UTF-8') ?>">
          <td>
            <div class="book-cell">
              <img src="<?= htmlspecialchars($book['cover'], ENT_QUOTES, 'UTF-8') ?>" alt="Bìa <?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>">
              <span><strong><?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?></strong><small><?= htmlspecialchars($book['author'], ENT_QUOTES, 'UTF-8') ?></small></span>
            </div>
          </td>
          <td><?= $book['listPrice'] ?>₫</td>
          <td class="price"><?= $book['digitalPrice'] ?>₫</td>
          <td>
            <?php if ($book['campaign']): ?>
              <span class="campaign <?= $book['type'] === 'sale' ? 'sale' : 'voucher' ?>"><?= htmlspecialchars($book['campaign'], ENT_QUOTES, 'UTF-8') ?></span>
            <?php else: ?>
              —
            <?php endif; ?>
          </td>
          <td><span class="rating">★ <?= $book['rating'] ?> <small>(<?= $book['reviews'] ?>)</small></span></td>
          <td><button type="button" class="more-button" data-toast="Tùy chọn cho “<?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>” sẽ được kết nối khi có API sách.">⋮</button></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <p class="empty-state" hidden>Không tìm thấy sách phù hợp.</p>
</div>
