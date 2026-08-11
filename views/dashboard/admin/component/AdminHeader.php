<?php
/**
 * Admin Header Component
 */
declare(strict_types=1);
?>
<header class="admin-header">
    <div class="admin-header-container">
        <h1 class="admin-header-title">Dashboard Hệ Thống</h1>

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
            <button type="button" class="btn-report" data-toast="Đang xuất báo cáo tổng quan hệ thống...">
                Báo cáo hệ thống
            </button>
            <button type="button" class="btn-notification" aria-label="Thông báo" data-toast="Bạn có 3 thông báo mới chưa đọc.">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
                <span class="notification-badge"></span>
            </button>
        </div>
    </div>
</header>
