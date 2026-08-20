<?php
if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

$query = $_GET['q'] ?? '';

$filters = $filters ?? [
    'category'   => null,
    'priceRange' => null,
    'rating'     => null,
];

$searchResults = [
    ['title' => 'Nhà giả kim',          'author' => 'Paulo Coelho',    'price' => '89.000đ',  'rating' => 4.8, 'sold' => '12.5K', 'coverBg' => 'background:linear-gradient(135deg,#d97706,#9a3412)'],
    ['title' => 'Tư duy nhanh và chậm','author' => 'Daniel Kahneman', 'price' => '159.000đ', 'rating' => 4.7, 'sold' => '8.9K',  'coverBg' => 'background:linear-gradient(135deg,#1d4ed8,#312e81)'],
    ['title' => 'Ikigai',              'author' => 'Héctor García',   'price' => '79.000đ',  'rating' => 4.6, 'sold' => '15.3K', 'coverBg' => 'background:linear-gradient(135deg,#ef4444,#be185d)'],
];
?>

<div class="page-wrapper">

    <!-- Tiêu đề -->
    <div style="margin-bottom: 32px;">
        <h1 class="page-title">
            Kết quả tìm kiếm: "<?= e($query) ?>"
        </h1>
        <p class="page-subtitle">Tìm thấy <?= count($searchResults) ?> kết quả</p>
    </div>

    <!-- Bộ lọc đang dùng -->
    <?php
    $hasFilters = !empty($filters['category'])
               || !empty($filters['priceRange'])
               || !empty($filters['rating']);
    ?>
    <?php if ($hasFilters): ?>
    <div class="active-filters">
        <span class="active-filters-label">Đang lọc:</span>

        <?php if (!empty($filters['category'])): ?>
        <span class="filter-tag">
            <?= e($filters['category']) ?>
            <button type="button" class="filter-tag-remove">×</button>
        </span>
        <?php endif; ?>

        <?php if (!empty($filters['priceRange'])): ?>
        <span class="filter-tag">
            <?= e($filters['priceRange']) ?>
            <button type="button" class="filter-tag-remove">×</button>
        </span>
        <?php endif; ?>

        <?php if (!empty($filters['rating'])): ?>
        <span class="filter-tag">
            <?= e($filters['rating']) ?>★+
            <button type="button" class="filter-tag-remove">×</button>
        </span>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Kết quả tìm kiếm -->
    <div class="search-result-grid">
        <?php foreach ($searchResults as $index => $book): ?>
        <a href="/home?view=book-detail&book=<?= $index % 4 ?>" class="search-result-card">
            <div class="search-cover" style="<?= $book['coverBg'] ?>">
                <div>
                    <h2><?= e($book['title']) ?></h2>
                    <p><?= e($book['author']) ?></p>
                </div>
            </div>
            <div class="search-card-body">
                <h3><?= e($book['title']) ?></h3>
                <p class="card-author"><?= e($book['author']) ?></p>
                <div class="card-price-row">
                    <span class="card-price"><?= e($book['price']) ?></span>
                    <span class="card-rating">⭐ <?= e($book['rating']) ?></span>
                </div>
                <div class="card-sold"><?= e($book['sold']) ?> đã bán</div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

</div>