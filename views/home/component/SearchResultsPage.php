<?php

function e($value)
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/*
 * Dữ liệu truyền từ trang/controller
 * Nếu chưa có thì dùng giá trị mặc định.
 */
$query = $query ?? '';

$filters = $filters ?? [
    'category' => null,
    'priceRange' => null,
    'rating' => null
];

$searchResults = [
    [
        'title' => 'Nhà giả kim',
        'author' => 'Paulo Coelho',
        'price' => '89.000đ',
        'rating' => 4.8,
        'sold' => '12.5K',
        'coverColor' => 'bg-gradient-to-br from-amber-600 to-orange-800'
    ],
    [
        'title' => 'Tư duy nhanh và chậm',
        'author' => 'Daniel Kahneman',
        'price' => '159.000đ',
        'rating' => 4.7,
        'sold' => '8.9K',
        'coverColor' => 'bg-gradient-to-br from-blue-700 to-indigo-900'
    ],
    [
        'title' => 'Ikigai',
        'author' => 'Héctor García',
        'price' => '79.000đ',
        'rating' => 4.6,
        'sold' => '15.3K',
        'coverColor' => 'bg-gradient-to-br from-red-500 to-pink-700'
    ]
];

?>

<div class="max-w-[1280px] mx-auto px-8 py-10">

    <!-- Tiêu đề -->
    <div class="mb-8">

        <h1 class="text-3xl font-bold text-[#102A43] mb-2">
            Kết quả tìm kiếm:
            "<?= e($query) ?>"
        </h1>

        <p class="text-gray-600">
            Tìm thấy <?= count($searchResults) ?> kết quả
        </p>

    </div>


    <!-- Bộ lọc đang sử dụng -->
    <?php
    $hasFilters =
        !empty($filters['category']) ||
        !empty($filters['priceRange']) ||
        !empty($filters['rating']);
    ?>

    <?php if ($hasFilters): ?>

        <div class="mb-6 flex flex-wrap gap-2">

            <span class="text-sm text-gray-600">
                Đang lọc:
            </span>


            <!-- Category -->
            <?php if (!empty($filters['category'])): ?>

                <span
                    class="px-3 py-1 bg-[#D9F6F0] text-[#087E8B] rounded-full text-sm flex items-center gap-2"
                >

                    <?= e($filters['category']) ?>

                    <button
                        type="button"
                        class="cursor-pointer hover:text-[#075A64]"
                    >
                        ×
                    </button>

                </span>

            <?php endif; ?>


            <!-- Price -->
            <?php if (!empty($filters['priceRange'])): ?>

                <span
                    class="px-3 py-1 bg-[#D9F6F0] text-[#087E8B] rounded-full text-sm flex items-center gap-2"
                >

                    <?= e($filters['priceRange']) ?>

                    <button
                        type="button"
                        class="cursor-pointer hover:text-[#075A64]"
                    >
                        ×
                    </button>

                </span>

            <?php endif; ?>


            <!-- Rating -->
            <?php if (!empty($filters['rating'])): ?>

                <span
                    class="px-3 py-1 bg-[#D9F6F0] text-[#087E8B] rounded-full text-sm flex items-center gap-2"
                >

                    <?= e($filters['rating']) ?>★+

                    <button
                        type="button"
                        class="cursor-pointer hover:text-[#075A64]"
                    >
                        ×
                    </button>

                </span>

            <?php endif; ?>

        </div>

    <?php endif; ?>


    <!-- Search Results -->
    <div class="grid grid-cols-4 gap-5">

        <?php foreach ($searchResults as $index => $book): ?>

            <div
                class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition"
            >

                <!-- Book Cover -->
                <div
                    class="<?= e($book['coverColor']) ?> h-64 flex items-center justify-center p-5"
                >

                    <div class="text-white text-center">

                        <h2 class="text-xl font-bold">
                            <?= e($book['title']) ?>
                        </h2>

                        <p class="text-sm opacity-80 mt-2">
                            <?= e($book['author']) ?>
                        </p>

                    </div>

                </div>


                <!-- Book Info -->
                <div class="p-4">

                    <h3 class="font-semibold text-[#102A43] mb-1">
                        <?= e($book['title']) ?>
                    </h3>

                    <p class="text-sm text-gray-500">
                        <?= e($book['author']) ?>
                    </p>


                    <div class="flex items-center justify-between mt-3">

                        <span class="text-[#087E8B] font-bold">
                            <?= e($book['price']) ?>
                        </span>

                        <span class="text-sm text-gray-500">
                            ⭐ <?= e($book['rating']) ?>
                        </span>

                    </div>


                    <div class="text-xs text-gray-400 mt-2">
                        <?= e($book['sold']) ?> đã bán
                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>