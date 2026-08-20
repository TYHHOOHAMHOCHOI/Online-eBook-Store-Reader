<?php
if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

$booksData = [
    [
        'title' => 'Nhà giả kim',
        'author' => 'Paulo Coelho',
        'price' => '89.000đ',
        'rating' => '4.8',
        'readers' => '12.5K',
        'coverColor' => 'cover-alchemist',
        'publisher' => 'NXB Hội Nhà Văn',
        'year' => '2020',
        'pages' => '228',
        'description' => 'Nhà giả kim (The Alchemist) kể về chuyến phiêu lưu kỳ diệu của Santiago — một cậu bé chăn cừu người Tây Ban Nha — trong hành trình theo đuổi giấc mơ tìm kho báu ở Kim tự tháp Ai Cập. Qua câu chuyện giản dị mà sâu sắc, Paulo Coelho gửi gắm thông điệp rằng: khi bạn thực sự khao khát điều gì, cả vũ trụ sẽ hợp lực giúp bạn đạt được nó.'
    ],
    [
        'title' => 'Cây cam ngọt của tôi',
        'author' => 'José Mauro de Vasconcelos',
        'price' => '95.000đ',
        'rating' => '4.9',
        'readers' => '18.2K',
        'coverColor' => 'cover-orange-tree',
        'publisher' => 'NXB Hội Nhà Văn',
        'year' => '2021',
        'pages' => '244',
        'description' => 'Câu chuyện về cậu bé Zezé 5 tuổi sống trong một gia đình nghèo tại Brazil. Với trí tưởng tượng phong phú, Zezé biến cây cam ngọt trong vườn thành người bạn tri kỷ. Cuốn sách là hành trình khám phá thế giới đầy yêu thương và nước mắt qua đôi mắt trẻ thơ.'
    ],
    [
        'title' => 'Tư duy nhanh và chậm',
        'author' => 'Daniel Kahneman',
        'price' => '159.000đ',
        'rating' => '4.7',
        'readers' => '8.9K',
        'coverColor' => 'cover-thinking',
        'publisher' => 'NXB Thế Giới',
        'year' => '2019',
        'pages' => '568',
        'description' => 'Daniel Kahneman — nhà tâm lý học đoạt giải Nobel Kinh tế — giải thích hai hệ thống tư duy chi phối mọi quyết định của con người: Hệ thống 1 (nhanh, trực giác) và Hệ thống 2 (chậm, logic). Cuốn sách thay đổi cách bạn nhìn nhận về suy nghĩ và hành vi của chính mình.'
    ],
    [
        'title' => 'Ikigai',
        'author' => 'Héctor García',
        'price' => '79.000đ',
        'rating' => '4.6',
        'readers' => '15.3K',
        'coverColor' => 'cover-ikigai',
        'publisher' => 'NXB Lao Động',
        'year' => '2022',
        'pages' => '196',
        'description' => 'Ikigai — bí mật sống trường thọ và hạnh phúc của người Nhật. Cuốn sách khám phá triết lý Ikigai thông qua lối sống của cư dân vùng Okinawa — nơi có tỷ lệ người sống trên 100 tuổi cao nhất thế giới. Tìm ra lý do để thức dậy mỗi sáng chính là chìa khóa của một cuộc đời viên mãn.'
    ]
];

$bookId = (int)($_GET['book'] ?? 0);
if (!isset($booksData[$bookId])) {
    $bookId = 0;
}
$book = $booksData[$bookId];

$relatedBooks = [
    ['title' => 'Alchemist Tales',   'author' => 'Various Authors', 'price' => '85.000đ', 'rating' => 4.6, 'sold' => '8.3K',  'coverBg' => 'background: linear-gradient(135deg,#f59e0b,#b45309)'],
    ['title' => 'Tuổi thơ dữ dội',  'author' => 'Phùng Quán',      'price' => '69.000đ', 'rating' => 4.8, 'sold' => '12.1K', 'coverBg' => 'background: linear-gradient(135deg,#10b981,#065f46)'],
    ['title' => 'Nghệ thuật tư duy', 'author' => 'Rolf Dobelli',    'price' => '129.000đ','rating' => 4.7, 'sold' => '6.7K',  'coverBg' => 'background: linear-gradient(135deg,#3b82f6,#1e1b4b)'],
    ['title' => 'Life Purpose Guide','author' => 'Richard Leider',  'price' => '95.000đ', 'rating' => 4.5, 'sold' => '9.2K',  'coverBg' => 'background: linear-gradient(135deg,#ec4899,#9f1239)'],
    ['title' => 'Đắc nhân tâm',      'author' => 'Dale Carnegie',   'price' => '79.000đ', 'rating' => 4.9, 'sold' => '45.8K', 'coverBg' => 'background: linear-gradient(135deg,#6b7280,#111827)'],
];
?>

