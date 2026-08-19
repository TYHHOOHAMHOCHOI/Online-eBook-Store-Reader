<?php
if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
$isRegister = ($_GET['mode'] ?? '') === 'register';
?>

<!-- Login / Register Modal Component (Converted from Login.tsx) -->
<div id="loginModal" class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <a href="/home" class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity block cursor-default"></a>

    <!-- Modal Box -->
    <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 relative pointer-events-auto transition-transform scale-100">
            
            <!-- Close Button -->
            <a
                href="/home"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-100 transition-colors"
                title="Đóng"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </a>

            <!-- Header Logo & Title -->
            <div class="text-center mb-6">
                <div class="flex items-center justify-center gap-2 mb-4">
                    <div class="w-12 h-12 bg-[#087E8B] rounded-lg flex items-center justify-center shadow-md">
                        <span class="text-white text-2xl font-bold">R</span>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-[#102A43] mb-1">
                    <?= $isRegister ? 'Tạo tài khoản mới' : 'Chào mừng trở lại' ?>
                </h2>
                <p class="text-gray-600 text-sm">
                    <?= $isRegister ? 'Đăng ký để khám phá hàng ngàn cuốn sách hay' : 'Đăng nhập để tiếp tục đọc sách yêu thích' ?>
                </p>
            </div>

            <!-- Login / Register Form -->
            <form action="/home" method="GET" class="space-y-4" onsubmit="alert('<?= $isRegister ? 'Đăng ký thành công!' : 'Đăng nhập thành công!' ?>'); return true;">
                <input type="hidden" name="view" value="home">

                <?php if ($isRegister): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Họ và tên
                        </label>
                        <input
                            type="text"
                            required
                            placeholder="Nguyễn Văn A"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#087E8B] focus:ring-2 focus:ring-[#087E8B]/20 transition-all"
                        />
                    </div>
                <?php endif; ?>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Email hoặc số điện thoại
                    </label>
                    <input
                        type="text"
                        required
                        placeholder="example@email.com"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#087E8B] focus:ring-2 focus:ring-[#087E8B]/20 transition-all"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Mật khẩu
                    </label>
                    <input
                        type="password"
                        required
                        placeholder="••••••••"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#087E8B] focus:ring-2 focus:ring-[#087E8B]/20 transition-all"
                    />
                </div>

                <?php if (!$isRegister): ?>
                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" checked class="rounded text-[#087E8B] focus:ring-[#087E8B]" />
                            <span class="text-gray-600 text-xs">Ghi nhớ đăng nhập</span>
                        </label>
                        <a href="#" onclick="alert('Vui lòng kiểm tra email để nhận liên kết khôi phục mật khẩu!'); return false;" class="text-xs font-medium text-[#087E8B] hover:text-[#075A64] hover:underline">
                            Quên mật khẩu?
                        </a>
                    </div>
                <?php else: ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Loại tài khoản
                        </label>
                        <select class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#087E8B] bg-white">
                            <option value="READER">Độc giả (Mua & Đọc sách)</option>
                            <option value="PUBLISHER">Nhà phát hành (Bán sách)</option>
                        </select>
                    </div>
                <?php endif; ?>

                <button
                    type="submit"
                    class="w-full px-6 py-3 bg-[#087E8B] text-white rounded-lg hover:bg-[#075A64] font-semibold text-sm shadow-md transition-colors"
                >
                    <?= $isRegister ? 'Đăng ký ngay' : 'Đăng nhập' ?>
                </button>

                <!-- Divider -->
                <div class="relative my-5">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="px-3 bg-white text-gray-400 font-medium">Hoặc</span>
                    </div>
                </div>

                <!-- Social Logins -->
                <div class="space-y-2.5">
                    <button
                        type="button"
                        onclick="alert('Đang kết nối với Google...')"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center justify-center gap-3 transition-colors text-xs font-medium text-gray-700"
                    >
                        <svg class="w-4 h-4" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                        </svg>
                        Đăng nhập với Google
                    </button>

                    <button
                        type="button"
                        onclick="alert('Đang kết nối với Facebook...')"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center justify-center gap-3 transition-colors text-xs font-medium text-gray-700"
                    >
                        <svg class="w-4 h-4" fill="#1877F2" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                        Đăng nhập với Facebook
                    </button>
                </div>
            </form>

            <!-- Bottom Register Link -->
            <div class="mt-6 text-center text-xs">
                <?php if ($isRegister): ?>
                    <span class="text-gray-600">Đã có tài khoản? </span>
                    <a href="/home?view=login" class="text-[#087E8B] hover:text-[#075A64] font-semibold">
                        Đăng nhập ngay
                    </a>
                <?php else: ?>
                    <span class="text-gray-600">Chưa có tài khoản? </span>
                    <a href="/home?view=login&mode=register" class="text-[#087E8B] hover:text-[#075A64] font-semibold">
                        Đăng ký ngay
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>
