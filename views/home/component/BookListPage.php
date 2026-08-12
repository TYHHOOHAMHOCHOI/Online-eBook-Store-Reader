<?php

function e($value)
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$allBooks = [
    [
        'title' => 'Nhà giả kim',
        'author' => 'Paulo Coelho',
        'price' => '89.000đ',
        'rating' => 4.8,
        'sold' => '12.5K',
        'coverColor' => 'bg-gradient-to-br from-amber-600 to-orange-800'
    ],
    [
        'title' => 'Cây cam ngọt của tôi',
        'author' => 'José Mauro',
        'price' => '95.000đ',
        'rating' => 4.9,
        'sold' => '18.2K',
        'coverColor' => 'bg-gradient-to-br from-green-600 to-emerald-800'
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
    ],
    [
        'title' => 'Bí mật tối thượng',
        'author' => 'Rhonda Byrne',
        'originalPrice' => '149.000đ',
        'salePrice' => '119.000đ',
        'rating' => 4.8,
        'sold' => '25K',
        'coverColor' => 'bg-gradient-to-br from-purple-600 to-purple-900'
    ],
    [
        'title' => 'Bước chậm lại',
        'author' => 'Haemin Sunim',
        'originalPrice' => '99.000đ',
        'salePrice' => '79.000đ',
        'rating' => 4.9,
        'sold' => '22K',
        'coverColor' => 'bg-gradient-to-br from-teal-500 to-teal-800'
    ],
    [
        'title' => 'Tuổi trẻ đáng giá',
        'author' => 'Rosie Nguyễn',
        'originalPrice' => '89.000đ',
        'salePrice' => '69.000đ',
        'rating' => 4.7,
        'sold' => '19K',
        'coverColor' => 'bg-gradient-to-br from-rose-500 to-rose-800'
    ],
    [
        'title' => 'Tâm lý học về tiền',
        'author' => 'Morgan Housel',
        'originalPrice' => '159.000đ',
        'salePrice' => '139.000đ',
        'rating' => 4.8,
        'sold' => '17K',
        'coverColor' => 'bg-gradient-to-br from-emerald-600 to-emerald-900'
    ],
    [
        'title' => 'Đi tìm lẽ sống',
        'author' => 'Viktor Frankl',
        'originalPrice' => '119.000đ',
        'salePrice' => '99.000đ',
        'rating' => 4.9,
        'sold' => '16K',
        'coverColor' => 'bg-gradient-to-br from-slate-600 to-slate-900'
    ],
    [
        'title' => 'Henry Ford',
        'author' => 'Steven Watts',
        'price' => '129.000đ',
        'rating' => 4.5,
        'sold' => '3.2K',
        'coverColor' => 'bg-gradient-to-br from-gray-700 to-gray-900'
    ],
    [
        'title' => '10 bước về hội họa',
        'author' => 'Lê Minh Quốc',
        'originalPrice' => '159.000đ',
        'salePrice' => '119.000đ',
        'rating' => 4.6,
        'sold' => '5.8K',
        'coverColor' => 'bg-gradient-to-br from-cyan-600 to-blue-800'
    ],
    [
        'title' => 'Chuyện lon xon',
        'author' => 'Nhiều tác giả',
        'price' => '69.000đ',
        'rating' => 4.7,
        'sold' => '7.1K',
        'coverColor' => 'bg-gradient-to-br from-yellow-500 to-orange-700'
    ]
];

$categories = [
    'Văn học',
    'Kinh tế',
    'Tâm lý',
    'Kỹ năng sống'
];

$prices = [
    'Dưới 50k',
    '50k - 100k',
    '100k - 150k',
    'Trên 150k'
];

$ratings = [
    '5★',
    '4★ trở lên',
    '3★ trở lên'
];

?>

