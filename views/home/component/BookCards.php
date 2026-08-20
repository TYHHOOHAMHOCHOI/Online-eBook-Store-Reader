<?php

$books = [
    [
        "title" => "Nhà giả kim",
        "author" => "Paulo Coelho",
        "badge" => "Bán chạy",
        "badgeColor" => "badge-red",
        "price" => "89.000đ",
        "rating" => "4.8",
        "readers" => "12.5K",
        "coverColor" => "cover-alchemist"
    ],
    [
        "title" => "Cây cam ngọt của tôi",
        "author" => "José Mauro de Vasconcelos",
        "badge" => "Yêu thích",
        "badgeColor" => "badge-pink",
        "price" => "95.000đ",
        "rating" => "4.9",
        "readers" => "18.2K",
        "coverColor" => "cover-orange-tree"
    ],
    [
        "title" => "Tư duy nhanh và chậm",
        "author" => "Daniel Kahneman",
        "badge" => "Top 10",
        "badgeColor" => "badge-teal",
        "price" => "159.000đ",
        "rating" => "4.7",
        "readers" => "8.9K",
        "coverColor" => "cover-thinking"
    ],
    [
        "title" => "Ikigai",
        "author" => "Héctor García",
        "badge" => "Mới",
        "badgeColor" => "badge-yellow",
        "price" => "79.000đ",
        "rating" => "4.6",
        "readers" => "15.3K",
        "coverColor" => "cover-ikigai"
    ]
];

foreach ($books as $bookIndex => $book) {
?>

<a href="/home?view=book-detail&book=<?= $bookIndex; ?>" class="book-card favorite-card" style="display: block; text-decoration: none; color: inherit;">
    <div class="book-cover favorite-cover <?= $book['coverColor']; ?>">
        <div class="book-cover-content">
            <div class="favorite-cover-title">
                <?= $book['title']; ?>
            </div>
            <div class="favorite-cover-author">
                <?= $book['author']; ?>
            </div>
        </div>
        <div class="book-badge <?= $book['badgeColor']; ?>">
            <?= $book['badge']; ?>
        </div>
    </div>

    <div class="book-card-content">
        <h3><?= $book['title']; ?></h3>
        <p class="book-author"><?= $book['author']; ?></p>

        <div class="book-rating-row">
            <div class="book-rating">
                <svg class="star-icon" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                <span style="font-weight: 600;"><?= $book['rating']; ?></span>
            </div>
            <span class="book-readers">• <?= $book['readers']; ?> đã đọc</span>
        </div>

        <div class="book-price"><?= $book['price']; ?></div>
    </div>
</a>

<?php
}
?>