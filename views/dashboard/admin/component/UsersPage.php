<?php
/**
 * Admin Users Management Page
 */
declare(strict_types=1);
?>
<div class="page-section">
    <!-- Page intro -->
    <div class="page-intro" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
        <div>
            <p style="color:#64748b;font-size:0.875rem;">Quản lý tài khoản độc giả, nhà phát hành và phân quyền hệ thống.</p>
        </div>
    </div>

    <!-- Quick stats -->
    <div class="stats-grid" style="margin-bottom:1.5rem;">
        <div class="stat-card" style="border-left:4px solid #087E8B;">
            <div class="stat-card-content">
                <div class="stat-label">Tổng người dùng</div>
                <div class="stat-value"><?= count($users) ?></div>
                <div class="stat-growth"><span>+12% tháng này</span></div>
            </div>
        </div>
        <div class="stat-card" style="border-left:4px solid #16a34a;">
            <div class="stat-card-content">
                <div class="stat-label">Đang hoạt động</div>
                <div class="stat-value"><?= count(array_filter($users, fn($u) => $u['status'] === 'Hoạt động')) ?></div>
                <div class="stat-growth"><span>Tài khoản active</span></div>
            </div>
        </div>
        <div class="stat-card" style="border-left:4px solid #3b82f6;">
            <div class="stat-card-content">
                <div class="stat-label">Nhà phát hành</div>
                <div class="stat-value"><?= count(array_filter($users, fn($u) => $u['role'] === 'Publisher')) ?></div>
                <div class="stat-growth"><span>Đối tác xuất bản</span></div>
            </div>
        </div>
        <div class="stat-card" style="border-left:4px solid #ef4444;">
            <div class="stat-card-content">
                <div class="stat-label">Bị khóa</div>
                <div class="stat-value"><?= count(array_filter($users, fn($u) => $u['status'] === 'Bị khóa')) ?></div>
                <div class="stat-growth"><span>Tài khoản bị hạn chế</span></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-content">
                <div class="stat-label">Placeholder</div>
                <div class="stat-value">—</div>
            </div>
        </div>
    </div>

    <!-- Filter bar -->
    <div style="display:flex;gap:0.75rem;margin-bottom:1rem;align-items:center;">
        <label style="display:flex;align-items:center;gap:0.5rem;border:1px solid #e2e8f0;border-radius:8px;padding:0.5rem 0.75rem;background:#fff;flex:1;max-width:300px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="search" placeholder="Tìm tên hoặc email..." data-user-search style="border:none;outline:none;font-size:0.875rem;width:100%;">
        </label>
        <select style="border:1px solid #e2e8f0;border-radius:8px;padding:0.5rem 0.75rem;font-size:0.875rem;background:#fff;color:#334155;" id="roleFilter">
            <option value="">Tất cả vai trò</option>
            <option value="User">User</option>
            <option value="Publisher">Publisher</option>
        </select>
        <select style="border:1px solid #e2e8f0;border-radius:8px;padding:0.5rem 0.75rem;font-size:0.875rem;background:#fff;color:#334155;" id="statusFilter">
            <option value="">Tất cả trạng thái</option>
            <option value="Hoạt động">Hoạt động</option>
            <option value="Bị khóa">Bị khóa</option>
        </select>
    </div>

    <!-- Table -->
    <div class="bottom-panel" style="padding:0;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;" id="usersTable">
            <thead>
                <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;letter-spacing:.05em;color:#64748b;font-weight:600;">Người dùng</th>
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;letter-spacing:.05em;color:#64748b;font-weight:600;">Email</th>
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;letter-spacing:.05em;color:#64748b;font-weight:600;">Vai trò</th>
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;letter-spacing:.05em;color:#64748b;font-weight:600;">Trạng thái</th>
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;letter-spacing:.05em;color:#64748b;font-weight:600;">Ngày tham gia</th>
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;letter-spacing:.05em;color:#64748b;font-weight:600;">Đơn hàng</th>
                    <th style="padding:0.875rem 1.25rem;text-align:left;font-size:0.7rem;text-transform:uppercase;letter-spacing:.05em;color:#64748b;font-weight:600;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr style="border-bottom:1px solid #f1f5f9;transition:background .15s;" class="user-row"
                    data-name="<?= htmlspecialchars(strtolower($user['name']), ENT_QUOTES, 'UTF-8') ?>"
                    data-email="<?= htmlspecialchars(strtolower($user['email']), ENT_QUOTES, 'UTF-8') ?>"
                    data-role="<?= htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8') ?>"
                    data-status="<?= htmlspecialchars($user['status'], ENT_QUOTES, 'UTF-8') ?>"
                    onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''"
                >
                    <td style="padding:0.875rem 1.25rem;">
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div style="width:36px;height:36px;background:linear-gradient(135deg,#087E8B,#052B41);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.75rem;font-weight:700;flex-shrink:0;">
                                <?= mb_strtoupper(mb_substr($user['name'], 0, 1, 'UTF-8'), 'UTF-8') ?>
                            </div>
                            <span style="font-size:0.875rem;font-weight:600;color:#102A43;"><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </td>
                    <td style="padding:0.875rem 1.25rem;font-size:0.875rem;color:#64748b;"><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td style="padding:0.875rem 1.25rem;">
                        <?php if ($user['role'] === 'Publisher'): ?>
                            <span style="font-size:0.75rem;padding:2px 10px;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;border-radius:6px;font-weight:600;">Publisher</span>
                        <?php else: ?>
                            <span style="font-size:0.75rem;padding:2px 10px;background:#f1f5f9;color:#475569;border:1px solid #cbd5e1;border-radius:6px;font-weight:600;">User</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:0.875rem 1.25rem;">
                        <?php if ($user['status'] === 'Hoạt động'): ?>
                            <span style="display:flex;align-items:center;gap:5px;font-size:0.8rem;color:#16a34a;font-weight:600;">
                                <span style="width:7px;height:7px;background:#22c55e;border-radius:50%;display:inline-block;"></span>
                                Hoạt động
                            </span>
                        <?php else: ?>
                            <span style="display:flex;align-items:center;gap:5px;font-size:0.8rem;color:#dc2626;font-weight:600;">
                                <span style="width:7px;height:7px;background:#ef4444;border-radius:50%;display:inline-block;"></span>
                                Bị khóa
                            </span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:0.875rem 1.25rem;font-size:0.875rem;color:#64748b;"><?= htmlspecialchars($user['joined'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td style="padding:0.875rem 1.25rem;font-size:0.875rem;font-weight:600;color:#334155;"><?= $user['orders'] ?></td>
                    <td style="padding:0.875rem 1.25rem;">
                        <div style="display:flex;gap:0.5rem;">
                            <button type="button" title="Xem chi tiết" style="padding:6px;border:none;background:transparent;border-radius:6px;cursor:pointer;color:#64748b;" data-toast="Xem chi tiết người dùng: <?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                            <?php if ($user['status'] === 'Hoạt động'): ?>
                                <button type="button" title="Khóa tài khoản" style="padding:6px;border:none;background:transparent;border-radius:6px;cursor:pointer;color:#ef4444;" data-toast="Đã gửi yêu cầu khóa tài khoản: <?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </button>
                            <?php else: ?>
                                <button type="button" title="Mở khóa tài khoản" style="padding:6px;border:none;background:transparent;border-radius:6px;cursor:pointer;color:#16a34a;" data-toast="Đã gửi yêu cầu mở khóa tài khoản: <?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></svg>
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <!-- Footer -->
        <div style="padding:0.75rem 1.25rem;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:0.75rem;color:#64748b;">Hiển thị <?= count($users) ?> người dùng</span>
        </div>
    </div>
</div>

<script>
(function() {
    const searchInput = document.querySelector('[data-user-search]');
    const roleFilter  = document.getElementById('roleFilter');
    const statusFilter= document.getElementById('statusFilter');
    const rows        = document.querySelectorAll('.user-row');

    function filterRows() {
        const q    = (searchInput?.value || '').toLowerCase();
        const role = roleFilter?.value   || '';
        const stat = statusFilter?.value || '';
        rows.forEach(row => {
            const matchQ    = row.dataset.name.includes(q) || row.dataset.email.includes(q);
            const matchRole = !role || row.dataset.role   === role;
            const matchStat = !stat || row.dataset.status === stat;
            row.hidden = !(matchQ && matchRole && matchStat);
        });
    }
    searchInput?.addEventListener('input', filterRows);
    roleFilter?.addEventListener('change', filterRows);
    statusFilter?.addEventListener('change', filterRows);
})();
</script>
