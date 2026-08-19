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

$sampleText = "Cậu bé tên là Santiago. Trời đã xế chiều khi đàn cừu đi đến một nhà thờ cổ kính nơi người chăn cừu từng nghỉ đêm. Mái nhà của nhà thờ từ lâu đã đổ sập, và có một cây sung lớn mọc lên chỗ thánh đường cũ.\n\nCậu quyết định sẽ dành đêm nay ở đấy. Cậu dồn hết đàn cừu vào cửa đổ nát, rồi xếp những tấm gỗ thành chướng ngại vật để ngăn không cho cừu đi ra ngoài trong đêm. Không có sói ở vùng này, nhưng một khi nào có con cừu đi lạc vào đêm tối, cậu sẽ phải mất cả buổi sáng hôm sau để tìm nó.\n\nCậu trải chiếc áo choàng trên nền đất và nằm xuống, dùng cuốn sách mình vừa đọc dở làm gối. Trước khi ngủ, cậu nghĩ rằng mình cần phải đọc thêm vài cuốn sách dày hơn. Dùng sách làm gối sẽ thoải mái hơn nhiều.";
?>

<div id="reader-container" class="reader-container" style="background-color: #F4ECD8; color: #5B4636;">
    
    <div class="reader-content">
        <div class="reader-header">
            <h1 class="reader-title"><?= $bookInfo['title'] ?></h1>
            <p class="reader-author"><?= $bookInfo['author'] ?></p>
        </div>

        <h2 class="reader-chapter-title">Phần ba</h2>

        <div id="reading-text" class="reading-text" style="font-size: 18px; font-family: Georgia, serif;">
            <?= $sampleText ?>
        </div>
    </div>

    <div class="reader-taskbar">
        <div class="taskbar-inner">
            <div class="flex items-center" style="gap: 1rem;">
                <a href="?" class="taskbar-btn">← Trở lại</a>
                <span style="font-size: 0.875rem; opacity: 0.7;">Chương <?= $bookInfo['currentChapter'] ?> / <?= count($chapters) ?></span>
            </div>

            <div class="flex items-center" style="gap: 0.75rem;">
                <button onclick="togglePanel('chapters-panel')" class="taskbar-btn">Mục lục</button>
                <button onclick="togglePanel('settings-panel')" class="taskbar-btn">Cài đặt</button>
            </div>

            <div class="flex items-center" style="gap: 0.75rem;">
                <div class="reader-progress-bg">
                    <div class="reader-progress-fill" style="width: 45%;"></div>
                </div>
                <span style="font-size: 0.875rem; opacity: 0.7;">45%</span>
            </div>
        </div>
    </div>

    <div id="chapters-overlay" class="overlay" onclick="togglePanel('chapters-panel')"></div>
    <div id="chapters-panel" class="panel">
        <div class="panel-header">
            <h3><?= $bookInfo['title'] ?></h3>
            <button onclick="togglePanel('chapters-panel')" style="border: none; background: transparent; font-size: 1.2rem; cursor: pointer;">❌</button>
        </div>
        <div>
            <?php foreach ($chapters as $chapter) : ?>
                <button class="chapter-btn" style="<?= $chapter['id'] == $bookInfo['currentChapter'] ? 'background-color: #087E8B20; border-left: 4px solid #087E8B;' : '' ?>">
                    <span><?= $chapter['id'] ?>. <?= $chapter['title'] ?></span>
                    <span style="opacity: 0.6;">Trang <?= $chapter['page'] ?></span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="settings-overlay" class="overlay" onclick="togglePanel('settings-panel')"></div>
    <div id="settings-panel" class="panel">
        <div class="panel-header">
            <h3>Cài đặt hiển thị</h3>
            <button onclick="togglePanel('settings-panel')" style="border: none; background: transparent; font-size: 1.2rem; cursor: pointer;">❌</button>
        </div>
        <div style="margin-bottom: 2rem;">
            <h4 style="margin-bottom: 1rem; font-weight: 600;">Cỡ chữ</h4>
            <div class="flex items-center" style="gap: 1rem;">
                <button onclick="changeFontSize(-2)" class="font-ctrl-btn">➖</button>
                <span id="font-size-display" style="font-weight: bold;">18px</span>
                <button onclick="changeFontSize(2)" class="font-ctrl-btn">➕</button>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePanel(panelId) {
        var panel = document.getElementById(panelId);
        var overlay = document.getElementById(panelId.replace('panel', 'overlay'));
        
        if (panel.style.display === "block") {
            panel.style.display = "none";
            overlay.style.display = "none";
        } else {
            panel.style.display = "block";
            overlay.style.display = "block";
        }
    }

    var currentSize = 18;
    function changeFontSize(change) {
        currentSize += change;
        if (currentSize < 14) currentSize = 14;
        if (currentSize > 28) currentSize = 28;
        
        document.getElementById('reading-text').style.fontSize = currentSize + 'px';
        document.getElementById('font-size-display').innerText = currentSize + 'px';
    }
</script>