<section class="px-12 py-10" style="background-color: #F5FBFA;">
    <div class="max-w-5xl">
        <h2 class="text-2xl mb-6" style="font-weight: 700; color: #102A43;">Hoạt động gần đây</h2>

        <div class="space-y-4 mb-6">
            <!-- Thay thế hàm map() của React bằng vòng lặp foreach của PHP -->
            <?php foreach ($recentActivities as $activity) : ?>
                <div class="bg-white rounded-lg p-5 shadow-sm border-l-4" style="border-left-color: <?= $activity['highlightColor'] ?>;">
                    <div class="flex items-start gap-4">
                        
                        <!-- Biểu tượng Highlighter -->
                        <div class="p-2 rounded-lg" style="background-color: <?= $activity['highlightColor'] ?>20;">
                            <svg class="w-5 h-5" style="color: <?= $activity['highlightColor'] ?>;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                        </div>
                        
                        <div class="flex-1">
                            <p class="text-base mb-2 italic" style="color: #102A43;">
                                <?= $activity['quote'] ?>
                            </p>
                            <div class="flex items-center gap-3 text-sm" style="color: #6B7280;">
                                <span style="font-weight: 600; color: #087E8B;">
                                    <?= $activity['book'] ?>
                                </span>
                                <span>•</span>
                                <span>Trang <?= $activity['page'] ?></span>
                            </div>
                        </div>
                        
                        <!-- Biểu tượng Bookmark -->
                        <svg class="w-5 h-5" style="color: #9CA3AF;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                        </svg>
                        
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <button class="px-6 py-3 rounded-lg border-2 text-base" style="border-color: #087E8B; color: #087E8B; font-weight: 600;">
            Xem tất cả ghi chú
        </button>
    </div>
</section>