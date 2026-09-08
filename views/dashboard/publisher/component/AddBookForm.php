<?php
declare(strict_types=1);

// ==========================================================
// 1. KẾT NỐI CƠ SỞ DỮ LIỆU (DATABASE PDO)
// ==========================================================
$host = 'db';
$dbname = 'ebook_store';
$username = 'ebook_user';
$password = 'ebook_password';

$message = "";
$messageType = "";

try {
    if (!isset($pdo)) {
        if (function_exists('db')) {
            $pdo = db();
        } else {
            $host = getenv('DB_HOST') ?: 'db';
            $dbname = getenv('DB_DATABASE') ?: 'ebook_store';
            $username = getenv('DB_USERNAME') ?: 'ebook_user';
            $password = getenv('DB_PASSWORD') ?: 'ebook_password';
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }
    }
} catch (Throwable $e) {
    $dbError = $e->getMessage();
}

// Lấy ID Nhà xuất bản (tra cứu chuẩn theo publishers.id từ session / user_id)
$publisher_id = $_SESSION['publisher_id'] ?? null;
if (empty($publisher_id) && isset($pdo)) {
    $userId = $_SESSION['user_id'] ?? null;
    if ($userId) {
        $stmtP = $pdo->prepare("SELECT id FROM publishers WHERE user_id = ? LIMIT 1");
        $stmtP->execute([$userId]);
        $publisher_id = (int)$stmtP->fetchColumn();
    }
}
if (empty($publisher_id)) {
    $publisher_id = (isset($publisherId) && !empty($publisherId)) ? (int)$publisherId : 1;
}

// ==========================================================
// 2. XỬ LÝ KHI NHẤN NÚT "LƯU SÁCH" (SUBMIT FORM)
// ==========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    
    // Lấy dữ liệu từ Form (Khớp chính xác với name trong HTML)
    $title         = trim($_POST['title'] ?? '');
    $author        = trim($_POST['author'] ?? '');
    $category_name = trim($_POST['category'] ?? '');
    $description   = trim($_POST['description'] ?? '');
    $isbnRaw       = trim($_POST['isbn'] ?? '');
    $isbn          = !empty($isbnRaw) ? $isbnRaw : null;
    $publish_year  = !empty($_POST['publish_year']) ? (int)$_POST['publish_year'] : (int)date('Y');
    $list_price    = !empty($_POST['list_price']) ? (float)$_POST['list_price'] : 0;
    $digital_price = !empty($_POST['digital_price']) ? (float)$_POST['digital_price'] : 0;

    $allow_activation_code = isset($_POST['physical_code']) ? 1 : 0;
    $allow_rental          = isset($_POST['rental']) ? 1 : 0;

    if (!empty($title) && !empty($author)) {

        // Tạo slug tự động và xử lý trùng lặp
        $baseSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $baseSlug = rtrim($baseSlug, '-') ?: 'book';
        $slug = $baseSlug;

        if (isset($pdo)) {
            $stmtSlug = $pdo->prepare("SELECT COUNT(*) FROM books WHERE slug = ?");
            $stmtSlug->execute([$slug]);
            if ((int)$stmtSlug->fetchColumn() > 0) {
                $slug = $baseSlug . '-' . substr(md5(uniqid((string)mt_rand(), true)), 0, 6);
            }
        }

        // Ánh xạ Thể loại sang ID (Mặc định = 1)
        $categoryMap = [
            'Kỹ năng sống' => 4,
            'Kinh doanh'   => 8,
            'Tâm lý học'   => 3,
            'Tâm lý'       => 3,
            'Giáo dục'     => 9,
            'Văn học'      => 1,
            'Kinh tế'      => 2,
        ];
        $category_id = $categoryMap[$category_name] ?? 1;

        // Xử lý Upload Ảnh Bìa (name="cover")
        $cover_path = '';
        if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
            $upload_dir_cover = __DIR__ . '/../../../../public/uploads/covers/';
            if (!file_exists($upload_dir_cover)) {
                mkdir($upload_dir_cover, 0777, true);
            }
            $cover_filename = time() . '_' . basename($_FILES['cover']['name']);
            if (move_uploaded_file($_FILES['cover']['tmp_name'], $upload_dir_cover . $cover_filename)) {
                $cover_path = 'uploads/covers/' . $cover_filename;
            }
        }

        // Xử lý Upload File EPUB (name="epub")
        $file_path = '';
        if (isset($_FILES['epub']) && $_FILES['epub']['error'] === UPLOAD_ERR_OK) {
            $upload_dir_book = __DIR__ . '/../../../../public/uploads/books/';
            if (!file_exists($upload_dir_book)) {
                mkdir($upload_dir_book, 0777, true);
            }
            $book_filename = time() . '_' . basename($_FILES['epub']['name']);
            if (move_uploaded_file($_FILES['epub']['tmp_name'], $upload_dir_book . $book_filename)) {
                $file_path = 'uploads/books/' . $book_filename;
            }
        }

        // Lưu vào CSDL
        if (isset($pdo)) {
            try {
                $sql = "INSERT INTO books 
                        (publisher_id, category_id, title, slug, author, description, isbn, publish_year, list_price, digital_price, cover_path, file_path, allow_activation_code, allow_rental, status, created_at) 
                        VALUES 
                        (:publisher_id, :category_id, :title, :slug, :author, :description, :isbn, :publish_year, :list_price, :digital_price, :cover_path, :file_path, :allow_activation_code, :allow_rental, 'pending', NOW())";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':publisher_id'           => $publisher_id,
                    ':category_id'            => $category_id,
                    ':title'                  => $title,
                    ':slug'                   => $slug,
                    ':author'                 => $author,
                    ':description'            => $description,
                    ':isbn'                   => $isbn,
                    ':publish_year'           => $publish_year,
                    ':list_price'             => $list_price,
                    ':digital_price'          => $digital_price,
                    ':cover_path'             => $cover_path,
                    ':file_path'              => $file_path,
                    ':allow_activation_code'  => $allow_activation_code,
                    ':allow_rental'           => $allow_rental,
                ]);

                $message = "🎉 Đã thêm thành công cuốn sách '" . htmlspecialchars($title) . "' vào Cơ sở dữ liệu!";
                $messageType = "success";
            } catch (PDOException $e) {
                $message = "Lỗi lưu CSDL: " . $e->getMessage();
                $messageType = "error";
            }
        } else {
            $message = "Chưa kết nối được CSDL: " . ($dbError ?? '');
            $messageType = "error";
        }
    } else {
        $message = "Vui lòng nhập đầy đủ Tên sách và Tác giả!";
        $messageType = "warning";
    }
}
?>

<?php if (!empty($message)): ?>
    <div style="padding: 14px 20px; margin-bottom: 20px; border-radius: 8px; font-weight: 600; 
        background-color: <?= $messageType === 'success' ? '#d1fae5' : ($messageType === 'warning' ? '#fef3c7' : '#fee2e2') ?>; 
        color: <?= $messageType === 'success' ? '#065f46' : ($messageType === 'warning' ? '#92400e' : '#991b1b') ?>;
        border: 1px solid <?= $messageType === 'success' ? '#a7f3d0' : ($messageType === 'warning' ? '#fde68a' : '#fecaca') ?>;">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<form class="book-form panel" method="post" action="/publisher-dashboard?page=add-book" enctype="multipart/form-data">
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