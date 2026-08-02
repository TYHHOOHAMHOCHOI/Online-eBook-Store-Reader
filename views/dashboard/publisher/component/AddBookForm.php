<form class="book-form panel" method="post" enctype="multipart/form-data" data-book-form>
  <div class="form-heading">
    <div>
      <h2>Thông tin xuất bản</h2>
      <p>Điền các thông tin cần thiết trước khi gửi sách để xét duyệt.</p>
    </div>
    <span class="draft-badge">Bản nháp</span>
  </div>

  <div class="form-layout">
    <section class="upload-column">
      <label class="cover-upload" data-cover-drop>
        <input type="file" name="cover" accept="image/*" data-cover-input>
        <span class="upload-placeholder">▧<b>Tải ảnh bìa lên</b><small>PNG, JPG hoặc WEBP · tối đa 5 MB</small></span>
        <img alt="Xem trước ảnh bìa" hidden data-cover-preview>
      </label>
      <label class="epub-upload">
        <input type="file" name="epub" accept=".epub,application/epub+zip" data-epub-input>
        <b>⌁ Tải tệp EPUB</b><small data-epub-name>Chưa có tệp nào được chọn</small>
      </label>
    </section>

    <section class="form-fields">
      <label>Tên sách <input required name="title" placeholder="Nhập tên sách"></label>
      <div class="two-fields">
        <label>Tác giả <input required name="author" placeholder="Tên tác giả"></label>
        <label>Thể loại <select required name="category"><option value="">Chọn thể loại</option><option>Kỹ năng sống</option><option>Kinh doanh</option><option>Tâm lý học</option><option>Giáo dục</option></select></label>
      </div>
      <label>Mô tả <textarea name="description" rows="5" placeholder="Giới thiệu ngắn về nội dung sách"></textarea></label>
      <div class="two-fields">
        <label>ISBN <input name="isbn" placeholder="978-..." inputmode="numeric"></label>
        <label>Năm xuất bản <input name="publish_year" type="number" value="2026" min="1900" max="2100"></label>
      </div>
      <div class="two-fields">
        <label>Giá niêm yết (₫) <input required name="list_price" type="number" min="0" placeholder="129000"></label>
        <label>Giá điện tử (₫) <input required name="digital_price" type="number" min="0" placeholder="89000"></label>
      </div>
      <fieldset>
        <legend>Tùy chọn phát hành</legend>
        <label class="check-row"><input type="checkbox" name="physical_code" checked> Cho phép tạo mã kích hoạt cho sách giấy</label>
        <label class="check-row"><input type="checkbox" name="rental"> Cho phép thuê sách điện tử</label>
      </fieldset>
    </section>
  </div>

  <div class="form-actions">
    <a class="secondary-button" href="/publisher-dashboard">Hủy</a>
    <button class="primary-button" type="submit">Lưu sách</button>
  </div>
</form>
