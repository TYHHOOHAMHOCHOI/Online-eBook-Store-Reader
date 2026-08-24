<?php

declare(strict_types=1);

session_start();

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // =========================
    // VALIDATE
    // =========================

    if ($email === '') {
        $errors[] = 'Vui lòng nhập email.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ.';
    }

    if ($password === '') {
        $errors[] = 'Vui lòng nhập mật khẩu.';
    }

    // =========================
    // KIỂM TRA DATABASE
    // =========================

    if (empty($errors)) {

        try {

            $database = db();

            $stmt = $database->prepare(
                'SELECT id, name, email, phone, password_hash, role
                 FROM users
                 WHERE email = :email
                 LIMIT 1'
            );

            $stmt->execute([
                'email' => $email
            ]);

            $user = $stmt->fetch();

            // Không tìm thấy tài khoản
            if (!$user) {

                $errors[] = 'Email hoặc mật khẩu không chính xác.';

            } elseif (!password_verify($password, $user['password_hash'])) {

                // Sai mật khẩu
                $errors[] = 'Email hoặc mật khẩu không chính xác.';

            } else {

                // =========================
                // ĐĂNG NHẬP THÀNH CÔNG
                // =========================

                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                // Chuyển về trang Home
                header('Location: /home');
                exit;
            }

        } catch (PDOException $e) {

            $errors[] = 'Không thể kết nối với cơ sở dữ liệu.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title><?= htmlspecialchars($pageTitle ?? 'Đăng nhập - Readly') ?></title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(135deg, #eef7f8, #f8fbfc);

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 30px 15px;

            color: #1e293b;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
        }

        .login-card {
            background: #ffffff;

            border-radius: 20px;

            padding: 38px;

            box-shadow:
                0 15px 45px rgba(0, 0, 0, 0.08);
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-icon {

            width: 54px;
            height: 54px;

            margin: 0 auto 10px;

            border-radius: 14px;

            background: #087e8b;

            color: white;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 26px;
            font-weight: bold;
        }

        .logo h1 {
            margin: 0;

            font-size: 28px;

            color: #087e8b;
        }

        .logo p {

            margin: 8px 0 0;

            color: #64748b;

            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {

            display: block;

            margin-bottom: 8px;

            font-size: 14px;

            font-weight: 600;
        }

        .form-group input {

            width: 100%;

            padding: 14px;

            border: 1px solid #dbe4e8;

            border-radius: 10px;

            outline: none;

            font-size: 15px;

            transition: 0.2s;
        }

        .form-group input:focus {

            border-color: #087e8b;

            box-shadow:
                0 0 0 3px rgba(8, 126, 139, 0.1);
        }

        .message {

            padding: 13px 14px;

            border-radius: 10px;

            margin-bottom: 20px;

            font-size: 14px;
        }

        .error-message {

            background: #fff1f2;

            color: #be123c;

            border: 1px solid #fecdd3;
        }

        .error-message div {
            margin: 4px 0;
        }

        .login-button {

            width: 100%;

            border: none;

            border-radius: 10px;

            padding: 14px;

            background: #087e8b;

            color: white;

            font-size: 16px;

            font-weight: 600;

            cursor: pointer;

            transition: 0.2s;
        }

        .login-button:hover {

            background: #066873;

            transform: translateY(-1px);
        }

        .register-link {

            text-align: center;

            margin-top: 23px;

            font-size: 14px;

            color: #64748b;
        }

        .register-link a {

            color: #087e8b;

            font-weight: 600;

            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 500px) {

            .login-card {
                padding: 25px 20px;
            }
        }

    </style>

</head>

<body>

<div class="login-container">

    <div class="login-card">

        <!-- Logo -->

        <div class="logo">

            <div class="logo-icon">
                R
            </div>

            <h1>
                Readly
            </h1>

            <p>
                Chào mừng bạn quay trở lại
            </p>

        </div>


        <!-- Error -->

        <?php if (!empty($errors)): ?>

            <div class="message error-message">

                <?php foreach ($errors as $error): ?>

                    <div>
                        • <?= htmlspecialchars($error) ?>
                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>


        <!-- Login form -->

        <form
            method="POST"
            action="/login"
        >

            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= htmlspecialchars($email) ?>"
                    placeholder="example@email.com"
                    autocomplete="email"
                    required
                >

            </div>


            <div class="form-group">

                <label for="password">
                    Mật khẩu
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Nhập mật khẩu"
                    autocomplete="current-password"
                    required
                >

            </div>


            <button
                type="submit"
                class="login-button"
            >
                Đăng nhập
            </button>

        </form>


        <!-- Register -->

        <div class="register-link">

            Chưa có tài khoản?

            <a href="/register">
                Đăng ký ngay
            </a>

        </div>

    </div>

</div>

</body>

</html>