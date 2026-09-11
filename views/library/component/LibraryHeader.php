<header style="height: 80px; background: #fff; border-bottom: 1px solid #e5e7eb;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 3rem; height: 100%; display: flex; align-items: center; justify-content: space-between;">

        <!-- Logo -->
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <div class="logo-box">R</div>
            <span style="font-size: 1.5rem; font-weight: 600; color: #102A43;">readly</span>
        </div>

        <!-- Navigation -->
        <nav style="display: flex; align-items: center; gap: 2rem;">
            <a href="/home" style="color: #6B7280; font-size: 1rem;">Khám phá</a>
            <a href="/library" style="color: #087E8B; font-weight: 600; font-size: 1rem; position: relative; text-decoration: none;">
                Thư viện
                <div style="position: absolute; bottom: -28px; left: 0; right: 0; height: 2px; background-color: #087E8B;"></div>
            </a>
            <a href="/home?view=login&mode=register" style="color: #6B7280; font-size: 1rem;">Kích hoạt</a>
        </nav>

        <!-- Search & User -->
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div class="search-box">
                <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" placeholder="Tìm trong thư viện của bạn..." class="search-input">
            </div>

            <button style="background: none; border: none; cursor: pointer; padding: 8px; border-radius: 8px;">
                <svg style="width: 20px; height: 20px; color: #6B7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
            </button>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div class="user-avatar" style="background: #087E8B; color: #ffffff; font-weight: 700; font-size: 13px; display: flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 50%; text-transform: uppercase;">
                        <?= htmlspecialchars(mb_substr($_SESSION['user_name'] ?? 'U', 0, 1, 'UTF-8'), ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <span style="font-size: 14px; font-weight: 600; color: #102A43;">
                        <?= htmlspecialchars($_SESSION['user_name'] ?? 'Tài khoản', ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <a href="/home" onclick="setTimeout(function(){ if(typeof openDepositModal==='function') openDepositModal(); }, 300);" style="background: #087E8B; color: #ffffff; padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;" title="Nạp tiền vào tài khoản">
                        <span>Số dư: <?= number_format($_SESSION['user_balance'] ?? 0, 0, ',', '.') ?>đ</span> +
                    </a>
                </div>
            <?php else: ?>
                <a href="/home?view=login" style="color: #087E8B; font-weight: 600; font-size: 14px; text-decoration: none;">Đăng nhập</a>
            <?php endif; ?>
        </div>

    </div>
</header>