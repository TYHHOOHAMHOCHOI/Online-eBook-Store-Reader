<?php

// Helper function — defined once, used by all included components
if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

$view = $_GET['view'] ?? 'home';

/*
|--------------------------------------------------------------------------
| LOGIN / REGISTER
|--------------------------------------------------------------------------
| Xử lý trước khi xuất HTML để header('Location: ...') hoạt động bình thường.
*/

if ($view === 'login') {
    include __DIR__ . '/component/Login.php';
}

if ($view === 'register') {
    include __DIR__ . '/component/Register.php';
}

/*
|--------------------------------------------------------------------------
| DEPOSIT POST HANDLER
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deposit') {
    if (isset($_SESSION['user_id'])) {
        $depositAmount = (float)($_POST['deposit_amount'] ?? 0);
        if ($depositAmount > 0) {
            $_SESSION['user_balance'] = ($_SESSION['user_balance'] ?? 0) + $depositAmount;

            try {
                $database = db();
                try {
                    $database->exec("ALTER TABLE users ADD COLUMN balance DECIMAL(12,2) NOT NULL DEFAULT 0.00");
                } catch (Throwable $t) {}

                $stmt = $database->prepare("UPDATE users SET balance = COALESCE(balance, 0) + ? WHERE id = ?");
                $stmt->execute([$depositAmount, $_SESSION['user_id']]);
            } catch (Throwable $t) {
                error_log("Deposit DB error: " . $t->getMessage());
            }

            $_SESSION['deposit_success'] = 'Nạp tiền thành công! Đã cộng ' . number_format($depositAmount, 0, ',', '.') . 'đ vào tài khoản.';
        }
    }
    header('Location: ' . ($_SERVER['REQUEST_URI'] ?? '/home'));
    exit;
}

?>
<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        <?= e($pageTitle ?? 'Readly - Online eBook Store & Reader') ?>
    </title>

    <link
        rel="stylesheet"
        href="/assets/css/home.css?v=<?= time() ?>"
    >

</head>

<body class="home-page">

<header class="home-header">

    <div class="home-container header-inner">

        <!-- Logo -->

        <div class="header-logo">

            <a
                href="/home"
                style="display:flex;align-items:center;gap:8px;text-decoration:none;color:inherit;"
            >

                <div class="logo-mark">
                    <span>R</span>
                </div>

                <span class="logo-text">
                    readly
                </span>

            </a>

        </div>


        <nav class="header-nav">

            <a href="/home">
                Khám phá
            </a>

            <a href="/library">
                Thư viện
            </a>

            <a href="/home?view=register">
                Kích hoạt
            </a>

        </nav>


        <div class="header-actions">

            <form
                action="/home"
                method="GET"
                class="search-wrapper"
            >

                <input
                    type="hidden"
                    name="view"
                    value="search"
                >

                <input
                    type="text"
                    name="q"
                    placeholder="Tìm tên sách, tác giả..."
                    value="<?= e($_GET['q'] ?? '') ?>"
                >

            </form>


            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-logged-badge" style="display: flex; align-items: center; gap: 10px;">
                    <div class="user-avatar-circle" style="width: 34px; height: 34px; border-radius: 50%; background: var(--readly-primary, #087E8B); color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; text-transform: uppercase;">
                        <?= e(mb_substr($_SESSION['user_name'] ?? 'U', 0, 1, 'UTF-8')) ?>
                    </div>
                    <span class="user-display-name" style="font-weight: 600; font-size: 14px; color: var(--readly-text, #102A43);">
                        <?= e($_SESSION['user_name'] ?? 'Tài khoản') ?>
                    </span>

                    <?php if (($_SESSION['user_role'] ?? '') === 'publisher'): ?>
                        <a
                            href="/publisher-dashboard"
                            class="outline-button"
                            style="text-decoration:none;padding:4px 10px;font-size:12px;"
                        >
                            Kênh NXB
                        </a>
                    <?php elseif (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                        <a
                            href="/"
                            class="outline-button"
                            style="text-decoration:none;padding:4px 10px;font-size:12px;"
                        >
                            Admin
                        </a>
                    <?php endif; ?>

                    <!-- Balance Button (Replaces "Đọc ngay" when logged in) -->
                    <button
                        type="button"
                        onclick="openDepositModal()"
                        class="primary-button"
                        style="text-decoration:none;display:inline-flex;align-items:center;gap:6px;cursor:pointer;border:none;background:var(--readly-primary, #087E8B);color:#ffffff;padding:7px 14px;border-radius:8px;font-weight:600;font-size:14px;transition:transform 0.2s;"
                        title="Bấm để nạp tiền vào tài khoản"
                    >
                        <span>Số dư: <?= number_format($_SESSION['user_balance'] ?? 0, 0, ',', '.') ?>đ</span>
                        <span style="background: rgba(255,255,255,0.25); border-radius: 50%; width: 18px; height: 18px; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; margin-left: 2px;">+</span>
                    </button>
                </div>
            <?php else: ?>
                <a
                    href="/home?view=login"
                    class="login-link"
                >
                    Đăng nhập
                </a>

                <a
                    href="/home?view=register"
                    class="outline-button"
                    style="text-decoration:none;padding:6px 14px;font-size:14px;"
                >
                    Đăng ký
                </a>

                <a
                    href="/home?view=book-list"
                    class="primary-button"
                    style="text-decoration:none;display:inline-block;"
                >
                    Đọc ngay
                </a>
            <?php endif; ?>

        </div>

    </div>

</header>

<main>

<?php

switch ($view) {

    case 'book-detail':

        include __DIR__ . '/component/BookDetailPage.php';

        break;


    case 'book-list':

        include __DIR__ . '/component/BookListPage.php';

        break;


    case 'search':

        include __DIR__ . '/component/SearchResultsPage.php';

        break;


    case 'login':
    case 'register':

        /*
         * Login/Register đã được xử lý ở phía trên.
         * Không include lại ở đây.
         */

        break;


    case 'home':

    default:

        include __DIR__ . '/component/HomePage.php';

        break;

}

