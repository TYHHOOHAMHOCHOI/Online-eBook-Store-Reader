<section class="activities-section">
    <div style="max-width: 800px;">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: #102A43; margin: 0 0 1.5rem;">Hoạt động gần đây</h2>

        <div style="margin-bottom: 1.5rem;">
            <?php foreach ($recentActivities as $activity): ?>
            <div class="activity-card" style="border-left-color: <?= $activity['highlightColor'] ?>;">

                <!-- Icon -->
                <div style="padding: 8px; border-radius: 8px; background-color: <?= $activity['highlightColor'] ?>20; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 20px; height: 20px; color: <?= $activity['highlightColor'] ?>;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                </div>

                <!-- Nội dung -->
                <div style="flex: 1;">
                    <p class="activity-quote"><?= $activity['quote'] ?></p>
                    <div class="activity-meta">
                        <span style="font-weight: 600; color: #087E8B;"><?= $activity['book'] ?></span>
                        <span>•</span>
                        <span>Trang <?= $activity['page'] ?></span>
                    </div>
                </div>

                <!-- Bookmark icon -->
                <svg style="width: 20px; height: 20px; color: #9CA3AF; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                </svg>
            </div>
            <?php endforeach; ?>
        </div>

        <button class="btn-outline">Xem tất cả ghi chú</button>
    </div>
</section>