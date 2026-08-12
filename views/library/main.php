<?php
// 1. Mảng dữ liệu Sách
$books = [
    [ 'id' => 1, 'title' => 'Nhà giả kim', 'author' => 'Paulo Coelho', 'progress' => 68, 'color' => '#FFB84D' ],
    [ 'id' => 2, 'title' => 'Cây cam ngọt của tôi', 'author' => 'José Mauro', 'progress' => 42, 'color' => '#FF6B9D' ],
    [ 'id' => 3, 'title' => 'Tư duy nhanh và chậm', 'author' => 'Daniel Kahneman', 'progress' => 85, 'color' => '#4ECDC4' ],
    [ 'id' => 4, 'title' => 'Ikigai', 'author' => 'Héctor García', 'progress' => 23, 'color' => '#95E1D3' ],
    [ 'id' => 5, 'title' => 'Đắc nhân tâm', 'author' => 'Dale Carnegie', 'progress' => 56, 'color' => '#F38181' ],
    [ 'id' => 6, 'title' => 'Tuổi trẻ đáng giá bao nhiêu', 'author' => 'Rosie Nguyễn', 'progress' => 31, 'color' => '#AA96DA' ],
    [ 'id' => 7, 'title' => 'Sapiens', 'author' => 'Yuval Noah Harari', 'progress' => 74, 'color' => '#FCBAD3' ],
    [ 'id' => 8, 'title' => 'Atomic Habits', 'author' => 'James Clear', 'progress' => 19, 'color' => '#A8D8EA' ],
];

// 2. Mảng dữ liệu Hoạt động gần đây
$recentActivities = [
    [
        'id' => 1,
        'quote' => '"Khi bạn muốn một điều gì đó, cả vũ trụ sẽ hợp lực giúp bạn đạt được điều đó."',
        'book' => 'Nhà giả kim',
        'page' => 24,
        'highlightColor' => '#FFCA3A'
    ],
    [
        'id' => 2,
        'quote' => '"Hạnh phúc không phải là điều bạn tìm thấy ở cuối con đường, mà là chính con đường đó."',
        'book' => 'Ikigai',
        'page' => 87,
        'highlightColor' => '#FF5A5F'
    ],
    [
        'id' => 3,
        'quote' => '"Chúng ta không thể giải quyết vấn đề bằng cùng một cách suy nghĩ khi ta tạo ra nó."',
        'book' => 'Tư duy nhanh và chậm',
        'page' => 156,
        'highlightColor' => '#087E8B'
    ],
];

// 3. Xử lý logic hiển thị Trình đọc sách (BookReader)
if (isset($_GET['read'])) {
    $bookId = $_GET['read'];
    $selectedBook = null;
    
    foreach ($books as $book) {
        if ($book['id'] == $bookId) {
            $selectedBook = $book;
            break;
        }
    }
    
    if ($selectedBook) {
        include __DIR__ . '/component/BookReader.php';
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thư Viện - Readly</title>
    
    <!-- Nhúng CDN Tailwind CSS để nhận các class giao diện -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Nhúng file CSS tùy chỉnh theo phân công -->
    <link rel="stylesheet" href="/assets/css/library.css">
</head>
<body class="bg-gray-100 text-gray-800 antialiased">

    <!-- Giao diện Thư Viện chính -->
    <div class="min-h-screen bg-white" style="width: 1280px; margin: 0 auto; font-family: 'Inter', sans-serif;">
        
        <?php include __DIR__ . '/component/LibraryHeader.php'; ?>
        
        <?php include __DIR__ . '/component/LibraryOverview.php'; ?>
        
        <?php include __DIR__ . '/component/LibraryBooks.php'; ?>
        
        <?php include __DIR__ . '/component/RecentActivities.php'; ?>
        
        <?php include __DIR__ . '/component/LibraryFooter.php'; ?>
        
    </div>

</body>
</html>