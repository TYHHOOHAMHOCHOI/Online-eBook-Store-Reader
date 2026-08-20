<?php
if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

$allBooks = [
    ['title' => 'Nhà giả kim',              'author' => 'Paulo Coelho',      'price' => '89.000đ',  'rating' => 4.8, 'sold' => '12.5K', 'coverBg' => 'background:linear-gradient(135deg,#d97706,#9a3412)'],
    ['title' => 'Cây cam ngọt của tôi',     'author' => 'José Mauro',        'price' => '95.000đ',  'rating' => 4.9, 'sold' => '18.2K', 'coverBg' => 'background:linear-gradient(135deg,#16a34a,#065f46)'],
    ['title' => 'Tư duy nhanh và chậm',     'author' => 'Daniel Kahneman',   'price' => '159.000đ', 'rating' => 4.7, 'sold' => '8.9K',  'coverBg' => 'background:linear-gradient(135deg,#1d4ed8,#312e81)'],
    ['title' => 'Ikigai',                    'author' => 'Héctor García',     'price' => '79.000đ',  'rating' => 4.6, 'sold' => '15.3K', 'coverBg' => 'background:linear-gradient(135deg,#ef4444,#be185d)'],
    ['title' => 'Bí mật tối thượng',        'author' => 'Rhonda Byrne',      'salePrice' => '119.000đ', 'originalPrice' => '149.000đ', 'rating' => 4.8, 'sold' => '25K',   'coverBg' => 'background:linear-gradient(135deg,#9333ea,#581c87)'],
    ['title' => 'Bước chậm lại',            'author' => 'Haemin Sunim',      'salePrice' => '79.000đ',  'originalPrice' => '99.000đ',  'rating' => 4.9, 'sold' => '22K',   'coverBg' => 'background:linear-gradient(135deg,#14b8a6,#115e59)'],
    ['title' => 'Tuổi trẻ đáng giá',        'author' => 'Rosie Nguyễn',     'salePrice' => '69.000đ',  'originalPrice' => '89.000đ',  'rating' => 4.7, 'sold' => '19K',   'coverBg' => 'background:linear-gradient(135deg,#f43f5e,#9f1239)'],
    ['title' => 'Tâm lý học về tiền',       'author' => 'Morgan Housel',     'salePrice' => '139.000đ', 'originalPrice' => '159.000đ', 'rating' => 4.8, 'sold' => '17K',   'coverBg' => 'background:linear-gradient(135deg,#059669,#064e3b)'],
    ['title' => 'Đi tìm lẽ sống',           'author' => 'Viktor Frankl',     'salePrice' => '99.000đ',  'originalPrice' => '119.000đ', 'rating' => 4.9, 'sold' => '16K',   'coverBg' => 'background:linear-gradient(135deg,#475569,#0f172a)'],
    ['title' => 'Henry Ford',               'author' => 'Steven Watts',      'price' => '129.000đ', 'rating' => 4.5, 'sold' => '3.2K',  'coverBg' => 'background:linear-gradient(135deg,#374151,#111827)'],
    ['title' => '10 bước về hội họa',       'author' => 'Lê Minh Quốc',     'salePrice' => '119.000đ', 'originalPrice' => '159.000đ', 'rating' => 4.6, 'sold' => '5.8K',  'coverBg' => 'background:linear-gradient(135deg,#0891b2,#1e40af)'],
    ['title' => 'Chuyện lon xon',           'author' => 'Nhiều tác giả',    'price' => '69.000đ',  'rating' => 4.7, 'sold' => '7.1K',  'coverBg' => 'background:linear-gradient(135deg,#eab308,#c2410c)'],
];

$categories = ['Văn học', 'Kinh tế', 'Tâm lý', 'Kỹ năng sống'];
$prices     = ['Dưới 50k', '50k - 100k', '100k - 150k', 'Trên 150k'];
$ratings    = ['5★', '4★ trở lên', '3★ trở lên'];
?>

<div class="page-wrapper">

    <a href="/home" class="back-link">
        <span>&#10094;</span>
        Quay lại
    </a>

    <div style="margin-bottom: 32px;">
        <h1 class="page-title">Danh sách sách</h1>
        <p class="page-subtitle">Hiển thị <?= count($allBooks) ?> cuốn sách</p>
    </div>

    <div class="list-layout">

        <!-- Sidebar lọc -->
        <aside class="filter-sidebar">
            <div class="filter-box">
                <h3 class="filter-box-title">⚙️ Bộ lọc</h3>

                <!-- Thể loại -->
                <div>
                    <span class="filter-label">Thể loại</span>
                    <?php foreach ($categories as $cat): ?>
                    <label class="filter-option">
                        <input type="checkbox" name="category[]" value="<?= e($cat) ?>">
                        <span><?= e($cat) ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>

                <!-- Giá -->
                <div class="filter-group">
                    <span class="filter-label">Giá</span>
                    <?php foreach ($prices as $pr): ?>
                    <label class="filter-option">
                        <input type="checkbox" name="price[]" value="<?= e($pr) ?>">
                        <span><?= e($pr) ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>

                <!-- Đánh giá -->
                <div class="filter-group">
                    <span class="filter-label">Đánh giá</span>
                    <?php foreach ($ratings as $rt): ?>
                    <label class="filter-option">
                        <input type="checkbox" name="rating[]" value="<?= e($rt) ?>">
                        <span><?= e($rt) ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </aside>

        <!-- Lưới sách -->
        <div class="booklist-area">
            <div class="booklist-grid">
                <?php foreach ($allBooks as $index => $book): ?>
                <a href="/home?view=book-detail&book=<?= $index % 4 ?>" class="booklist-card">
                    <div class="booklist-cover" style="<?= $book['coverBg'] ?>">
                        <div>
                            <h2><?= e($book['title']) ?></h2>
                            <p><?= e($book['author']) ?></p>
                        </div>
                    </div>
                    <div class="booklist-card-body">
                        <h3><?= e($book['title']) ?></h3>
                        <p class="card-author"><?= e($book['author']) ?></p>
                        <div class="card-price-row">
                            <div>
                                <?php if (isset($book['salePrice'])): ?>
                                    <span class="card-price"><?= e($book['salePrice']) ?></span>
                                    <span class="card-original-price"><?= e($book['originalPrice']) ?></span>
                                <?php else: ?>
                                    <span class="card-price"><?= e($book['price']) ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="card-rating">⭐ <?= e($book['rating']) ?></span>
                        </div>
                        <div class="card-sold"><?= e($book['sold']) ?> đã bán</div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>

            <!-- Phân trang -->
            <div class="pagination-bar">
                <button class="pag-btn">Trước</button>
                <button class="pag-btn active">1</button>
                <button class="pag-btn">2</button>
                <button class="pag-btn">3</button>
                <button class="pag-btn">Sau</button>
            </div>
        </div>

    </div>
</div>