<?php
/**
 * Admin Header Component — dynamic title from $currentTitle
 */
declare(strict_types=1);
?>
<header class="admin-header">
    <div class="admin-header-container">
        <div>
            <h1 class="admin-header-title"><?= htmlspecialchars($currentTitle ?? 'Dashboard Hệ Thống', ENT_QUOTES, 'UTF-8') ?></h1>
        </div>

        <!-- Search Bar -->
        <div class="admin-search-wrapper">
            <svg class="admin-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text"
                   class="admin-search-input"
                   placeholder="Tìm kiếm..."
                   aria-label="Tìm kiếm hệ thống" />
        </div>

        <!-- Actions -->
        <div class="admin-header-actions">
        </div>
    </div>
</header>