<div class="max-w-[1280px] mx-auto px-8 py-10">

    <!-- Quay lại -->
    <button
        onclick="history.back()"
        class="flex items-center gap-2 text-[#087E8B] hover:text-[#075A64] mb-6 text-sm"
    >
        <span class="inline-block rotate-180">➜</span>
        Quay lại
    </button>


    <!-- Title -->
    <div class="mb-8">

        <h1 class="text-3xl font-bold text-[#102A43] mb-2">
            Danh sách sách
        </h1>

        <p class="text-gray-600">
            Hiển thị <?= count($allBooks) ?> cuốn sách
        </p>

    </div>


    <div class="flex gap-6">

        <!-- Filters Sidebar -->
        <div class="w-64 flex-shrink-0">

            <div class="bg-white rounded-lg border border-gray-200 p-4">

                <h3 class="font-semibold text-[#102A43] mb-4 flex items-center gap-2">
                    ⚙️
                    Bộ lọc
                </h3>


                <div class="space-y-4">

                    <!-- Thể loại -->
                    <div>

                        <label class="text-sm font-medium text-gray-700 mb-2 block">
                            Thể loại
                        </label>

                        <div class="space-y-2">

                            <?php foreach ($categories as $category): ?>

                                <label class="flex items-center gap-2 text-sm">

                                    <input
                                        type="checkbox"
                                        name="category[]"
                                        value="<?= e($category) ?>"
                                        class="rounded"
                                    >

                                    <span>
                                        <?= e($category) ?>
                                    </span>

                                </label>

                            <?php endforeach; ?>

                        </div>

                    </div>


                    <!-- Giá -->
                    <div class="border-t pt-4">

                        <label class="text-sm font-medium text-gray-700 mb-2 block">
                            Giá
                        </label>

                        <div class="space-y-2">

                            <?php foreach ($prices as $price): ?>

                                <label class="flex items-center gap-2 text-sm">

                                    <input
                                        type="checkbox"
                                        name="price[]"
                                        value="<?= e($price) ?>"
                                        class="rounded"
                                    >

                                    <span>
                                        <?= e($price) ?>
                                    </span>

                                </label>

                            <?php endforeach; ?>

                        </div>

                    </div>


                    <!-- Đánh giá -->
                    <div class="border-t pt-4">

                        <label class="text-sm font-medium text-gray-700 mb-2 block">
                            Đánh giá
                        </label>

                        <div class="space-y-2">

                            <?php foreach ($ratings as $rating): ?>

                                <label class="flex items-center gap-2 text-sm">

                                    <input
                                        type="checkbox"
                                        name="rating[]"
                                        value="<?= e($rating) ?>"
                                        class="rounded"
                                    >

                                    <span>
                                        <?= e($rating) ?>
                                    </span>

                                </label>

                            <?php endforeach; ?>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Book Grid -->
        <div class="flex-1">

            <div class="grid grid-cols-4 gap-4">

                <?php foreach ($allBooks as $index => $book): ?>

                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition">

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

                                <div>

                                    <?php if (isset($book['salePrice'])): ?>

                                        <span class="text-[#087E8B] font-bold">
                                            <?= e($book['salePrice']) ?>
                                        </span>

                                        <span class="text-xs text-gray-400 line-through ml-1">
                                            <?= e($book['originalPrice']) ?>
                                        </span>

                                    <?php else: ?>

                                        <span class="text-[#087E8B] font-bold">
                                            <?= e($book['price']) ?>
                                        </span>

                                    <?php endif; ?>

                                </div>

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


            <!-- Pagination -->
            <div class="flex justify-center items-center gap-2 mt-8">

                <button
                    class="px-3 py-2 border border-gray-300 rounded-lg hover:border-[#087E8B] text-sm"
                >
                    Trước
                </button>

                <button
                    class="px-3 py-2 bg-[#087E8B] text-white rounded-lg text-sm"
                >
                    1
                </button>

                <button
                    class="px-3 py-2 border border-gray-300 rounded-lg hover:border-[#087E8B] text-sm"
                >
                    2
                </button>

                <button
                    class="px-3 py-2 border border-gray-300 rounded-lg hover:border-[#087E8B] text-sm"
                >
                    3
                </button>

                <button
                    class="px-3 py-2 border border-gray-300 rounded-lg hover:border-[#087E8B] text-sm"
                >
                    Sau
                </button>

            </div>

        </div>

    </div>

</div>