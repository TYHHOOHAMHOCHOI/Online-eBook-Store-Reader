<?php

declare(strict_types=1);

$errors = [];
$success = '';

$name = '';
$email = '';
$phone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Lấy dữ liệu từ form
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    // =========================
    // VALIDATE
    // =========================

    if ($name === '') {
        $errors[] = 'Vui lòng nhập họ và tên.';
    } elseif (mb_strlen($name) < 2) {
        $errors[] = 'Họ và tên phải có ít nhất 2 ký tự.';
    }

    if ($email === '') {
        $errors[] = 'Vui lòng nhập email.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ.';
    }

    if ($phone !== '') {
        if (!preg_match('/^[0-9]{9,11}$/', $phone)) {
            $errors[] = 'Số điện thoại phải có từ 9 đến 11 chữ số.';
        }
    }

    if ($password === '') {
        $errors[] = 'Vui lòng nhập mật khẩu.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự.';
    }

    if ($password !== $passwordConfirm) {
        $errors[] = 'Mật khẩu xác nhận không khớp.';
    }

    // =========================
    // KIỂM TRA DATABASE
    // =========================

    if (empty($errors)) {
        try {
            $database = db();

            // Kiểm tra email đã tồn tại
            $stmt = $database->prepare(
                'SELECT id FROM users WHERE email = :email LIMIT 1'
            );

            $stmt->execute([
                'email' => $email
            ]);

            if ($stmt->fetch()) {
                $errors[] = 'Email này đã được sử dụng.';
            }

            // Kiểm tra số điện thoại nếu có nhập
            if ($phone !== '') {
                $stmt = $database->prepare(
                    'SELECT id FROM users WHERE phone = :phone LIMIT 1'
                );

                $stmt->execute([
                    'phone' => $phone
                ]);

                if ($stmt->fetch()) {
                    $errors[] = 'Số điện thoại này đã được sử dụng.';
                }
            }

            // =========================
            // INSERT USER
            // =========================

            if (empty($errors)) {

                // Mã hóa mật khẩu
                $passwordHash = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $stmt = $database->prepare(
                    'INSERT INTO users
                    (name, email, phone, password_hash, role)
                    VALUES
                    (:name, :email, :phone, :password_hash, :role)'
                );

                $stmt->execute([
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone !== '' ? $phone : null,
                    'password_hash' => $passwordHash,
                    'role' => 'customer'
                ]);

                $success = 'Đăng ký tài khoản thành công! Bạn có thể đăng nhập ngay.';

                // Xóa dữ liệu form sau khi đăng ký thành công
                $name = '';
                $email = '';
                $phone = '';
            }

        } catch (PDOException $e) {
            $errors[] = 'Không thể kết nối hoặc ghi dữ liệu vào cơ sở dữ liệu.';
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

    <title><?= htmlspecialchars($pageTitle ?? 'Đăng ký - Readly') ?></title>

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

        .register-container {
            width: 100%;
            max-width: 480px;
        }

        .register-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 36px;
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.08);
        }

        .logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo-icon {
            width: 52px;
            height: 52px;
            margin: 0 auto 10px;
            border-radius: 14px;
            background: #087e8b;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
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
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            font-size: 14px;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #dbe4e8;
            border-radius: 10px;
            outline: none;
            font-size: 15px;
            transition: 0.2s;
        }

        .form-group input:focus {
            border-color: #087e8b;
            box-shadow: 0 0 0 3px rgba(8, 126, 139, 0.1);
        }

        .required {
            color: #ef4444;
        }

        .message {
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 14px;
        }

        .error-message {
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3;
        }

        .success-message {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .error-message div {
            margin: 4px 0;
        }

        .register-button {
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

        .register-button:hover {
            background: #066873;
            transform: translateY(-1px);
        }

        .login-link {
            text-align: center;
            margin-top: 22px;
            font-size: 14px;
            color: #64748b;
        }

        .login-link a {
            color: #087e8b;
            font-weight: 600;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 500px) {
            .register-card {
                padding: 25px 20px;
            }
        }
    </style>
</head>

<body>

<div class="register-container">

    <div class="register-card">

        <div class="logo">
            <div class="logo-icon">R</div>
            <h1>Readly</h1>
            <p>Tạo tài khoản để bắt đầu đọc sách</p>
        </div>

        <?php if (!empty($errors)): ?>

            <div class="message error-message">
                <?php foreach ($errors as $error): ?>
                    <div>• <?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>


        <?php if ($success !== ''): ?>

            <div class="message success-message">
                <?= htmlspecialchars($success) ?>
            </div>

        <?php endif; ?>


        <form method="POST" action="/register">

            <div class="form-group">
                <label for="name">
                    Họ và tên <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?= htmlspecialchars($name) ?>"
                    placeholder="Nhập họ và tên"
                    required
                >
            </div>


            <div class="form-group">
                <label for="email">
                    Email <span class="required">*</span>
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= htmlspecialchars($email) ?>"
                    placeholder="example@email.com"
                    required
                >
            </div>


            <div class="form-group">
                <label for="phone">
                    Số điện thoại
                </label>

                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    value="<?= htmlspecialchars($phone) ?>"
                    placeholder="0901234567"
                >
            </div>


            <div class="form-group">
                <label for="password">
                    Mật khẩu <span class="required">*</span>
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Tối thiểu 6 ký tự"
                    required
                >
            </div>


            <div class="form-group">
                <label for="password_confirm">
                    Xác nhận mật khẩu <span class="required">*</span>
                </label>

                <input
                    type="password"
                    id="password_confirm"
                    name="password_confirm"
                    placeholder="Nhập lại mật khẩu"
                    required
                >
            </div>


            <button
                type="submit"
                class="register-button"
            >
                Đăng ký tài khoản
            </button>

        </form>


        <div class="login-link">
            Đã có tài khoản?
            <a href="/login">Đăng nhập</a>
        </div>

    </div>

</div>

</body>
</html>