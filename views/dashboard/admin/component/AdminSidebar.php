<?php
/**
 * Admin Sidebar Component — with ?page= routing links
 */
declare(strict_types=1);

$currentPage = $_GET['page'] ?? 'dashboard';

$pendingCount = isset($pendingBooks) && is_array($pendingBooks) ? count($pendingBooks) : 0;
$pendingBadge = $pendingCount > 0 ? (string)$pendingCount : null;

$navItems = [
    ['page' => 'dashboard',    'icon' => 'dashboard', 'label' => 'Dashboard',              'badge' => null],
    ['page' => 'users',        'icon' => 'users',     'label' => 'Quản lý người dùng',     'badge' => null],
    ['page' => 'books',        'icon' => 'book',      'label' => 'Duyệt sách',             'badge' => $pendingBadge],
    ['page' => 'categories',   'icon' => 'folder',    'label' => 'Quản lý danh mục',       'badge' => null],
    ['page' => 'transactions', 'icon' => 'receipt',   'label' => 'Giao dịch & Đối soát',  'badge' => null],
];
?>
<aside class="admin-sidebar">
    <!-- Logo -->
    <div class="admin-sidebar-logo">
        <a href="/" style="display:flex;align-items:center;gap:10px;text-decoration:none;color:inherit;">
            <div class="admin-logo-mark">R</div>
            <div>
                <span class="admin-logo-text">readly</span>
                <small style="display:block;font-size:10px;color:#087E8B;font-weight:600;letter-spacing:.05em;">Admin Panel</small>
            </div>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="admin-nav" aria-label="Menu Admin">
        <?php foreach ($navItems as $item):
            $isActive = ($currentPage === $item['page']);
        ?>
            <a href="/?page=<?= htmlspecialchars($item['page'], ENT_QUOTES, 'UTF-8') ?>"
               class="admin-nav-item <?= $isActive ? 'is-active' : '' ?>"
               style="text-decoration:none;">

                <?php if ($item['icon'] === 'dashboard'): ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>
                <?php elseif ($item['icon'] === 'users'): ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <?php elseif ($item['icon'] === 'book'): ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                <?php elseif ($item['icon'] === 'folder'): ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                <?php elseif ($item['icon'] === 'receipt'): ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1z"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="10" x2="16" y2="10"/><line x1="8" y1="14" x2="12" y2="14"/></svg>
                <?php endif; ?>

                <span><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></span>

                <?php if ($item['badge']): ?>
                    <span class="admin-nav-badge" <?= $item['page'] === 'books' ? 'id="navPendingBadge"' : '' ?>><?= htmlspecialchars($item['badge'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Admin Profile -->
    <div class="admin-profile">
        <div class="admin-profile-info">
            <div class="admin-avatar">AD</div>
            <div class="admin-user-details">
                <span class="admin-user-name">Admin User</span>
                <span class="admin-user-role">Super Admin</span>
            </div>
        </div>
        <button type="button" class="admin-logout-btn" data-toast="Đã gửi yêu cầu đăng xuất khỏi hệ thống.">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            <span>Đăng xuất</span>
        </button>
    </div>
</aside>
