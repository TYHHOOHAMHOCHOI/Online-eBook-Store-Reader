<?php
/**
 * Admin Bottom Section Component (Pending Books Review & Recent Transactions)
 */
declare(strict_types=1);

if (!isset($pendingBooks) || !is_array($pendingBooks)) {
    $pendingBooks = [];
}
if (!isset($recentTransactions) || !is_array($recentTransactions)) {
    $recentTransactions = [];
}
?>
<div class="bottom-grid">
    <!-- Book Review Section -->
    <div class="bottom-panel">
        <div class="panel-header">
            <h2 class="panel-title">Sách chờ kiểm duyệt</h2>
            <span class="badge-count"><?= count($pendingBooks) ?> sách</span>
        </div>
        <div class="pending-books-list">
            <?php foreach ($pendingBooks as $book): ?>
                <div class="pending-book-card" data-book-id="<?= (int)($book['id'] ?? 0) ?>" data-book-title="<?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>">
                    <div class="pending-book-info">
                        <div>
                            <h3 class="book-title"><?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <p class="book-meta">
                                <?= htmlspecialchars($book['publisher'], ENT_QUOTES, 'UTF-8') ?> • <?= htmlspecialchars($book['date'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                        <span class="status-badge-yellow"><?= htmlspecialchars($book['status'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="action-buttons-group">
                        <button type="button" class="btn-action btn-approve" data-book-id="<?= (int)($book['id'] ?? 0) ?>" data-book-title="<?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>" data-toast="Đã duyệt thành công cuốn sách: <?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            Duyệt
                        </button>
                        <button type="button" class="btn-action btn-reject" data-book-id="<?= (int)($book['id'] ?? 0) ?>" data-book-title="<?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>" data-toast="Đã từ chối duyệt sách: <?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            Từ chối
                        </button>
                        <button type="button" class="btn-action btn-request-edit" data-book-id="<?= (int)($book['id'] ?? 0) ?>" data-book-title="<?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>" data-toast="Đã gửi yêu cầu chỉnh sửa thông tin sách: <?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Yêu cầu sửa
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Recent Transactions Section -->
    <div class="bottom-panel">
        <h2 class="panel-title" style="margin-bottom: 1rem;">Giao dịch mới nhất</h2>
        <div class="transactions-list">
            <?php foreach ($recentTransactions as $tx): ?>
                <div class="transaction-item">
                    <div>
                        <div class="transaction-id">Mã: <?= htmlspecialchars($tx['id'], ENT_QUOTES, 'UTF-8') ?></div>
                        <div class="transaction-buyer"><?= htmlspecialchars($tx['buyer'], ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                    <div>
                        <div class="transaction-amount"><?= number_format($tx['amount'], 0, ',', '.') ?>đ</div>
                        <span class="<?= $tx['status'] === 'Thành công' ? 'status-badge-green' : 'status-badge-red' ?>">
                            <?= htmlspecialchars($tx['status'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="btn-view-all" data-toast="Mở toàn bộ danh sách lịch sử giao dịch & đối soát.">
            Xem tất cả giao dịch →
        </button>
    </div>
</div>
