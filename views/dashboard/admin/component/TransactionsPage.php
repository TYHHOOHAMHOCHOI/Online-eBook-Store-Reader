<?php
/**
 * Admin Transactions & Reconciliation Page
 */
declare(strict_types=1);

$totalRevenue   = array_sum(array_column(array_filter($recentTransactions, fn($t) => $t['status'] === 'Thành công'), 'amount'));
$successCount   = count(array_filter($recentTransactions, fn($t) => $t['status'] === 'Thành công'));
$failCount      = count(array_filter($recentTransactions, fn($t) => $t['status'] === 'Thất bại'));
$pendingCount   = count(array_filter($recentTransactions, fn($t) => $t['status'] === 'Chờ xử lý'));
$avgOrder       = $successCount > 0 ? round($totalRevenue / $successCount) : 0;

$methodIcons = ['Chuyển khoản' => '🏦', 'Ví điện tử' => '📱', 'Thẻ tín dụng' => '💳'];
?>
<div class="page-section">
    <!-- Toolbar -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
        <p style="color:#64748b;font-size:0.875rem;">Quản lý toàn bộ giao dịch mua sách và đối soát doanh thu với nhà phát hành.</p>
        <div style="display:flex;gap:0.75rem;">
            <button type="button" style="display:flex;align-items:center;gap:6px;padding:0.5rem 1rem;border:1px solid #e2e8f0;border-radius:8px;background:#fff;color:#334155;font-size:0.875rem;cursor:pointer;" data-toast="Đang xuất báo cáo đối soát...">
                ⇩ Xuất báo cáo đối soát
            </button>
            <button type="button" class="btn-action btn-approve" style="padding:0.5rem 1.25rem;border-radius:8px;" data-toast="Đã khởi động xử lý đối soát hàng loạt.">
                Xử lý đối soát hàng loạt
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid" style="margin-bottom:1.5rem;">
        <div class="stat-card" style="border-left:4px solid #087E8B;">
            <div class="stat-card-content">
                <div class="stat-label">Tổng doanh thu</div>
                <div class="stat-value" style="font-size:1.25rem;"><?= number_format($totalRevenue, 0, ',', '.') ?>đ</div>
                <div class="stat-growth"><span>+23% so với tháng trước</span></div>
            </div>
        </div>
        <div class="stat-card" style="border-left:4px solid #16a34a;">
            <div class="stat-card-content">
                <div class="stat-label">Thành công</div>
                <div class="stat-value" style="color:#16a34a;"><?= $successCount ?></div>
                <div class="stat-growth"><span><?= $successCount > 0 ? round($successCount / count($recentTransactions) * 100) : 0 ?>% tổng GD</span></div>
            </div>
        </div>
        <div class="stat-card" style="border-left:4px solid #f59e0b;">
            <div class="stat-card-content">
                <div class="stat-label">Chờ xử lý</div>
                <div class="stat-value" style="color:#f59e0b;"><?= $pendingCount ?></div>
                <div class="stat-growth" style="color:#f59e0b;"><span>Cần xử lý ngay</span></div>
            </div>
        </div>
        <div class="stat-card" style="border-left:4px solid #ef4444;">
            <div class="stat-card-content">
                <div class="stat-label">Thất bại</div>
                <div class="stat-value" style="color:#ef4444;"><?= $failCount ?></div>
                <div class="stat-growth" style="color:#ef4444;"><span>Cần kiểm tra</span></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-content">
                <div class="stat-label">Giá trị TB/đơn</div>
                <div class="stat-value" style="font-size:1.2rem;"><?= number_format($avgOrder, 0, ',', '.') ?>đ</div>
                <div class="stat-growth"><span>Mỗi đơn hàng thành công</span></div>
            </div>
        </div>
    </div>

    <!-- Filter bar -->
    <div style="display:flex;gap:0.5rem;margin-bottom:1rem;" id="statusFilterBar">
        <?php foreach (['Tất cả' => '', 'Thành công' => 'Thành công', 'Chờ xử lý' => 'Chờ xử lý', 'Thất bại' => 'Thất bại'] as $label => $val): ?>
        <button class="status-chip <?= $val === '' ? 'is-active-chip' : '' ?>" data-status="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>"
            style="padding:5px 14px;border-radius:20px;border:1px solid <?= $val === '' ? '#087E8B' : '#e2e8f0' ?>;
                   background:<?= $val === '' ? '#087E8B' : '#fff' ?>;
                   color:<?= $val === '' ? '#fff' : '#334155' ?>;
                   font-size:0.8rem;cursor:pointer;font-weight:600;">
            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
        </button>
        <?php endforeach; ?>
    </div>

    <!-- Table -->
    <div class="bottom-panel" style="padding:0;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;" id="txTable">
            <thead>
                <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;color:#64748b;font-weight:600;">Mã GD</th>
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;color:#64748b;font-weight:600;">Người mua</th>
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;color:#64748b;font-weight:600;">Số tiền</th>
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;color:#64748b;font-weight:600;">Phương thức</th>
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;color:#64748b;font-weight:600;">Ngày</th>
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;color:#64748b;font-weight:600;">Trạng thái</th>
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;color:#64748b;font-weight:600;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentTransactions as $tx):
                    $icon = $methodIcons[$tx['method']] ?? '💰';
                    if ($tx['status'] === 'Thành công') {
                        $badge = 'background:#f0fdf4;color:#16a34a;border-color:#bbf7d0;';
                    } elseif ($tx['status'] === 'Chờ xử lý') {
                        $badge = 'background:#fefce8;color:#ca8a04;border-color:#fef08a;';
                    } else {
                        $badge = 'background:#fef2f2;color:#dc2626;border-color:#fecaca;';
                    }
                ?>
                <tr class="tx-row" data-status="<?= htmlspecialchars($tx['status'], ENT_QUOTES, 'UTF-8') ?>"
                    style="border-bottom:1px solid #f1f5f9;transition:background .15s;"
                    onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <td style="padding:0.875rem 1.25rem;">
                        <span style="font-family:monospace;font-size:0.8rem;font-weight:700;color:#102A43;background:#f1f5f9;padding:2px 8px;border-radius:5px;">
                            #<?= htmlspecialchars($tx['id'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td style="padding:0.875rem 1.25rem;font-size:0.875rem;font-weight:600;color:#334155;"><?= htmlspecialchars($tx['buyer'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td style="padding:0.875rem 1.25rem;font-size:0.875rem;font-weight:700;color:#102A43;"><?= number_format($tx['amount'], 0, ',', '.') ?>đ</td>
                    <td style="padding:0.875rem 1.25rem;font-size:0.875rem;color:#64748b;"><?= $icon ?> <?= htmlspecialchars($tx['method'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td style="padding:0.875rem 1.25rem;font-size:0.8rem;color:#94a3b8;"><?= htmlspecialchars($tx['date'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td style="padding:0.875rem 1.25rem;">
                        <span style="font-size:0.75rem;padding:3px 10px;border-radius:6px;border:1px solid;font-weight:600;<?= $badge ?>">
                            <?= htmlspecialchars($tx['status'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td style="padding:0.875rem 1.25rem;">
                        <div style="display:flex;gap:0.5rem;">
                            <button type="button" title="Xem chi tiết" style="padding:6px;border:none;background:transparent;border-radius:6px;cursor:pointer;color:#64748b;" data-toast="Xem chi tiết giao dịch #<?= htmlspecialchars($tx['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                            <button type="button" title="Thêm tùy chọn" style="padding:6px;border:none;background:transparent;border-radius:6px;cursor:pointer;color:#64748b;" data-toast="Tùy chọn giao dịch #<?= htmlspecialchars($tx['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div style="padding:0.75rem 1.25rem;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:0.75rem;color:#64748b;" id="txCount"><?= count($recentTransactions) ?> giao dịch</span>
            <span style="font-size:0.75rem;font-weight:700;color:#087E8B;">Tổng thành công: <?= number_format($totalRevenue, 0, ',', '.') ?>đ</span>
        </div>
    </div>
</div>

<script>
(function() {
    const chips = document.querySelectorAll('.status-chip');
    const rows  = document.querySelectorAll('.tx-row');
    chips.forEach(chip => {
        chip.addEventListener('click', function() {
            chips.forEach(c => {
                c.classList.remove('is-active-chip');
                c.style.background = '#fff';
                c.style.color = '#334155';
                c.style.borderColor = '#e2e8f0';
            });
            this.classList.add('is-active-chip');
            this.style.background = '#087E8B';
            this.style.color = '#fff';
            this.style.borderColor = '#087E8B';
            const status = this.dataset.status;
            let count = 0;
            rows.forEach(row => {
                const match = !status || row.dataset.status === status;
                row.hidden = !match;
                if (match) count++;
            });
            document.getElementById('txCount').textContent = count + ' giao dịch';
        });
    });
})();
</script>
