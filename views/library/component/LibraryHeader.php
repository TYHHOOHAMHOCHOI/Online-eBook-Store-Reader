<header class="bg-white border-b border-gray-200" style="height: 80px;">
    <div class="px-12 h-full flex items-center justify-between">
        
        <!-- Logo -->
        <div class="flex items-center gap-2">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white" style="background-color: #087E8B;">
                <span class="text-xl" style="font-weight: 700;">R</span>
            </div>
            <span class="text-2xl" style="font-weight: 600; color: #102A43;">readly</span>
        </div>

        <!-- Navigation -->
        <nav class="flex items-center gap-8">
            <a href="#" class="text-base" style="color: #6B7280;">Khám phá</a>
            <a href="#" class="text-base relative" style="color: #087E8B; font-weight: 600;">
                Thư viện
                <div class="absolute bottom-[-20px] left-0 right-0 h-0.5" style="background-color: #087E8B;"></div>
            </a>
            <a href="#" class="text-base" style="color: #6B7280;">Kích hoạt</a>
        </nav>

        <!-- Search & User actions -->
        <div class="flex items-center gap-4">
            <div class="relative">
                <!-- Biểu tượng Search (SVG thay cho <Search />) -->
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5" style="color: #9CA3AF;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input
                    type="text"
                    placeholder="Tìm trong thư viện của bạn..."
                    class="pl-10 pr-4 py-2 rounded-lg border border-gray-200 w-80"
                    style="color: #102A43;"
                />
            </div>
            
            <button class="p-2 rounded-lg hover:bg-gray-50">
                <!-- Biểu tượng Bell (SVG thay cho <Bell />) -->
                <svg class="w-5 h-5" style="color: #6B7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
            </button>
            
            <button class="w-10 h-10 rounded-full flex items-center justify-center" style="background-color: #D9F6F0;">
                <!-- Biểu tượng User (SVG thay cho <User />) -->
                <svg class="w-5 h-5" style="color: #087E8B;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </button>
        </div>
        
    </div>
</header>