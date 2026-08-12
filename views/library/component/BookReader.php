<?php
$bookInfo = [
    'title' => isset($selectedBook) ? $selectedBook['title'] : 'Tên sách mặc định',
    'author' => isset($selectedBook) ? $selectedBook['author'] : 'Tác giả',
    'currentChapter' => 3,
];

$chapters = [
    ['id' => 1, 'title' => 'Phần một', 'page' => 1],
    ['id' => 2, 'title' => 'Phần hai', 'page' => 45],
    ['id' => 3, 'title' => 'Phần ba', 'page' => 89],
    ['id' => 4, 'title' => 'Phần bốn', 'page' => 134],
    ['id' => 5, 'title' => 'Phần năm', 'page' => 178],
    ['id' => 6, 'title' => 'Phần sáu', 'page' => 210],
];

$themes = [
    ['id' => 'white-black', 'name' => 'Trắng - Đen', 'bg' => '#FFFFFF', 'text' => '#000000'],
    ['id' => 'black-white', 'name' => 'Đen - Trắng', 'bg' => '#000000', 'text' => '#FFFFFF'],
    ['id' => 'sepia', 'name' => 'Vàng sách', 'bg' => '#F4ECD8', 'text' => '#5B4636'],
    ['id' => 'night', 'name' => 'Đêm', 'bg' => '#1A1A1A', 'text' => '#E0E0E0'],
    ['id' => 'green', 'name' => 'Xanh lá', 'bg' => '#E8F5E9', 'text' => '#1B5E20'],
];

$fonts = [
    ['id' => 'serif', 'name' => 'Serif', 'family' => 'Georgia, serif'],
    ['id' => 'sans', 'name' => 'Sans-serif', 'family' => 'Inter, sans-serif'],
    ['id' => 'palatino', 'name' => 'Palatino', 'family' => 'Palatino, serif'],
    ['id' => 'times', 'name' => 'Times New Roman', 'family' => 'Times New Roman, serif'],
];

$readingModes = [
    ['id' => 'scroll', 'name' => 'Cuộn dọc', 'icon' => '↕️'],
    ['id' => 'page', 'name' => 'Từng trang', 'icon' => '📄'],
    ['id' => 'horizontal', 'name' => 'Trượt ngang', 'icon' => '↔️'],
];

$sampleText = "Cậu bé tên là Santiago. Trời đã xế chiều khi đàn cừu đi đến một nhà thờ cổ kính nơi người chăn cừu từng nghỉ đêm. Mái nhà của nhà thờ từ lâu đã đổ sập, và có một cây sung lớn mọc lên chỗ thánh đường cũ.\n\nCậu quyết định sẽ dành đêm nay ở đấy. Cậu dồn hết đàn cừu vào cửa đổ nát, rồi xếp những tấm gỗ thành chướng ngại vật để ngăn không cho cừu đi ra ngoài trong đêm. Không có sói ở vùng này, nhưng một khi nào có con cừu đi lạc vào đêm tối, cậu sẽ phải mất cả buổi sáng hôm sau để tìm nó.\n\nCậu trải chiếc áo choàng trên nền đất và nằm xuống, dùng cuốn sách mình vừa đọc dở làm gối. Trước khi ngủ, cậu nghĩ rằng mình cần phải đọc thêm vài cuốn sách dày hơn. Dùng sách làm gối sẽ thoải mái hơn nhiều.";

// Cài đặt mặc định ban đầu
$currentTheme = $themes[0];
$currentFont = $fonts[0];
$fontSize = 18;
?>

