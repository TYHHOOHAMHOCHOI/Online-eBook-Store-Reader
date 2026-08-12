<?php

$books = [
    [
        "title" => "Nhà giả kim",
        "author" => "Paulo Coelho",
        "badge" => "Bán chạy",
        "badgeColor" => "bg-[#FF5A5F]",
        "price" => "89.000đ",
        "rating" => "4.8",
        "readers" => "12.5K",
        "coverColor" => "bg-gradient-to-br from-amber-600 to-orange-800"
    ],
    [
        "title" => "Cây cam ngọt của tôi",
        "author" => "José Mauro de Vasconcelos",
        "badge" => "Yêu thích",
        "badgeColor" => "bg-pink-500",
        "price" => "95.000đ",
        "rating" => "4.9",
        "readers" => "18.2K",
        "coverColor" => "bg-gradient-to-br from-green-600 to-emerald-800"
    ],
    [
        "title" => "Tư duy nhanh và chậm",
        "author" => "Daniel Kahneman",
        "badge" => "Top 10",
        "badgeColor" => "bg-[#087E8B]",
        "price" => "159.000đ",
        "rating" => "4.7",
        "readers" => "8.9K",
        "coverColor" => "bg-gradient-to-br from-blue-700 to-indigo-900"
    ],
    [
        "title" => "Ikigai",
        "author" => "Héctor García",
        "badge" => "Mới",
        "badgeColor" => "bg-[#FFCA3A] text-[#102A43]",
        "price" => "79.000đ",
        "rating" => "4.6",
        "readers" => "15.3K",
        "coverColor" => "bg-gradient-to-br from-red-500 to-pink-700"
    ]
];

foreach ($books as $book) {
?>

<div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow overflow-hidden cursor-pointer">

    <div class="relative">

        <div class="<?= $book['coverColor']; ?> h-52 flex items-center justify-center p-4 relative">

            <div class="text-white text-center">

                <div class="text-lg font-bold mb-2">
                    <?= $book['title']; ?>
                </div>

                <div class="text-xs opacity-80">
                    <?= $book['author']; ?>
                </div>

            </div>

            <div class="absolute top-2 right-2 <?= $book['badgeColor']; ?> text-white text-xs px-2 py-1 rounded-full">
                <?= $book['badge']; ?>
            </div>

        </div>

    </div>

    <div class="p-3">

        <h3 class="text-[#102A43] font-semibold text-sm mb-1">
            <?= $book['title']; ?>
        </h3>

        <p class="text-gray-600 text-xs mb-2">
            <?= $book['author']; ?>
        </p>

        <div class="flex items-center gap-2 mb-2">

            <div class="flex items-center gap-1">

                <span>⭐</span>

                <span class="text-xs font-semibold">
                    <?= $book['rating']; ?>
                </span>

            </div>

            <span class="text-gray-400 text-xs">
                • <?= $book['readers']; ?> đã đọc
            </span>

        </div>

        <div class="text-[#087E8B] font-bold">
            <?= $book['price']; ?>
        </div>

    </div>

</div>

<?php
}
?>