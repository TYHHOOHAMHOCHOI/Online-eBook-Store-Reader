<?php
if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
?>

<!-- Register Modal Component -->
<div id="registerModal" class="auth-modal-overlay">
    <!-- Backdrop click to close -->
    <a href="/home" class="auth-modal-backdrop" title="Đóng"></a>

    <!-- Modal Box -->
    <div class="auth-modal-container">
        <div class="auth-modal-card">
            
            <!-- Close Button -->
            <a href="/home" class="auth-modal-close" title="Đóng">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </a>

            <!-- Header Logo & Title -->
            <div class="auth-header">
                <div class="auth-logo-box">
                    <span>R</span>
                </div>
                <h2 class="auth-title">Tạo tài khoản mới</h2>
                <p class="auth-subtitle">Đăng ký để khám phá hàng ngàn cuốn sách hay trên Readly</p>
            </div>

            <!-- Register Form -->
            <form action="/home" method="GET" class="auth-form" onsubmit="alert('Đăng ký tài khoản thành công! Chào mừng bạn đến với Readly.'); return true;">
                <input type="hidden" name="view" value="home">

                <!-- Họ và tên -->
                <div class="auth-form-group">
                    <label class="auth-label" for="reg_name">Họ và tên</label>
                    <input
                        id="reg_name"
                        type="text"
                        required
                        placeholder="Nguyễn Văn A"
                        class="auth-input"
                    />
                </div>

                <!-- Email hoặc Số điện thoại -->
                <div class="auth-form-group">
                    <label class="auth-label" for="reg_contact">Email hoặc số điện thoại</label>
                    <input
                        id="reg_contact"
                        type="text"
                        required
                        placeholder="example@email.com hoặc 090..."
                        class="auth-input"
                    />
                </div>

                <!-- Loại tài khoản -->
                <div class="auth-form-group">
                    <label class="auth-label" for="reg_role">Loại tài khoản</label>
                    <select id="reg_role" class="auth-input auth-select">
                        <option value="READER">📖 Độc giả (Mua & Đọc sách)</option>
                        <option value="PUBLISHER">🏢 Nhà phát hành (Bán sách & Quản lý)</option>
                    </select>
                </div>

                <!-- Mật khẩu -->
                <div class="auth-form-group">
                    <label class="auth-label" for="reg_password">Mật khẩu</label>
                    <input
                        id="reg_password"
                        type="password"
                        required
                        placeholder="Tối thiểu 6 ký tự"
                        minlength="6"
                        class="auth-input"
                    />
                </div>

                <!-- Xác nhận mật khẩu -->
                <div class="auth-form-group">
                    <label class="auth-label" for="reg_password_confirm">Xác nhận mật khẩu</label>
                    <input
                        id="reg_password_confirm"
                        type="password"
                        required
                        placeholder="Nhập lại mật khẩu"
                        minlength="6"
                        class="auth-input"
                    />
                </div>

                <!-- Điều khoản -->
                <div class="auth-checkbox-row">
                    <label class="auth-checkbox-label">
                        <input type="checkbox" required class="auth-checkbox" />
                        <span>Tôi đồng ý với <a href="#" class="auth-link">Điều khoản dịch vụ</a> và <a href="#" class="auth-link">Chính sách bảo mật</a></span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="auth-submit-btn">
                    Đăng ký ngay
                </button>

                <!-- Divider -->
                <div class="auth-divider">
                    <span>Hoặc đăng ký bằng</span>
                </div>

                <!-- Social Buttons -->
                <div class="auth-social-group">
                    <button
                        type="button"
                        onclick="alert('Đang kết nối đăng ký với Google...')"
                        class="auth-social-btn"
                    >
                        <svg width="18" height="18" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                        </svg>
                        Google
                    </button>

                    <button
                        type="button"
                        onclick="alert('Đang kết nối đăng ký với Facebook...')"
                        class="auth-social-btn"
                    >
                        <svg width="18" height="18" fill="#1877F2" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                        Facebook
                    </button>
                </div>
            </form>

            <!-- Bottom Link chuyển sang Đăng nhập -->
            <div class="auth-footer">
                <span class="auth-footer-text">Đã có tài khoản? </span>
                <a href="/home?view=login" class="auth-link-bold">
                    Đăng nhập ngay
                </a>
            </div>

        </div>
    </div>
</div>
