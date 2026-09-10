<?php
/**
 * Admin Stats Component
 */
declare(strict_types=1);

if (!isset($stats) || !is_array($stats)) {
    $stats = [];
}
?>
<div class="stats-grid">
    <?php foreach ($stats as $stat): ?>
        <div class="stat-card">
            <div class="stat-card-content">
                <div class="stat-label"><?= htmlspecialchars($stat['label'], ENT_QUOTES, 'UTF-8') ?></div>
                <div class="stat-value"><?= htmlspecialchars($stat['value'], ENT_QUOTES, 'UTF-8') ?></div>
                <div class="stat-growth">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                        <polyline points="17 6 23 6 23 12"/>
                    </svg>
                    <span><?= htmlspecialchars($stat['growth'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            </div>
            <div class="stat-icon-bg" style="color: <?= htmlspecialchars($stat['color'], ENT_QUOTES, 'UTF-8') ?>;">
                <?php if ($stat['icon'] === 'users'): ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <?php elseif ($stat['icon'] === 'publisher'): ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                <?php elseif ($stat['icon'] === 'file-text'): ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                <?php elseif ($stat['icon'] === 'shopping-cart'): ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                <?php elseif ($stat['icon'] === 'credit-card'): ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
