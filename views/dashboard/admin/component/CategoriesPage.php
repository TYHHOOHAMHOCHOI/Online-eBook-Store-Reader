<?php
/**
 * Admin Categories Management Page
 */
declare(strict_types=1);

$barColors = ['#087E8B', '#16a34a', '#7c3aed', '#d97706', '#3b82f6'];
$totalBooks = array_sum(array_column($categories, 'books'));
$maxBooks   = max(array_column($categories, 'books'));
?>
<div class="page-section">
    <!-- Toolbar -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
        <p style="color:#64748b;font-size:0.875rem;">Quản lý thể loại và phân loại sách trên hệ thống.</p>
        <button type="button" class="btn-action btn-approve" style="padding:0.5rem 1rem;border-radius:8px;" data-toast="Chức năng thêm danh mục sẽ được kết nối với API.">
            + Thêm danh mục
        </button>
    </div>

    <!-- Quick stats -->
    <div class="stats-grid" style="margin-bottom:1.5rem;">
        <div class="stat-card" style="border-left:4px solid #087E8B;">
            <div class="stat-card-content">
                <div class="stat-label">Tổng thể loại</div>
                <div class="stat-value"><?= count($categories) ?></div>
            </div>
        </div>
        <div class="stat-card" style="border-left:4px solid #16a34a;">
            <div class="stat-card-content">
                <div class="stat-label">Tổng số sách</div>
                <div class="stat-value"><?= number_format($totalBooks) ?></div>
            </div>
        </div>
        <div class="stat-card" style="border-left:4px solid #3b82f6;">
            <div class="stat-card-content">
                <div class="stat-label">TB sách/thể loại</div>
                <div class="stat-value"><?= number_format(round($totalBooks / count($categories))) ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-content">
                <div class="stat-label">Thể loại lớn nhất</div>
                <div class="stat-value" style="font-size:1.1rem;"><?= htmlspecialchars($categories[array_search($maxBooks, array_column($categories, 'books'))]['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                <div class="stat-growth"><span><?= number_format($maxBooks) ?> sách</span></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-content">
                <div class="stat-label">NXB & Tác giả</div>
                <div class="stat-value"><?= number_format($pubAuthorTotal ?? 0) ?></div>
                <div class="stat-growth"><span><?= number_format($totalPublishers ?? 0) ?> NXB • <?= number_format($totalAuthors ?? 0) ?> tác giả</span></div>
            </div>
        </div>
    </div>

    <!-- Distribution bar chart -->
    <div class="bottom-panel" style="margin-bottom:1.5rem;">
        <h3 style="font-size:0.9rem;font-weight:700;color:#102A43;margin-bottom:1rem;">Phân bố sách theo thể loại</h3>
        <div style="display:grid;grid-template-columns:repeat(2, 1fr);gap:0.75rem 1.5rem;">
        <?php foreach ($categories as $i => $cat): ?>
        <?php $pct = $totalBooks > 0 ? round(($cat['books'] / $totalBooks) * 100) : 0; ?>
        <?php $barPct = $maxBooks > 0 ? round(($cat['books'] / $maxBooks) * 100) : 0; ?>
        <div style="display:flex;align-items:center;gap:1rem;">
            <span style="font-size:0.8rem;font-weight:600;color:#334155;width:90px;flex-shrink:0;"><?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?></span>
            <div style="flex:1;background:#f1f5f9;border-radius:99px;height:10px;overflow:hidden;">
                <div style="width:<?= $barPct ?>%;height:100%;background:<?= $barColors[$i % count($barColors)] ?>;border-radius:99px;transition:width .6s;"></div>
            </div>
            <span style="font-size:0.8rem;font-weight:700;color:#64748b;width:70px;text-align:right;flex-shrink:0;">
                <?= number_format($cat['books']) ?> (<?= $pct ?>%)
            </span>
        </div>
        <?php endforeach; ?>
        </div>
    </div>

    <!-- Table -->
    <div class="bottom-panel" style="padding:0;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;color:#64748b;font-weight:600;">Tên danh mục</th>
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;color:#64748b;font-weight:600;">Loại</th>
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;color:#64748b;font-weight:600;">Số sách</th>
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;color:#64748b;font-weight:600;">Tỷ lệ</th>
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;color:#64748b;font-weight:600;">Ngày tạo</th>
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;color:#64748b;font-weight:600;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $i => $cat):
                    $pct = $totalBooks > 0 ? round(($cat['books'] / $totalBooks) * 100) : 0;
                    $color = $barColors[$i % count($barColors)];
                ?>
                <tr style="border-bottom:1px solid #f1f5f9;transition:background .15s;"
                    onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <td style="padding:0.875rem 1.25rem;">
                        <div style="display:flex;align-items:center;gap:0.65rem;">
                            <span style="width:10px;height:10px;border-radius:50%;background:<?= $color ?>;flex-shrink:0;display:inline-block;"></span>
                            <span style="font-size:0.875rem;font-weight:700;color:#102A43;"><?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </td>
                    <td style="padding:0.875rem 1.25rem;">
                        <span style="font-size:0.75rem;padding:2px 10px;background:#eff6ff;color:#1d4ed8;border-radius:6px;font-weight:600;"><?= htmlspecialchars($cat['type'], ENT_QUOTES, 'UTF-8') ?></span>
                    </td>
                    <td style="padding:0.875rem 1.25rem;font-size:0.875rem;font-weight:700;color:#334155;"><?= number_format($cat['books']) ?></td>
                    <td style="padding:0.875rem 1.25rem;">
                        <div style="display:flex;align-items:center;gap:0.5rem;">
                            <div style="width:80px;background:#f1f5f9;border-radius:99px;height:6px;overflow:hidden;">
                                <div style="width:<?= $pct ?>%;height:100%;background:<?= $color ?>;border-radius:99px;"></div>
                            </div>
                            <span style="font-size:0.75rem;color:#94a3b8;font-weight:600;"><?= $pct ?>%</span>
                        </div>
                    </td>
                    <td style="padding:0.875rem 1.25rem;font-size:0.8rem;color:#94a3b8;"><?= htmlspecialchars($cat['createdDate'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td style="padding:0.875rem 1.25rem;">
                        <div style="display:flex;gap:0.5rem;">
                            <button type="button" title="Chỉnh sửa" style="padding:6px;border:none;background:transparent;border-radius:6px;cursor:pointer;color:#64748b;" data-toast="Chỉnh sửa danh mục: <?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <button type="button" title="Xóa" style="padding:6px;border:none;background:transparent;border-radius:6px;cursor:pointer;color:#ef4444;" data-toast="Xóa danh mục: <?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?> (cần xác nhận)">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div style="padding:0.75rem 1.25rem;background:#f8fafc;border-top:1px solid #e2e8f0;">
            <span style="font-size:0.75rem;color:#64748b;"><?= count($categories) ?> thể loại</span>
        </div>
    </div>
</div>
