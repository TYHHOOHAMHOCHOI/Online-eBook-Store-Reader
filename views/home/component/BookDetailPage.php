<?php
function e($value)
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
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

    <div class="grid grid-cols-3 gap-8">

        <!-- Book Cover -->
        <div>

            <div class="<?= e($book['coverColor']) ?> rounded-2xl shadow-2xl p-8 h-96 flex items-center justify-center">

                <div class="text-white text-center">

                    <h1 class="text-3xl font-bold mb-3">
                        <?= e($book['title']) ?>
                    </h1>

                    <p class="text-lg opacity-80">
                        <?= e($book['author']) ?>
                    </p>

                </div>

            </div>

            <div class="mt-6 space-y-3">

                <!-- Mua ngay -->
                <button
                    class="w-full px-6 py-3 bg-[#FFCA3A] text-[#102A43] rounded-lg hover:bg-[#FFD865] flex items-center justify-center gap-2 font-semibold"
                >
                    🛒
                    Mua ngay - <?= e($book['price']) ?>
                </button>

                <!-- Đọc thử -->
                <button
                    class="w-full px-6 py-3 bg-[#087E8B] text-white rounded-lg hover:bg-[#075A64] flex items-center justify-center gap-2"
                >
                    📖
                    Đọc thử
                </button>

                <div class="flex gap-3">

                    <!-- Yêu thích -->
                    <button
                        class="flex-1 px-4 py-2 border-2 border-gray-300 rounded-lg hover:border-[#087E8B] flex items-center justify-center gap-2 text-sm"
                    >
                        ❤️
                        Yêu thích
                    </button>

                    <!-- Chia sẻ -->
                    <button
                        class="flex-1 px-4 py-2 border-2 border-gray-300 rounded-lg hover:border-[#087E8B] flex items-center justify-center gap-2 text-sm"
                    >
                        🔗
                        Chia sẻ
                    </button>

                </div>

            </div>
        </div>


        <!-- Book Info -->
        <div class="col-span-2">

            <h1 class="text-3xl font-bold text-[#102A43] mb-2">
                <?= e($book['title']) ?>
            </h1>

            <p class="text-lg text-gray-600 mb-4">
                Tác giả: <?= e($book['author']) ?>
            </p>


            <!-- Rating -->
            <div class="flex items-center gap-6 mb-6 pb-6 border-b border-gray-200">

                <div class="flex items-center gap-2">

                    <span class="text-xl text-[#FFCA3A]">
                        ★
                    </span>

                    <span class="text-lg font-semibold">
                        <?= e($book['rating']) ?>
                    </span>

                    <span class="text-gray-500 text-sm">
                        (1.234 đánh giá)
                    </span>

                </div>

                <div class="text-gray-500">
                    •
                </div>

                <div class="text-gray-600">
                    <?= e($book['readers']) ?> độc giả
                </div>

            </div>


            <!-- Thông tin chi tiết -->
            <div class="mb-6">

                <h3 class="font-semibold text-[#102A43] mb-3">
                    Thông tin chi tiết
                </h3>

                <div class="grid grid-cols-2 gap-3 text-sm">

                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">
                            Nhà xuất bản:
                        </span>

                        <span class="font-medium">
                            <?= e($book['publisher']) ?>
                        </span>
                    </div>


                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">
                            Năm xuất bản:
                        </span>

                        <span class="font-medium">
                            <?= e($book['year']) ?>
                        </span>
                    </div>


                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">
                            Số trang:
                        </span>

                        <span class="font-medium">
                            <?= e($book['pages']) ?> trang
                        </span>
                    </div>


                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">
                            Định dạng:
                        </span>

                        <span class="font-medium">
                            eBook (PDF, EPUB)
                        </span>
                    </div>

                </div>
            </div>


            <!-- Giới thiệu -->
            <div class="mb-6">

                <h3 class="font-semibold text-[#102A43] mb-3">
                    Giới thiệu sách
                </h3>

                <p class="text-gray-700 leading-relaxed">
                    <?= e($book['description']) ?>
                </p>

            </div>


            <!-- Đặc điểm -->
            <div class="bg-[#F5FBFA] rounded-lg p-6">

                <h3 class="font-semibold text-[#102A43] mb-3">
                    Đặc điểm nổi bật
                </h3>

                <ul class="space-y-2 text-sm text-gray-700">

                    <li class="flex items-start gap-2">
                        <span class="text-[#087E8B] mt-1">✓</span>
                        <span>
                            Đọc trên mọi thiết bị: điện thoại, máy tính bảng, máy tính
                        </span>
                    </li>

                    <li class="flex items-start gap-2">
                        <span class="text-[#087E8B] mt-1">✓</span>
                        <span>
                            Đánh dấu trang, ghi chú và tra cứu từ điển ngay trong sách
                        </span>
                    </li>

                    <li class="flex items-start gap-2">
                        <span class="text-[#087E8B] mt-1">✓</span>
                        <span>
                            Tải về để đọc offline, không cần kết nối internet
                        </span>
                    </li>

                    <li class="flex items-start gap-2">
                        <span class="text-[#087E8B] mt-1">✓</span>
                        <span>
                            Cập nhật và hỗ trợ miễn phí trọn đời
                        </span>
                    </li>

                </ul>

            </div>

        </div>

    </div>


    <!-- Sách liên quan -->
    <div class="mt-16">

        <h3 class="text-2xl font-bold text-[#102A43] mb-6">
            Sách liên quan
        </h3>

        <div class="grid grid-cols-5 gap-4">

            <?php
            $relatedBooks = [
                [
                    'title' => 'Alchemist Tales',
                    'author' => 'Various Authors',
                    'price' => '85.000đ',
                    'rating' => 4.6,
                    'sold' => '8.3K',
                    'coverColor' => 'bg-gradient-to-br from-amber-500 to-orange-700'
                ],
                [
                    'title' => 'Tuổi thơ dữ dội',
                    'author' => 'Phùng Quán',
                    'price' => '69.000đ',
                    'rating' => 4.8,
                    'sold' => '12.1K',
                    'coverColor' => 'bg-gradient-to-br from-green-500 to-teal-700'
                ],
                [
                    'title' => 'Nghệ thuật tư duy',
                    'author' => 'Rolf Dobelli',
                    'price' => '129.000đ',
                    'rating' => 4.7,
                    'sold' => '6.7K',
                    'coverColor' => 'bg-gradient-to-br from-blue-600 to-indigo-800'
                ],
                [
                    'title' => 'Life Purpose Guide',
                    'author' => 'Richard Leider',
                    'price' => '95.000đ',
                    'rating' => 4.5,
                    'sold' => '9.2K',
                    'coverColor' => 'bg-gradient-to-br from-pink-500 to-rose-700'
                ],
                [
                    'title' => 'Đắc nhân tâm',
                    'author' => 'Dale Carnegie',
                    'price' => '79.000đ',
                    'rating' => 4.9,
                    'sold' => '45.8K',
                    'coverColor' => 'bg-gradient-to-br from-gray-600 to-slate-800'
                ]
            ];
            ?>

            <?php foreach ($relatedBooks as $relatedBook): ?>

                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition">

                    <div class="<?= e($relatedBook['coverColor']) ?> h-48 flex items-center justify-center p-4">

                        <div class="text-white text-center">

                            <h4 class="font-bold text-lg">
                                <?= e($relatedBook['title']) ?>
                            </h4>

                            <p class="text-sm opacity-80 mt-2">
                                <?= e($relatedBook['author']) ?>
                            </p>

                        </div>

                    </div>

                    <div class="p-3">

                        <h4 class="font-semibold text-[#102A43] text-sm">
                            <?= e($relatedBook['title']) ?>
                        </h4>

                        <p class="text-gray-500 text-xs mt-1">
                            <?= e($relatedBook['author']) ?>
                        </p>

                        <div class="flex items-center justify-between mt-3">

                            <span class="text-[#087E8B] font-semibold text-sm">
                                <?= e($relatedBook['price']) ?>
                            </span>

                            <span class="text-xs text-gray-500">
                                ⭐ <?= e($relatedBook['rating']) ?>
                            </span>

                        </div>

                        <div class="text-xs text-gray-400 mt-2">
                            <?= e($relatedBook['sold']) ?> đã bán
                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</div>