?>

</main>


<?php
include __DIR__ . '/component/Footer.php';
?>

<!-- Deposit Modal (Popup Nạp tiền mô phỏng) -->
<div id="depositModal" class="auth-modal-overlay" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px); align-items: center; justify-content: center;">
    <div class="auth-modal-backdrop" onclick="closeDepositModal()" style="position: absolute; inset: 0;"></div>
    <div class="auth-modal-container" style="position: relative; z-index: 10; width: 100%; max-width: 440px; padding: 16px;">
        <div class="auth-modal-card" style="background: #ffffff; border-radius: 16px; padding: 28px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); position: relative;">
            
            <button type="button" class="auth-modal-close" onclick="closeDepositModal()" title="Đóng" style="position: absolute; top: 16px; right: 16px; background: none; border: none; cursor: pointer; color: #6B7280; padding: 4px;">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div class="auth-header" style="text-align: center; margin-bottom: 20px;">
                <h2 class="auth-title" style="font-size: 20px; font-weight: 700; color: #102A43; margin: 0 0 6px;">Nạp tiền vào tài khoản</h2>
                <p class="auth-subtitle" style="font-size: 13px; color: #6B7280; margin: 0;">
                    Số dư hiện tại: <strong style="color: var(--readly-primary, #087E8B); font-size: 15px;"><?= number_format($_SESSION['user_balance'] ?? 0, 0, ',', '.') ?>đ</strong>
                </p>
            </div>

            <form action="/home" method="POST" class="auth-form">
                <input type="hidden" name="action" value="deposit">

                <div class="auth-form-group" style="margin-bottom: 16px;">
                    <label class="auth-label" style="font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px; display: block;">Chọn nhanh số tiền</label>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px;">
                        <button type="button" class="preset-btn" onclick="selectPreset(50000)" style="padding: 10px; border: 1.5px solid #E5E7EB; border-radius: 8px; background: #F9FAFB; font-weight: 600; color: #102A43; cursor: pointer; transition: all 0.2s;">50.000đ</button>
                        <button type="button" class="preset-btn" onclick="selectPreset(100000)" style="padding: 10px; border: 1.5px solid #E5E7EB; border-radius: 8px; background: #F9FAFB; font-weight: 600; color: #102A43; cursor: pointer; transition: all 0.2s;">100.000đ</button>
                        <button type="button" class="preset-btn" onclick="selectPreset(200000)" style="padding: 10px; border: 1.5px solid #E5E7EB; border-radius: 8px; background: #F9FAFB; font-weight: 600; color: #102A43; cursor: pointer; transition: all 0.2s;">200.000đ</button>
                        <button type="button" class="preset-btn" onclick="selectPreset(500000)" style="padding: 10px; border: 1.5px solid #E5E7EB; border-radius: 8px; background: #F9FAFB; font-weight: 600; color: #102A43; cursor: pointer; transition: all 0.2s;">500.000đ</button>
                    </div>
                </div>

                <div class="auth-form-group" style="margin-bottom: 16px;">
                    <label class="auth-label" for="deposit_amount_input" style="font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; display: block;">Số tiền muốn nạp (VNĐ)</label>
                    <input
                        id="deposit_amount_input"
                        type="number"
                        name="deposit_amount"
                        min="10000"
                        step="10000"
                        required
                        placeholder="Nhập số tiền (VD: 100000)"
                        class="auth-input"
                        style="width: 100%; padding: 10px 14px; border: 1.5px solid #D1D5DB; border-radius: 8px; font-size: 15px; font-weight: 600; color: #102A43;"
                    >
                </div>

                <div class="auth-form-group" style="margin-bottom: 20px;">
                    <label class="auth-label" style="font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; display: block;">Phương thức nạp mô phỏng</label>
                    <select class="auth-input" style="width: 100%; padding: 10px 14px; border: 1.5px solid #D1D5DB; border-radius: 8px; font-size: 14px; background: #ffffff; color: #102A43;">
                        <option value="momo">Ví MoMo (Mô phỏng)</option>
                        <option value="vnpay">VNPay QR (Mô phỏng)</option>
                        <option value="banking">Chuyển khoản Ngân hàng (Mô phỏng)</option>
                    </select>
                </div>

                <button type="submit" class="auth-submit-btn" style="width: 100%; padding: 12px; background: var(--readly-primary, #087E8B); color: #ffffff; border: none; border-radius: 10px; font-weight: 700; font-size: 15px; cursor: pointer; box-shadow: 0 4px 12px rgba(8, 126, 139, 0.25);">
                    Nạp tiền ngay
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function openDepositModal() {
    var modal = document.getElementById('depositModal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

function closeDepositModal() {
    var modal = document.getElementById('depositModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function selectPreset(amount) {
    var input = document.getElementById('deposit_amount_input');
    if (input) {
        input.value = amount;
    }
}

function showToast(message, type = 'success') {
    var container = document.getElementById('toastNotificationContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastNotificationContainer';
        container.style.cssText = 'position: fixed; top: 24px; right: 24px; z-index: 999999; display: flex; flex-direction: column; gap: 10px; pointer-events: none;';
        document.body.appendChild(container);
    }

    var toast = document.createElement('div');
    var isError = type === 'error';
    var bg = isError ? '#EF4444' : '#087E8B';
    var icon = isError ? '⚠️' : '✓';

    toast.style.cssText = 'background: ' + bg + '; color: #ffffff; padding: 12px 20px; border-radius: 10px; font-weight: 600; font-size: 14px; box-shadow: 0 10px 25px rgba(0,0,0,0.18); display: flex; align-items: center; gap: 10px; pointer-events: auto; opacity: 0; transform: translateY(-10px); transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);';
    toast.innerHTML = '<span>' + icon + '</span><span>' + message + '</span>';

    container.appendChild(toast);

    setTimeout(function() {
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
    }, 50);

    setTimeout(function() {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-10px)';
        setTimeout(function() {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3200);
}
</script>

<?php if (!empty($_SESSION['deposit_success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast(<?= json_encode($_SESSION['deposit_success']) ?>, 'success');
        });
    </script>
    <?php unset($_SESSION['deposit_success']); ?>
<?php endif; ?>

</body>
</html>