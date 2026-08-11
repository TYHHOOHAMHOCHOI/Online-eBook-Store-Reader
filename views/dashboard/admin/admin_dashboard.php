<?php
/**
 * Main Admin Dashboard View Layout
 */
declare(strict_types=1);
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin Dashboard', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/css/admin-dashboard.css">
</head>
<body class="admin-body">
    <div class="admin-layout">
        <!-- Sidebar Navigation -->
        <?php require __DIR__ . '/component/AdminSidebar.php'; ?>

        <!-- Main Workspace Area -->
        <main class="admin-main">
            <!-- Header Bar -->
            <?php require __DIR__ . '/component/AdminHeader.php'; ?>

            <!-- Dashboard Content Container -->
            <div class="admin-content">
                <!-- Stats Row -->
                <?php require __DIR__ . '/component/AdminStats.php'; ?>

                <!-- Revenue Chart Section -->
                <?php require __DIR__ . '/component/RevenueChart.php'; ?>

                <!-- Bottom Grid (Books & Transactions) -->
                <?php require __DIR__ . '/component/AdminBottomSection.php'; ?>
            </div>
        </main>
    </div>

    <!-- Toast Notification Container -->
    <div id="adminToast" class="toast-notification" role="status" aria-live="polite"></div>

    <script src="/assets/js/admin-dashboard.js"></script>
</body>
</html>
