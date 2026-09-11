<?php
// Kiểm tra biến $selectedBook truyền từ main.php
if (!isset($selectedBook)) {
    header("Location: /library");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đang đọc: <?= htmlspecialchars($selectedBook['title']) ?></title>
    <style>
        :root {
            --bg-color: #ffffff;
            --text-color: #2b2b2b;
            --font-family: 'Segoe UI', Arial, sans-serif;
            --font-size: 18px;
        }

        body.theme-sepia { --bg-color: #f8f1e3; --text-color: #4f3b2b; }
        body.theme-dark { --bg-color: #1a1a1a; --text-color: #d1d1d1; }

        body {
            margin: 0;
            padding: 0;
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: var(--font-family);
            transition: background-color 0.3s, color 0.3s;
            user-select: text; /* Cho phép copy văn bản */
        }

        /* Toolbar điều khiển phía trên */
        .reader-toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        body.theme-dark .reader-toolbar {
            background: rgba(30, 30, 30, 0.95);
            border-bottom-color: #333;
            color: #fff;
        }

        .controls-group { display: flex; align-items: center; gap: 12px; }

        .btn-ctrl {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }

        body.theme-dark .btn-ctrl { background: #333; border-color: #555; color: #fff; }

        select.btn-ctrl { padding: 6px 8px; }

        /* Vùng hiển thị nội dung sách */
        .reader-container {
            max-width: 800px;
            margin: 80px auto 100px auto;
            padding: 0 25px;
            font-size: var(--font-size);
            line-height: 1.8;
            word-wrap: break-word;
        }

        .book-header { text-align: center; margin-bottom: 40px; border-bottom: 1px solid #ddd; padding-bottom: 20px; }

        /* Thanh tiến độ cố định phía dưới */
        .reader-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            border-top: 1px solid #e5e7eb;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            z-index: 1000;
        }

        body.theme-dark .reader-footer { background: rgba(30, 30, 30, 0.95); border-top-color: #333; }

        .progress-slider { flex: 1; }
    </style>
</head>
<body>

    <div class="reader-toolbar">
        <div class="controls-group">
            <a href="/library" class="btn-ctrl" style="text-decoration: none;">← Thư viện</a>
            <strong style="font-size: 0.95rem; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                <?= htmlspecialchars($selectedBook['title']) ?>
            </strong>
        </div>

        <div class="controls-group">
            <select id="fontSelect" class="btn-ctrl" onchange="changeFont(this.value)">
                <option value="'Segoe UI', Arial, sans-serif">Font Sans-Serif</option>
                <option value="Georgia, serif">Font Georgia (Serif)</option>
                <option value="'Times New Roman', serif">Font Times New Roman</option>
                <option value="'Courier New', monospace">Font Monospace</option>
            </select>

            <button class="btn-ctrl" onclick="changeFontSize(-2)">A-</button>
            <button class="btn-ctrl" onclick="changeFontSize(2)">A+</button>

            <select id="themeSelect" class="btn-ctrl" onchange="changeTheme(this.value)">
                <option value="light">☀️ Sáng</option>
                <option value="sepia">📜 Sepia</option>
                <option value="dark">🌙 Tối</option>
            </select>

            <button class="btn-ctrl" id="ttsBtn" onclick="toggleTextToSpeech()" style="background: #087E8B; color: white; border: none;">
                🔊 Đọc audio
            </button>
        </div>
    </div>

    <div class="reader-container" id="bookContent">
        <div class="book-header">
            <h2><?= htmlspecialchars($selectedBook['title']) ?></h2>
            <p>Tác giả: <i><?= htmlspecialchars($selectedBook['author']) ?></i></p>
        </div>

        <div id="textBody">
            <?php if (!empty($selectedBook['description'])): ?>
                <p><?= nl2br(htmlspecialchars($selectedBook['description'])) ?></p>
            <?php endif; ?>

            <p>Đây là nội dung cuốn sách <b><?= htmlspecialchars($selectedBook['title']) ?></b>. Trình đọc đã được tích hợp đầy đủ tính năng cho phép chọn và sao chép (copy) văn bản một cách dễ dàng.</p>
            
            <p>Bạn có thể thử dùng các công cụ phía trên để tùy chỉnh cỡ chữ lớn nhỏ, chuyển sang phông chữ Georgia kinh điển hoặc đổi giao diện nền Sepia/Tối để bảo vệ mắt khi đọc vào ban đêm.</p>

            <p>Đặc biệt, tính năng lưu tiến độ tự động sẽ giúp bạn tiếp tục đọc đúng vị trí này mà không sợ bị trôi trang khi đăng nhập lại ở các lần sau!</p>
        </div>
    </div>

    <div class="reader-footer">
        <span style="font-size: 0.85rem; font-weight: 600;">Tiến độ đọc:</span>
        <input type="range" id="progressSlider" class="progress-slider" min="0" max="100" value="<?= (int)$selectedBook['progress'] ?>" oninput="updateProgressLabel(this.value)" onchange="saveProgress(this.value)">
        <span id="progressValue" style="font-size: 0.85rem; font-weight: 700; min-width: 45px;"><?= (int)$selectedBook['progress'] ?>%</span>
    </div>

    <script>
        let currentFontSize = 18;
        let isSpeaking = false;
        const bookId = <?= (int)$selectedBook['id'] ?>;

        // 1. Thay đổi Kích thước chữ
        function changeFontSize(delta) {
            currentFontSize = Math.max(12, Math.min(32, currentFontSize + delta));
            document.documentElement.style.setProperty('--font-size', currentFontSize + 'px');
        }

        // 2. Thay đổi Phông chữ
        function changeFont(fontFamily) {
            document.documentElement.style.setProperty('--font-family', fontFamily);
        }

        // 3. Thay đổi Giao diện Màu nền
        function changeTheme(theme) {
            document.body.className = '';
            if (theme !== 'light') {
                document.body.classList.add('theme-' + theme);
            }
        }

        // 4. Cập nhật label tiến độ
        function updateProgressLabel(val) {
            document.getElementById('progressValue').innerText = val + '%';
        }

        // 5. Đọc văn bản thành tiếng (Text-to-Speech Audio)
        function toggleTextToSpeech() {
            if (!('speechSynthesis' in window)) {
                alert('Trình duyệt của bạn không hỗ trợ tính năng đọc âm thanh!');
                return;
            }

            const btn = document.getElementById('ttsBtn');

            if (isSpeaking) {
                window.speechSynthesis.cancel();
                isSpeaking = false;
                btn.innerText = '🔊 Đọc audio';
                btn.style.background = '#087E8B';
            } else {
                const text = document.getElementById('bookContent').innerText;
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'vi-VN';
                utterance.rate = 0.9;

                utterance.onend = function() {
                    isSpeaking = false;
                    btn.innerText = '🔊 Đọc audio';
                    btn.style.background = '#087E8B';
                };

                window.speechSynthesis.speak(utterance);
                isSpeaking = true;
                btn.innerText = '⏹️ Dừng đọc';
                btn.style.background = '#dc2626';
            }
        }

        // 6. Gửi AJAX Tự động Lưu tiến độ đọc vào CSDL
        function saveProgress(percent) {
            fetch('/api/update_progress.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `book_id=${bookId}&progress=${percent}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    console.log('Đã lưu tiến độ đọc thành công!');
                }
            })
            .catch(err => console.error('Lỗi lưu tiến độ:', err));
        }
    </script>
</body>
</html>