<section class="max-w-[1280px] mx-auto px-8 py-10">
                <div class="bg-[#075A64] rounded-2xl overflow-hidden">
                    <div class="grid grid-cols-2 gap-8 px-12 py-14">
                        <div class="flex flex-col justify-center gap-4">
                            <p class="text-[#D9F6F0] text-xs tracking-wide uppercase">
                                Sách hay không phải để lướt qua.
                            </p>
                            <h1 class="text-4xl text-white leading-tight">
                                Khám phá cuốn sách đúng lúc bạn cần
                            </h1>
                            <p class="text-[#D9F6F0] leading-relaxed">
                                Từ những câu chuyện chữa lành đến kiến thức giúp bạn tiến xa hơn — tất cả trong một thư viện đọc thật dễ chịu.
                            </p>
                            <div class="flex flex-col gap-2 mt-2">
                                <button class="px-6 py-3 bg-[#FFCA3A] text-[#102A43] rounded-lg hover:bg-[#FFD865] flex items-center gap-2 w-fit">
                                    <span class="font-semibold">Bắt đầu đọc</span>
                                    <span>→</span>
                                </button>
                                <p class="text-[#D9F6F0] text-sm">Hơn 20.000 tựa sách đang chờ bạn</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-center relative">
                            <div class="w-64 h-80 bg-white rounded-2xl shadow-2xl transform rotate-3 relative">
                                <div class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center">
                                    <div class="text-[#102A43] text-3xl font-bold mb-3 leading-tight">
                                        ĐỌC — MỞ RA MỘT THẾ GIỚI MỚI
                                    </div>
                                    <div class="w-20 h-1 bg-[#FFCA3A] rounded-full"></div>
                                </div>
                                <div class="absolute -top-3 -right-3 bg-[#FF5A5F] text-white px-3 py-1 rounded-full shadow-lg">
                                    <span class="text-xs font-semibold">32K+ độc giả</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Sách được yêu thích */}
            <section class="max-w-[1280px] mx-auto px-8 py-10">
                <div class="flex items-end justify-between mb-8">
                    <div>
                        <h2 class="text-2xl text-[#102A43] mb-2">Sách được yêu thích</h2>
                        <p class="text-gray-600 text-sm">
                            Những lựa chọn được cộng đồng Readly yêu mến nhất tuần này.
                        </p>
                    </div>
                    <button
                       
                        class="px-5 py-2 text-sm border-2 border-[#087E8B] text-[#087E8B] rounded-lg hover:bg-[#087E8B] hover:text-white flex items-center gap-2"
                    >
                        Xem danh sách
                        <span>→</span>
                    </button>
                </div>

                <div class="grid grid-cols-4 gap-5">
                    <?php include 'BookCards.php'; ?>
                    
                </div>
            </section>

            {/* Best Seller Section */}
            <section class="max-w-[1280px] mx-auto px-8 py-10 bg-white">
                <div class="mb-6">
                    <h2 class="text-2xl text-[#102A43] mb-2">Best Seller</h2>
                    <p class="text-gray-600 text-sm mb-6">Những cuốn sách bán chạy nhất trong tháng</p>

                    <div class="flex gap-3 border-b border-gray-200 pb-3">
                        <button class="px-3 py-2 text-sm text-[#087E8B] border-b-2 border-[#087E8B] font-semibold">
                            Văn học
                        </button>
                        <button class="px-3 py-2 text-sm text-gray-600 hover:text-[#087E8B]">Kinh tế</button>
                        <button class="px-3 py-2 text-sm text-gray-600 hover:text-[#087E8B]">Tâm lý</button>
                        <button class="px-3 py-2 text-sm text-gray-600 hover:text-[#087E8B]">Kỹ năng sống</button>
                        <button class="px-3 py-2 text-sm text-gray-600 hover:text-[#087E8B]">Thiếu nhi</button>
                        <button class="px-3 py-2 text-sm text-gray-600 hover:text-[#087E8B]">Ngoại ngữ</button>
                        <button class="px-3 py-2 text-sm text-gray-600 hover:text-[#087E8B]">Khoa học</button>
                    </div>
                </div>

                <div class="grid grid-cols-5 gap-4 mb-6">
                    
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex gap-3">
                        <button class="w-8 h-8 flex items-center justify-center text-sm border border-gray-300 rounded-lg hover:border-[#087E8B] hover:text-[#087E8B]">
                            01
                        </button>
                        <button class="w-8 h-8 flex items-center justify-center text-sm border border-gray-300 rounded-lg hover:border-[#087E8B] hover:text-[#087E8B]">
                            02
                        </button>
                        <button class="w-8 h-8 flex items-center justify-center text-sm border border-gray-300 rounded-lg hover:border-[#087E8B] hover:text-[#087E8B]">
                            03
                        </button>
                    </div>
                    <button
                        
                        class="text-[#087E8B] text-sm flex items-center gap-2 hover:gap-3 transition-all"
                    >
                        Xem tất cả
                        <span>→</span>
                    </button>
                </div>
            </section>

            {/* Gợi ý cho bạn */}
            <section class="bg-[#F5FBFA] py-10">
                <div class="max-w-[1280px] mx-auto px-8">
                    <div class="border-t-4 border-[#087E8B] bg-[#D9F6F0] rounded-t-2xl px-6 py-4 mb-6 flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl text-[#102A43] mb-1">Gợi ý cho bạn</h2>
                            <p class="text-gray-600 text-sm">Chọn lọc từ những điều bạn đã lưu và hay đọc.</p>
                        </div>
                        <div class="bg-[#087E8B] text-white px-5 py-3 rounded-lg">
                            <div class="text-sm">Dành riêng cho bạn</div>
                            <div class="text-xs opacity-80">Cập nhật hôm nay</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-4 mb-6">
                        <?php include 'BookCards.php'; ?>
                        
                    </div>

                    <div class="flex justify-center">
                        <button
                            
                            class="px-6 py-2 text-sm border-2 border-[#087E8B] text-[#087E8B] rounded-lg hover:bg-[#087E8B] hover:text-white flex items-center gap-2"
                        >
                            Xem tất cả
                            <span>→</span>
                        </button>
                    </div>
                </div>
            </section>