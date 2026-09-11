<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Login Component
|--------------------------------------------------------------------------
| Xử lý đăng nhập trực tiếp trong Home Login Modal.
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$loginErrors = [];
$email = '';

/*
|--------------------------------------------------------------------------
| Xử lý đăng nhập
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // =========================
    // VALIDATE
    // =========================

    if ($email === '') {

        $loginErrors[] = 'Vui lòng nhập email hoặc số điện thoại.';

    } elseif (
        !filter_var($email, FILTER_VALIDATE_EMAIL)
        && !preg_match('/^[0-9]{9,11}$/', $email)
    ) {

        $loginErrors[] = 'Email hoặc số điện thoại không hợp lệ.';
    }

    if ($password === '') {

        $loginErrors[] = 'Vui lòng nhập mật khẩu.';
    }

    // =========================
    // KIỂM TRA DATABASE
    // =========================

    if (empty($loginErrors)) {

        try {

            $database = db();

            $stmt = $database->prepare(
                'SELECT id, name, email, phone, password_hash, role, COALESCE(balance, 0) AS balance
                 FROM users
                 WHERE (email IS NOT NULL AND email = :email)
                    OR (phone IS NOT NULL AND phone = :phone)
                 LIMIT 1'
            );

            $stmt->execute([
                'email' => $email,
                'phone' => $email,
            ]);

            $user = $stmt->fetch();

            // Không tìm thấy tài khoản
            if (!$user) {

                $loginErrors[] = 'Email/Số điện thoại hoặc mật khẩu không chính xác.';

            // Sai mật khẩu
            } elseif (!password_verify($password, $user['password_hash'])) {

                $loginErrors[] = 'Email/Số điện thoại hoặc mật khẩu không chính xác.';

            } else {

                // =========================
                // ĐĂNG NHẬP THÀNH CÔNG
                // =========================

                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'] ?? $user['phone'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_balance'] = (float)($user['balance'] ?? 0);

                // Chuyển hướng theo vai trò
                if ($user['role'] === 'publisher') {
                    $stmtPub = $database->prepare('SELECT id FROM publishers WHERE user_id = ? LIMIT 1');
                    $stmtPub->execute([$user['id']]);
                    $_SESSION['publisher_id'] = (int)($stmtPub->fetchColumn() ?: 0);
                    header('Location: /publisher-dashboard');
                    exit;
                } elseif ($user['role'] === 'admin') {
                    header('Location: /');
                    exit;
                }

                // Customer / Độc giả về trang Home
                header('Location: /home');
                exit;
            }

        } catch (PDOException $e) {
            error_log('Login PDO Error: ' . $e->getMessage());
            $loginErrors[] = 'Không thể kết nối với cơ sở dữ liệu: ' . $e->getMessage();
        }
    }
}


/*
|--------------------------------------------------------------------------
| Helper function
|--------------------------------------------------------------------------
*/

if (!function_exists('e')) {

    function e($value)
    {
        return htmlspecialchars(
            (string)($value ?? ''),
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

?>

<!-- Login Modal Component -->
<div id="loginModal" class="auth-modal-overlay">

    <!-- Backdrop click to close -->
    <a
        href="/home"
        class="auth-modal-backdrop"
        title="Đóng"
    ></a>

    <!-- Modal Box -->
    <div class="auth-modal-container">

        <div class="auth-modal-card">

            <!-- Close Button -->
            <a
                href="/home"
                class="auth-modal-close"
                title="Đóng"
            >
                <svg
                    width="20"
                    height="20"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    ></path>
                </svg>
            </a>


            <!-- Header Logo & Title -->
            <div class="auth-header">

                <div class="auth-logo-box">
                    <span>R</span>
                </div>

                <h2 class="auth-title">
                    Chào mừng trở lại
                </h2>

                <p class="auth-subtitle">
                    Đăng nhập để tiếp tục đọc sách và quản lý thư viện của bạn
                </p>

            </div>


            <!-- Login Errors -->
            <?php if (!empty($loginErrors)): ?>

                <div class="auth-error-message">

                    <?php foreach ($loginErrors as $error): ?>

                        <div>
                            • <?= e($error) ?>
                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>


            <!-- Login Form -->
            <form
                action="/home?view=login"
                method="POST"
                class="auth-form"
            >

                <!-- Email / Số điện thoại -->
                <div class="auth-form-group">

                    <label
                        class="auth-label"
                        for="login_contact"
                    >
                        Email hoặc số điện thoại
                    </label>

                    <input
                        id="login_contact"
                        type="text"
                        name="email"
                        value="<?= e($email) ?>"
                        required
                        placeholder="example@email.com hoặc 090..."
                        class="auth-input"
                        autocomplete="username"
                    >

                </div>


                <!-- Mật khẩu -->
                <div class="auth-form-group">

                    <label
                        class="auth-label"
                        for="login_password"
                    >
                        Mật khẩu
                    </label>

                    <input
                        id="login_password"
                        type="password"
                        name="password"
                        required
                        placeholder="••••••••"
                        class="auth-input"
                        autocomplete="current-password"
                    >

                </div>


                <!-- Ghi nhớ & Quên mật khẩu -->
                <div class="auth-remember-row">

                    <label class="auth-checkbox-label">

                        <input
                            type="checkbox"
                            name="remember"
                            class="auth-checkbox"
                        >

                        <span>
                            Ghi nhớ đăng nhập
                        </span>

                    </label>


                    <a
                        href="#"
                        onclick="alert('Vui lòng kiểm tra email để nhận liên kết khôi phục mật khẩu!'); return false;"
                        class="auth-forgot-link"
                    >
                        Quên mật khẩu?
                    </a>

                </div>


                <!-- Submit Button -->
                <button
                    type="submit"
                    class="auth-submit-btn"
                >
                    Đăng nhập
                </button>


                <!-- Divider -->
                <div class="auth-divider">

                    <span>
                        Hoặc đăng nhập bằng
                    </span>

                </div>


                <!-- Social Buttons -->
                <div class="auth-social-group">

                    <!-- Google -->
                    <button
                        type="button"
                        onclick="alert('Đang kết nối đăng nhập với Google...')"
                        class="auth-social-btn"
                    >

                        <svg
                            width="18"
                            height="18"
                            viewBox="0 0 24 24"
                        >
                            <path
                                fill="#4285F4"
                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                            />

                            <path
                                fill="#34A853"
                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                            />

                            <path
                                fill="#FBBC05"
                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                            />

                            <path
                                fill="#EA4335"
                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                            />
                        </svg>

                        Google

                    </button>


                    <!-- Facebook -->
                    <button
                        type="button"
                        onclick="alert('Đang kết nối đăng nhập với Facebook...')"
                        class="auth-social-btn"
                    >

                        <svg
                            width="18"
                            height="18"
                            fill="#1877F2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"
                            />
                        </svg>

                        Facebook

                    </button>

                </div>

            </form>


            <!-- Bottom Link chuyển sang Đăng ký -->
            <div class="auth-footer">

                <span class="auth-footer-text">
                    Chưa có tài khoản?
                </span>

                <a
                    href="/home?view=register"
                    class="auth-link-bold"
                >
                    Đăng ký ngay
                </a>

            </div>

        </div>

    </div>

</div>