<div class="page-wrapper">

    <a href="/home" class="back-link">
        <span>&#10094;</span>
        Quay lại
    </a>

    <div class="detail-grid">

        <!-- Cột bìa sách -->
        <div class="detail-cover-col">
            <div class="detail-cover-box <?= e($book['coverColor']) ?>">
                <div>
                    <h1><?= e($book['title']) ?></h1>
                    <p><?= e($book['author']) ?></p>
                </div>
            </div>

            <div class="detail-actions">
                <button class="btn-buy">
                    🛒 Mua ngay — <?= e($book['price']) ?>
                </button>
                <a href="/library?read=1" class="btn-read-trial">
                    📖 Đọc thử
                </a>
                <div class="btn-row">
                    <button class="btn-secondary">❤️ Yêu thích</button>
                    <button class="btn-secondary">🔗 Chia sẻ</button>
                </div>
            </div>
        </div>

        <!-- Cột thông tin -->
        <div class="detail-info-col">

            <h1 class="detail-book-title"><?= e($book['title']) ?></h1>
            <p class="detail-book-author">Tác giả: <?= e($book['author']) ?></p>

            <div class="rating-row">
                <span class="rating-star">★</span>
                <span class="rating-value"><?= e($book['rating']) ?></span>
                <span class="rating-count">(1.234 đánh giá)</span>
                <span class="rating-sep">•</span>
                <span class="rating-readers"><?= e($book['readers']) ?> độc giả</span>
            </div>

            <!-- Thông tin chi tiết -->
            <div class="detail-section">
                <h3 class="detail-section-title">Thông tin chi tiết</h3>
                <div class="info-table">
                    <div class="info-row">
                        <span class="info-label">Nhà xuất bản:</span>
                        <span class="info-value"><?= e($book['publisher']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Năm xuất bản:</span>
                        <span class="info-value"><?= e($book['year']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Số trang:</span>
                        <span class="info-value"><?= e($book['pages']) ?> trang</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Định dạng:</span>
                        <span class="info-value">eBook (PDF, EPUB)</span>
                    </div>
                </div>
            </div>

            <!-- Giới thiệu -->
            <div class="detail-section">
                <h3 class="detail-section-title">Giới thiệu sách</h3>
                <p class="detail-description"><?= e($book['description']) ?></p>
            </div>

            <!-- Đặc điểm nổi bật -->
            <div class="detail-section">
                <div class="features-box">
                    <h3 class="detail-section-title">Đặc điểm nổi bật</h3>
                    <ul class="features-list">
                        <li><span class="feature-check">✓</span><span>Đọc trên mọi thiết bị: điện thoại, máy tính bảng, máy tính</span></li>
                        <li><span class="feature-check">✓</span><span>Đánh dấu trang, ghi chú và tra cứu từ điển ngay trong sách</span></li>
                        <li><span class="feature-check">✓</span><span>Tải về để đọc offline, không cần kết nối internet</span></li>
                        <li><span class="feature-check">✓</span><span>Cập nhật và hỗ trợ miễn phí trọn đời</span></li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

    <!-- Sách liên quan -->
    <div class="related-section">
        <h3>Sách liên quan</h3>
        <div class="related-grid">
            <?php foreach ($relatedBooks as $ri => $rel): ?>
            <a href="/home?view=book-detail&book=<?= $ri % 4 ?>" class="related-card">
                <div class="related-cover" style="<?= $rel['coverBg'] ?>">
                    <div>
                        <h4><?= e($rel['title']) ?></h4>
                        <p><?= e($rel['author']) ?></p>
                    </div>
                </div>
                <div class="related-card-body">
                    <h4><?= e($rel['title']) ?></h4>
                    <p><?= e($rel['author']) ?></p>
                    <div class="related-price-row">
                        <span class="related-price"><?= e($rel['price']) ?></span>
                        <span class="related-rating">⭐ <?= e($rel['rating']) ?></span>
                    </div>
                    <div class="related-sold"><?= e($rel['sold']) ?> đã bán</div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

</div>