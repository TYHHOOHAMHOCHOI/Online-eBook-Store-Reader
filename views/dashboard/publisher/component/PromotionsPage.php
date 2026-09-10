<div class="page-intro">
  <div><p>Tạo chiến dịch để tăng khả năng tiếp cận cho sản phẩm của bạn.</p></div>
  <button class="primary-button" type="button" data-open-promotion>＋ Tạo chiến dịch</button>
</div>

<div class="promotion-grid">
  <?php if (empty($promotionsList)): ?>
    <div style="grid-column:1/-1;text-align:center;padding:3rem;color:#94a3b8;background:#fff;border-radius:12px;border:1px dashed #cbd5e1;">
      Chưa có chiến dịch khuyến mãi nào. Hãy tạo chiến dịch mới!
    </div>
  <?php else: ?>
    <?php foreach ($promotionsList as $promo):
      $pct = $promo['max_uses'] ? round(($promo['used_count'] / $promo['max_uses']) * 100) : 0;
    ?>
      <article class="promotion-card">
        <div>
          <span class="campaign <?= $promo['type'] === 'flash_sale' ? 'sale' : 'voucher' ?>">
            <?= $promo['type'] === 'flash_sale' ? '⚡ Flash Sale' : '♢ Voucher' ?>
          </span>
          <strong><?= htmlspecialchars($promo['name'], ENT_QUOTES, 'UTF-8') ?></strong>
        </div>
        <b>-<?= (int)$promo['discount_percent'] ?>%</b>
        <p><?= htmlspecialchars($promo['start_date'], ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars($promo['end_date'], ENT_QUOTES, 'UTF-8') ?></p>
        <small>Áp dụng cho: <?= htmlspecialchars($promo['book_titles'] ?? 'Tất cả sản phẩm', ENT_QUOTES, 'UTF-8') ?></small>
        <?php if ($promo['max_uses']): ?>
          <div class="usage"><span><i style="width:<?= $pct ?>%"></i></span><em><?= number_format($promo['used_count']) ?> / <?= number_format($promo['max_uses']) ?> mã</em></div>
        <?php endif; ?>
        <div>
          <button class="secondary-button" type="button" data-toast="Chức năng chỉnh sửa khuyến mãi.">Chỉnh sửa</button>
          <button class="danger-button" type="button" data-toast="Xóa khuyến mãi.">×</button>
        </div>
      </article>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<div class="modal-backdrop" data-promotion-modal hidden>
  <form class="modal" data-promotion-form method="post" action="/publisher-dashboard?page=promotions">
    <input type="hidden" name="publisher_action" value="create_promotion">
    <button class="modal-close" type="button" data-close-modal>×</button>
    <h2>Tạo chiến dịch mới</h2>
    <label>Tên chiến dịch <input required name="name" placeholder="Ví dụ: Flash Sale cuối tuần"></label>
    <div class="two-fields">
      <label>Loại <select name="type"><option>Flash Sale</option><option>Voucher</option></select></label>
      <label>Mức giảm (%) <input required name="discount" type="number" min="1" max="100" placeholder="20"></label>
    </div>
    <div class="two-fields">
      <label>Bắt đầu <input required name="start_date" type="date" value="<?= date('Y-m-d') ?>"></label>
      <label>Kết thúc <input required name="end_date" type="date" value="<?= date('Y-m-d', strtotime('+30 days')) ?>"></label>
    </div>
    <fieldset>
      <legend>Áp dụng cho sản phẩm</legend>
      <?php foreach ($books as $book): ?>
        <label class="check-row"><input type="checkbox" name="book_ids[]" value="<?= $book['id'] ?>"> <?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?></label>
      <?php endforeach; ?>
    </fieldset>
    <div class="form-actions">
      <button class="secondary-button" type="button" data-close-modal>Hủy</button>
      <button class="primary-button" type="submit">Tạo chiến dịch</button>
    </div>
  </form>
</div>
