<?php
/**
 * Revenue Chart Component
 */
declare(strict_types=1);

$tabs = ['Hôm nay', 'Tuần này', 'Tháng này', 'Năm nay'];
$activeTab = 'Tháng này';
?>
<div class="chart-panel">
    <div class="chart-header">
        <h2 class="chart-title">Doanh thu toàn hệ thống</h2>
        <div class="tab-switcher">
            <?php foreach ($tabs as $tab): ?>
                <button type="button" 
                        class="tab-btn <?= $tab === $activeTab ? 'active' : '' ?>" 
                        data-tab="<?= htmlspecialchars($tab, ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($tab, ENT_QUOTES, 'UTF-8') ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="chart-container">
        <canvas id="revenueBarChart" data-chart='<?= htmlspecialchars(json_encode($adminPeriodData ?? [], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>'></canvas>
    </div>
</div>
