<?php
/**
 * Admin Book Approval Page
 */
declare(strict_types=1);

$categoryColors = [
    'Công nghệ' => '#3b82f6', 'Kinh doanh' => '#16a34a',
    'Tâm lý'    => '#7c3aed', 'Lịch sử'    => '#d97706',
    'Marketing' => '#db2777', 'Giáo dục'   => '#0284c7',
];
?>
<div class="page-section">
    <!-- Intro / toolbar -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
        <p style="color:#64748b;font-size:0.875rem;">Xét duyệt sách mới từ các nhà phát hành. Tổng cộng <strong style="color:#102A43;"><?= count($pendingBooks) ?> sách</strong> đang chờ kiểm duyệt.</p>
        <div style="display:flex;gap:0.75rem;">
            <button type="button" style="display:flex;align-items:center;gap:6px;padding:0.5rem 1rem;border:1px solid #e2e8f0;border-radius:8px;background:#fff;color:#334155;font-size:0.875rem;cursor:pointer;" data-toast="Đang xuất danh sách sách chờ duyệt...">
                ⇩ Xuất danh sách
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid" style="margin-bottom:1.5rem;">
        <div class="stat-card" style="border-left:4px solid #f59e0b;">
            <div class="stat-card-content">
                <div class="stat-label">Chờ duyệt</div>
                <div class="stat-value" id="statPendingCount"><?= count($pendingBooks) ?></div>
                <div class="stat-growth" style="color:#f59e0b;"><span>Cần xử lý</span></div>
            </div>
        </div>
        <div class="stat-card" style="border-left:4px solid #16a34a;">
            <div class="stat-card-content">
                <div class="stat-label">Đã duyệt hôm nay</div>
                <div class="stat-value" id="statApprovedTodayCount"><?= (int)($approvedTodayCount ?? 0) ?></div>
                <div class="stat-growth"><span>Hôm nay</span></div>
            </div>
        </div>
        <div class="stat-card" style="border-left:4px solid #ef4444;">
            <div class="stat-card-content">
                <div class="stat-label">Từ chối hôm nay</div>
                <div class="stat-value" id="statRejectedTodayCount"><?= (int)($rejectedTodayCount ?? 0) ?></div>
                <div class="stat-growth" style="color:#ef4444;"><span>Cần phản hồi NXB</span></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-content">
                <div class="stat-label">TB thời gian duyệt</div>
                <div class="stat-value">1.4<small style="font-size:1rem;">ngày</small></div>
                <div class="stat-growth"><span>-0.3 ngày so với tuần trước</span></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-content">
                <div class="stat-label">Tổng NXB gửi</div>
                <div class="stat-value">24</div>
                <div class="stat-growth"><span>Nhà xuất bản tháng này</span></div>
            </div>
        </div>
    </div>

    <!-- Category filter chips -->
    <div style="display:flex;gap:0.5rem;margin-bottom:1rem;flex-wrap:wrap;" id="catFilterBar">
        <button class="cat-chip is-selected" data-cat="" style="padding:4px 14px;border-radius:20px;border:1px solid #087E8B;background:#087E8B;color:#fff;font-size:0.8rem;cursor:pointer;font-weight:600;">
            Tất cả
        </button>
        <?php foreach (array_unique(array_column($pendingBooks, 'category')) as $cat): ?>
        <button class="cat-chip" data-cat="<?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?>"
                style="padding:4px 14px;border-radius:20px;border:1px solid #e2e8f0;background:#fff;color:#334155;font-size:0.8rem;cursor:pointer;font-weight:500;">
            <?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?>
        </button>
        <?php endforeach; ?>
    </div>

    <!-- Table -->
    <div class="bottom-panel" style="padding:0;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;" id="booksTable">
            <thead>
                <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;letter-spacing:.05em;color:#64748b;font-weight:600;">Sách</th>
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;color:#64748b;font-weight:600;">Tác giả</th>
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;color:#64748b;font-weight:600;">Nhà phát hành</th>
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;color:#64748b;font-weight:600;">Thể loại</th>
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;color:#64748b;font-weight:600;">Ngày gửi</th>
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;color:#64748b;font-weight:600;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingBooks as $book): ?>
                <tr class="book-row" data-cat="<?= htmlspecialchars($book['category'], ENT_QUOTES, 'UTF-8') ?>" data-book-id="<?= (int)($book['id'] ?? 0) ?>" data-book-title="<?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>"
                    style="border-bottom:1px solid #f1f5f9;transition:background .15s;"
                    onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <td style="padding:0.875rem 1.25rem;">
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div style="width:36px;height:48px;background:linear-gradient(160deg,#087E8B,#052B41);border-radius:4px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.8rem;font-weight:700;flex-shrink:0;box-shadow:0 2px 6px rgba(8,126,139,.3);">
                                <?= mb_strtoupper(mb_substr($book['title'], 0, 1, 'UTF-8'), 'UTF-8') ?>
                            </div>
                            <span style="font-size:0.875rem;font-weight:700;color:#102A43;"><?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </td>
                    <td style="padding:0.875rem 1.25rem;font-size:0.875rem;color:#64748b;"><?= htmlspecialchars($book['author'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td style="padding:0.875rem 1.25rem;font-size:0.875rem;color:#64748b;"><?= htmlspecialchars($book['publisher'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td style="padding:0.875rem 1.25rem;">
                        <?php $color = $categoryColors[$book['category']] ?? '#087E8B'; ?>
                        <span style="font-size:0.75rem;padding:2px 10px;background:<?= $color ?>18;color:<?= $color ?>;border-radius:6px;font-weight:600;">
                            <?= htmlspecialchars($book['category'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td style="padding:0.875rem 1.25rem;font-size:0.8rem;color:#94a3b8;"><?= htmlspecialchars($book['date'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td style="padding:0.875rem 1.25rem;">
                        <div class="action-buttons-group">
                            <button type="button" class="btn-action btn-approve" data-book-id="<?= (int)($book['id'] ?? 0) ?>" data-book-title="<?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>" data-toast="Đã duyệt thành công: <?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                Duyệt
                            </button>
                            <button type="button" class="btn-action btn-reject" data-book-id="<?= (int)($book['id'] ?? 0) ?>" data-book-title="<?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>" data-toast="Đã từ chối: <?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                Từ chối
                            </button>
                            <button type="button" class="btn-action btn-request-edit" data-book-id="<?= (int)($book['id'] ?? 0) ?>" data-book-title="<?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>" data-toast="Đã gửi yêu cầu sửa: <?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Yêu cầu sửa
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div style="padding:0.75rem 1.25rem;background:#f8fafc;border-top:1px solid #e2e8f0;">
            <span style="font-size:0.75rem;color:#64748b;" id="booksCount"><?= count($pendingBooks) ?> sách đang chờ duyệt</span>
        </div>
    </div>
</div>

<script>
(function() {
    const chips = document.querySelectorAll('.cat-chip');
    const rows  = document.querySelectorAll('.book-row');
    chips.forEach(chip => {
        chip.addEventListener('click', function() {
            chips.forEach(c => {
                c.classList.remove('is-selected');
                c.style.background = '#fff';
                c.style.color = '#334155';
                c.style.borderColor = '#e2e8f0';
            });
            this.classList.add('is-selected');
            this.style.background = '#087E8B';
            this.style.color = '#fff';
            this.style.borderColor = '#087E8B';
            const cat = this.dataset.cat;
            let count = 0;
            rows.forEach(row => {
                const match = !cat || row.dataset.cat === cat;
                row.hidden = !match;
                if (match) count++;
            });
            document.getElementById('booksCount').textContent = count + ' sách đang chờ duyệt';
        });
    });
})();
</script>
