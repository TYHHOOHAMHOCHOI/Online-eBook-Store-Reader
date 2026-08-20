<section class="hero-section home-container">
    <div class="hero-box">
        <div class="hero-grid">
            <div class="hero-content">
                <p class="hero-label">Sách hay không phải để lướt qua.</p>
                <h1>Khám phá cuốn sách đúng lúc bạn cần</h1>
                <p class="hero-description">Từ những câu chuyện chữa lành đến kiến thức giúp bạn tiến xa hơn — tất cả trong một thư viện đọc thật dễ chịu.</p>
                <div class="hero-action">
                    <a href="/library?read=1" class="primary-yellow-button" style="text-decoration: none;">
                        <span class="font-semibold">Bắt đầu đọc</span>
                        <span>→</span>
                    </a>
                    <p>Hơn 20.000 tựa sách đang chờ bạn</p>
                </div>
            </div>

            <div class="hero-book-area">
                <div class="hero-book">
                    <div class="hero-book-content">
                        <div class="hero-book-title">ĐỌC — MỞ RA MỘT THẾ GIỚI MỚI</div>
                        <div class="hero-book-line"></div>
                    </div>
                    <div class="reader-badge">32K+ độc giả</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sách được yêu thích -->
<section class="home-section home-container">
    <div class="section-heading">
        <div>
            <h2>Sách được yêu thích</h2>
            <p>Những lựa chọn được cộng đồng Readly yêu mến nhất tuần này.</p>
        </div>
        <a href="/home?view=book-list" class="outline-button" style="text-decoration:none;">
            Xem danh sách
            <span>→</span>
        </a>
    </div>

    <div class="favorite-grid">
        <?php include 'BookCards.php'; ?>
    </div>
</section>

<!-- Best Seller Section -->
<section class="home-section home-container best-seller-section">
    <div class="section-title-block">
        <h2>Best Seller</h2>
        <p>Những cuốn sách bán chạy nhất trong tháng</p>

        <div class="category-tabs">
            <button class="category-tab active">Văn học</button>
            <button class="category-tab">Kinh tế</button>
            <button class="category-tab">Tâm lý</button>
            <button class="category-tab">Kỹ năng sống</button>
            <button class="category-tab">Thiếu nhi</button>
            <button class="category-tab">Ngoại ngữ</button>
            <button class="category-tab">Khoa học</button>
        </div>
    </div>

    <div class="best-seller-grid">
        <!-- Có thể gọi component khác nếu cần -->
    </div>

    <div class="pagination-row">
        <div class="pagination">
            <button>01</button>
            <button>02</button>
            <button>03</button>
        </div>
        <a href="/home?view=book-list" class="view-all-link" style="text-decoration:none;">
            Xem tất cả
            <span>→</span>
        </a>
    </div>
</section>

<!-- Gợi ý cho bạn -->
<section class="recommendation-section">
    <div class="home-container">
        <div class="recommendation-header">
            <div>
                <h2>Gợi ý cho bạn</h2>
                <p>Chọn lọc từ những điều bạn đã lưu và hay đọc.</p>
            </div>
            <div class="personal-badge">
                <div>Dành riêng cho bạn</div>
                <div>Cập nhật hôm nay</div>
            </div>
        </div>

        <div class="recommended-grid">
            <?php include 'BookCards.php'; ?>
        </div>

        <div class="recommendation-footer">
            <a href="/home?view=book-list" class="outline-button" style="text-decoration:none;">
                Xem tất cả
                <span>→</span>
            </a>
        </div>
    </div>
</section>