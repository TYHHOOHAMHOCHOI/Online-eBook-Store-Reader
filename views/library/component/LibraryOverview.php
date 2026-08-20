<?php
    $radius        = 60;
    $percent       = 0.65;
    $circumference = 2 * pi() * $radius;
    $offset        = $circumference * (1 - $percent);
?>

<section class="overview-section">
    <div>
        <h1 class="overview-title">Thư viện của bạn</h1>
        <p class="overview-subtitle">Chào mừng trở lại, hôm nay bạn muốn đọc gì?</p>
    </div>

    <!-- Reading Goal Widget -->
    <div class="goal-widget">
        <h3 class="goal-title">Mục tiêu hôm nay</h3>

        <div class="progress-circle">
            <svg style="transform: rotate(-90deg);" width="140" height="140">
                <circle cx="70" cy="70" r="60" stroke="#E5E7EB" stroke-width="12" fill="none"/>
                <circle cx="70" cy="70" r="60" stroke="#FFCA3A" stroke-width="12" fill="none"
                    stroke-dasharray="<?= $circumference ?>"
                    stroke-dashoffset="<?= $offset ?>"
                    stroke-linecap="round"/>
            </svg>
            <div class="progress-text">
                <span style="font-size: 1.75rem; font-weight: 700; color: #102A43;">65%</span>
                <span style="font-size: 0.875rem; color: #6B7280;">13/20 phút</span>
            </div>
        </div>

        <p style="text-align: center; font-size: 0.875rem; color: #6B7280; margin: 0;">
            Bạn đã đọc <span style="font-weight: 600; color: #087E8B;">13 phút</span> trong ngày hôm nay.<br>
            Còn <span style="font-weight: 600; color: #FFCA3A;">7 phút</span> nữa để đạt mục tiêu!
        </p>
    </div>
</section>