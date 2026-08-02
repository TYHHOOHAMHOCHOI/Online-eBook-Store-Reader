<div class="page-intro">
  <div><p>Tạo chiến dịch để tăng khả năng tiếp cận cho sản phẩm của bạn.</p></div>
  <button class="primary-button" type="button" data-open-promotion>＋ Tạo chiến dịch</button>
</div>

<div class="promotion-grid">
  <article class="promotion-card">
    <div><span class="campaign sale">⚡ Flash Sale</span><strong>Ưu đãi sách mùa hè</strong></div>
    <b>-30%</b><p>20/07/2026 — 31/07/2026</p><small>Áp dụng cho: Đắc Nhân Tâm, Kỹ Năng Lãnh Đạo</small>
    <div class="usage"><span><i style="width:68%"></i></span><em>680 / 1.000 mã</em></div>
    <div><button class="secondary-button" type="button" data-toast="Chức năng chỉnh sửa cần API khuyến mãi.">Chỉnh sửa</button><button class="danger-button" type="button" data-toast="Chiến dịch chưa bị xóa vì chưa có API.">×</button></div>
  </article>
  <article class="promotion-card">
    <div><span class="campaign voucher">♢ Voucher</span><strong>Tri ân độc giả mới</strong></div>
    <b>-25%</b><p>01/07/2026 — 15/08/2026</p><small>Áp dụng cho: Nghệ Thuật Bán Hàng</small>
    <div class="usage"><span><i style="width:42%"></i></span><em>210 / 500 mã</em></div>
    <div><button class="secondary-button" type="button" data-toast="Chức năng chỉnh sửa cần API khuyến mãi.">Chỉnh sửa</button><button class="danger-button" type="button" data-toast="Chiến dịch chưa bị xóa vì chưa có API.">×</button></div>
  </article>
</div>

<div class="modal-backdrop" data-promotion-modal hidden>
  <form class="modal" data-promotion-form>
    <button class="modal-close" type="button" data-close-modal>×</button>
    <h2>Tạo chiến dịch mới</h2>
    <label>Tên chiến dịch <input required placeholder="Ví dụ: Flash Sale cuối tuần"></label>
    <div class="two-fields"><label>Loại <select><option>Flash Sale</option><option>Voucher</option></select></label><label>Mức giảm (%) <input required type="number" min="1" max="100" placeholder="20"></label></div>
    <div class="two-fields"><label>Bắt đầu <input required type="date"></label><label>Kết thúc <input required type="date"></label></div>
    <fieldset>
      <legend>Áp dụng cho sản phẩm</legend>
      <?php foreach ($books as $book): ?>
        <label class="check-row"><input type="checkbox" value="<?= $book['id'] ?>"> <?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?></label>
      <?php endforeach; ?>
    </fieldset>
    <div class="form-actions"><button class="secondary-button" type="button" data-close-modal>Hủy</button><button class="primary-button" type="submit">Tạo chiến dịch</button></div>
  </form>
</div>
