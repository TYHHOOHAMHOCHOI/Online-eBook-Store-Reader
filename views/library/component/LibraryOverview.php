<?php
    // Tính toán cho vòng tròn SVG tiến độ mục tiêu
    $radius = 60;
    $percent = 0.65; // 65%
    $circumference = 2 * pi() * $radius; // Chu vi vòng tròn = 2 * PI * R
    $offset = $circumference * (1 - $percent); // Tính phần nét đứt bị ẩn đi
?>

<section class="px-12 py-12" style="background-color: #F5FBFA;">
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-4xl mb-3" style="font-weight: 700; color: #102A43;">Thư viện của bạn</h1>
            <p class="text-lg" style="color: #6B7280;">Chào mừng trở lại, hôm nay bạn muốn đọc gì?</p>
        </div>

        <!-- Reading Goal Widget -->
        <div class="bg-white rounded-2xl p-6 shadow-sm" style="width: 320px;">
            <h3 class="text-lg mb-4" style="font-weight: 600; color: #102A43;">Mục tiêu hôm nay</h3>
            <div class="flex items-center justify-center mb-4">
                <div class="relative" style="width: 140px; height: 140px;">
                    <!-- Circular progress -->
                    <svg class="transform -rotate-90" width="140" height="140">
                        <circle
                            cx="70"
                            cy="70"
                            r="60"
                            stroke="#E5E7EB"
                            stroke-width="12"
                            fill="none"
                        />
                        <circle
                            cx="70"
                            cy="70"
                            r="60"
                            stroke="#FFCA3A"
                            stroke-width="12"
                            fill="none"
                            stroke-dasharray="<?= $circumference ?>"
                            stroke-dashoffset="<?= $offset ?>"
                            stroke-linecap="round"
                        />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-3xl" style="font-weight: 700; color: #102A43;">65%</span>
                        <span class="text-sm" style="color: #6B7280;">13/20 phút</span>
                    </div>
                </div>
            </div>
            <p class="text-center text-sm" style="color: #6B7280;">
                Bạn đã đọc <span style="font-weight: 600; color: #087E8B;">13 phút</span> trong ngày hôm nay.
                <br />Còn <span style="font-weight: 600; color: #FFCA3A;">7 phút</span> nữa để đạt mục tiêu!
            </p>
        </div>
    </div>
</section>