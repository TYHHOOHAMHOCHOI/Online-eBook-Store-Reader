<section class="books-section">
    <div class="books-toolbar">

        <!-- Tab trạng thái đọc -->
        <div class="tab-list">
            <button class="tab-btn active">Đang đọc</button>
            <button class="tab-btn">Chưa đọc</button>
            <button class="tab-btn">Đã hoàn thành</button>
        </div>

        <!-- Công cụ lọc -->
        <div class="filter-tools">
            <span style="font-size: 0.875rem; color: #6B7280;">Sắp xếp theo:</span>
            <select class="filter-select">
                <option>Tên sách</option>
                <option>Ngày mua</option>
                <option>Tiến độ</option>
            </select>
            <button class="filter-btn">Lọc theo trạng thái</button>
        </div>
    </div>

    <!-- Lưới sách -->
    <div class="books-grid">
        <?php foreach ($books as $book): ?>
        <div class="book-card">

            <!-- Bìa sách -->
            <div class="book-cover" style="background-color: <?= $book['color'] ?>; height: 280px; padding: 1.5rem;">
                <div>
                    <div class="book-line"></div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: #fff; margin: 0 0 8px;"><?= $book['title'] ?></h3>
                </div>
                <p style="font-size: 0.875rem; color: rgba(255,255,255,0.9); margin: 0;"><?= $book['author'] ?></p>
            </div>

            <!-- Thông tin tiến độ -->
            <div class="book-info">
                <div style="margin-bottom: 12px;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: #6B7280; margin-bottom: 4px;">
                        <span>Tiến độ đọc</span>
                        <span style="font-weight: 600; color: #087E8B;"><?= $book['progress'] ?>%</span>
                    </div>
                    <div class="progress-bg">
                        <div class="progress-fill" style="width: <?= $book['progress'] ?>%;"></div>
                    </div>
                </div>

                <a href="?read=<?= $book['id'] ?>" class="btn-read">
                    Đọc tiếp <span>&#10095;</span>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>