<!-- 2. Giao diện HTML (Đã xóa các thẻ React và bọc class chuẩn) -->
<div id="reader-container" class="relative w-full h-screen overflow-hidden" style="background-color: <?= $currentTheme['bg'] ?>; color: <?= $currentTheme['text'] ?>;">
    
    <!-- Nội dung đọc sách -->
    <div class="h-full overflow-y-auto pb-24 px-8 pt-12" style="max-width: 800px; margin: 0 auto;">
        
        <!-- Header sách -->
        <div class="mb-8 pb-4 border-b" style="border-color: rgba(0,0,0,0.1);">
            <h1 class="text-2xl mb-1" style="font-weight: 700; font-family: '<?= $currentFont['family'] ?>';">
                <?= $bookInfo['title'] ?>
            </h1>
            <p class="text-sm opacity-60" style="font-family: '<?= $currentFont['family'] ?>';">
                <?= $bookInfo['author'] ?>
            </p>
        </div>

        <!-- Tiêu đề chương -->
        <h2 class="text-xl mb-6" style="font-weight: 600; font-family: '<?= $currentFont['family'] ?>';">
            Phần ba
        </h2>

        <!-- Khung văn bản -->
        <div id="reading-text" class="leading-relaxed whitespace-pre-line" style="font-size: <?= $fontSize ?>px; font-family: '<?= $currentFont['family'] ?>'; line-height: 1.8;">
            <?= $sampleText ?>
        </div>
    </div>

    <!-- Thanh Taskbar ở dưới cùng -->
    <div class="fixed bottom-0 left-0 right-0 shadow-lg border-t" style="background-color: #ffffff; border-color: rgba(0,0,0,0.1);">
        <div class="flex items-center justify-between px-8 py-4" style="max-width: 1280px; margin: 0 auto;">
            
            <div class="flex items-center gap-4">
                <!-- Nút Đóng - Trở về thư viện (Thay cho onClose của React) -->
                <a href="?" class="flex items-center gap-2 px-3 py-2 rounded-lg transition-colors hover:opacity-80" style="background-color: rgba(0,0,0,0.05); color: inherit;">
                    <span>← Thư viện</span>
                </a>
                <span class="text-sm opacity-70">
                    Chương <?= $bookInfo['currentChapter'] ?> / <?= count($chapters) ?>
                </span>
            </div>

            <!-- Các nút công cụ -->
            <div class="flex items-center gap-3">
                <button onclick="togglePanel('chapters-panel')" class="flex items-center gap-2 px-4 py-2 rounded-lg transition-colors" style="background-color: rgba(0,0,0,0.05);">
                    <span>Mục lục</span>
                </button>
                <button onclick="togglePanel('settings-panel')" class="flex items-center gap-2 px-4 py-2 rounded-lg transition-colors" style="background-color: rgba(0,0,0,0.05);">
                    <span>Cài đặt</span>
                </button>
            </div>

            <!-- Tiến trình đọc -->
            <div class="flex items-center gap-3">
                <div class="w-32 h-1.5 rounded-full" style="background-color: rgba(0,0,0,0.1);">
                    <div class="h-full rounded-full transition-all" style="width: 45%; background-color: #087E8B;"></div>
                </div>
                <span class="text-sm opacity-70">45%</span>
            </div>
        </div>
    </div>

    <!-- Bảng Mục lục (Được ẩn đi bằng display: none) -->
    <div id="chapters-panel" style="display: none;">
        <div class="fixed inset-0 bg-black bg-opacity-50 z-40" onclick="togglePanel('chapters-panel')"></div>
        <div class="fixed bottom-0 left-0 right-0 rounded-t-3xl shadow-2xl z-50 p-6" style="background-color: #fff; max-height: 70vh; overflow-y: auto;">
            <div class="flex justify-between items-center mb-4 border-b pb-4">
                <h3 class="text-lg font-bold"><?= $bookInfo['title'] ?></h3>
                <button onclick="togglePanel('chapters-panel')">Đóng ❌</button>
            </div>
            <div>
                <!-- Vòng lặp foreach thay thế cho chapters.map[cite: 1] -->
                <?php foreach ($chapters as $chapter) : ?>
                    <button class="w-full flex justify-between p-4 mb-2 rounded-lg" style="<?= $chapter['id'] == $bookInfo['currentChapter'] ? 'background-color: #087E8B20; border-left: 4px solid #087E8B;' : '' ?>">
                        <span><?= $chapter['id'] ?>. <?= $chapter['title'] ?></span>
                        <span class="opacity-60">Trang <?= $chapter['page'] ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Bảng Cài đặt (Được ẩn đi bằng display: none) -->
    <div id="settings-panel" style="display: none;">
        <div class="fixed inset-0 bg-black bg-opacity-50 z-40" onclick="togglePanel('settings-panel')"></div>
        <div class="fixed bottom-0 left-0 right-0 rounded-t-3xl shadow-2xl z-50 p-6" style="background-color: #fff; max-height: 80vh; overflow-y: auto;">
            <div class="flex justify-between items-center mb-4 border-b pb-4">
                <h3 class="text-lg font-bold">Cài đặt hiển thị</h3>
                <button onclick="togglePanel('settings-panel')">Đóng ❌</button>
            </div>
            
            <div class="mb-8">
                <h4 class="mb-4 font-bold">Cỡ chữ</h4>
                <div class="flex items-center gap-4">
                    <button onclick="changeFontSize(-2)" class="p-3 bg-gray-200 rounded-lg">➖</button>
                    <span id="font-size-display" class="font-bold"><?= $fontSize ?>px</span>
                    <button onclick="changeFontSize(2)" class="p-3 bg-gray-200 rounded-lg">➕</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. Javascript thay thế cho các lệnh useState của React[cite: 1] -->
<script>
    // Hàm bật/tắt các menu panel (thay cho setShowChapters, setShowSettings)[cite: 1]
    function togglePanel(panelId) {
        var panel = document.getElementById(panelId);
        if (panel.style.display === "none") {
            panel.style.display = "block";
        } else {
            panel.style.display = "none";
        }
    }

    // Hàm thay đổi cỡ chữ (thay cho setFontSize)
    var currentSize = <?= $fontSize ?>;
    function changeFontSize(change) {
        currentSize += change;
        if (currentSize < 14) currentSize = 14; // Giới hạn nhỏ nhất[cite: 1]
        if (currentSize > 28) currentSize = 28; // Giới hạn lớn nhất[cite: 1]
        
        document.getElementById('reading-text').style.fontSize = currentSize + 'px';
        document.getElementById('font-size-display').innerText = currentSize + 'px';
    }
</script>