<section class="px-12 py-8 bg-white">
    <div class="flex justify-between items-center mb-8">
        
        <!-- Các nút Tabs phân loại trạng thái đọc -->
        <div class="flex gap-6">
            <button class="px-5 py-2 rounded-lg text-base" style="background-color: #087E8B; color: white; font-weight: 600;">
                Đang đọc
            </button>
            <button class="px-5 py-2 rounded-lg text-base border" style="border-color: #D1D5DB; color: #6B7280;">
                Chưa đọc
            </button>
            <button class="px-5 py-2 rounded-lg text-base border" style="border-color: #D1D5DB; color: #6B7280;">
                Đã hoàn thành
            </button>
        </div>

        <!-- Công cụ sắp xếp và lọc -->
        <div class="flex gap-3 items-center">
            <span class="text-sm" style="color: #6B7280;">Sắp xếp theo:</span>
            <select class="px-4 py-2 rounded-lg border text-sm" style="border-color: #087E8B; color: #087E8B;">
                <option>Tên sách</option>
                <option>Ngày mua</option>
                <option>Tiến độ</option>
            </select>
            <button class="px-4 py-2 rounded-lg border text-sm" style="border-color: #087E8B; color: #087E8B;">
                Lọc theo trạng thái
            </button>
        </div>
    </div>

    <!-- Lưới hiển thị danh sách các cuốn sách -->
    <div class="grid grid-cols-4 gap-6 mb-12">
        <!-- Thay thế hàm map() của React bằng vòng lặp foreach của PHP -->
        <?php foreach ($books as $book) : ?>
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden border border-gray-100">
                
                <!-- Ảnh bìa sách động -->
                <div class="relative" style="height: 280px; background-color: <?= $book['color'] ?>; padding: 24px;">
                    <div class="flex flex-col h-full justify-between">
                        <div>
                            <div class="w-12 h-1 mb-4" style="background-color: rgba(255,255,255,0.5);"></div>
                            <h3 class="text-xl text-white mb-2" style="font-weight: 700; line-height: 1.3;"><?= $book['title'] ?></h3>
                        </div>
                        <div>
                            <p class="text-sm text-white opacity-90"><?= $book['author'] ?></p>
                        </div>
                    </div>
                </div>

                <!-- Thông tin tiến độ và nút đọc sách -->
                <div class="p-4">
                    <div class="mb-3">
                        <div class="flex justify-between text-xs mb-1" style="color: #6B7280;">
                            <span>Tiến độ đọc</span>
                            <span style="font-weight: 600; color: #087E8B;"><?= $book['progress'] ?>%</span>
                        </div>
                        
                        <!-- Thanh Progress Bar -->
                        <div class="w-full h-2 rounded-full" style="background-color: #E5E7EB;">
                            <div class="h-2 rounded-full" style="width: <?= $book['progress'] ?>%; background-color: #087E8B;"></div>
                        </div>
                    </div>
                    
                    <!-- Nút Đọc tiếp (Thay đổi onClick thành href) -->
                    <a href="?read=<?= $book['id'] ?>" class="w-full py-2 rounded-lg text-sm flex items-center justify-center gap-1 transition-colors" style="background-color: #D9F6F0; color: #087E8B; font-weight: 600; text-decoration: none;">
                        Đọc tiếp
                        <span>&#10095;</span>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>