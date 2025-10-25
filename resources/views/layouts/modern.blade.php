<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SEO Meta Tags -->
    <title>{{ $meta_title ?? 'Laravel Shop - Modern E-commerce' }}</title>
    <meta name="description" content="{{ $meta_desc ?? 'Modern e-commerce platform built with Laravel and Tailwind CSS' }}">
    <meta name="keywords" content="{{ $meta_keywords ?? 'ecommerce, shop, online store, laravel, tailwind' }}">
    <meta name="robots" content="INDEX,FOLLOW">
    <link rel="canonical" href="{{ $url_canonical ?? url()->current() }}">
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('frontend/images/logo-mail.png') }}" type="image/gif" sizes="32x32">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/tailwind.css'])
    
    <!-- Additional CSS -->
    <link href="{{ asset('frontend/css/dark-light-mode.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/dark-mode-components.css') }}" rel="stylesheet">
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#3b82f6">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Laravel Shop">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:url" content="{{ $url_canonical ?? url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $meta_title ?? 'Laravel Shop' }}">
    <meta property="og:description" content="{{ $meta_desc ?? 'Modern e-commerce platform' }}">
    <meta property="og:image" content="{{ $share_image ?? asset('frontend/images/logo-mail.png') }}">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $meta_title ?? 'Laravel Shop' }}">
    <meta name="twitter:description" content="{{ $meta_desc ?? 'Modern e-commerce platform' }}">
    <meta name="twitter:image" content="{{ $share_image ?? asset('frontend/images/logo-mail.png') }}">
    
    <!-- Preload Critical Resources -->
    <link rel="preload" href="{{ asset('frontend/fonts/Inter-Regular.woff2') }}" as="font" type="font/woff2" crossorigin>
    
    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => console.log('SW registered'))
                    .catch(error => console.log('SW registration failed'));
            });
        }
    </script>
</head>

<body class="h-full bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <!-- Theme Toggle -->
    @include('components.theme-toggle')
    
    <!-- Skip to main content for accessibility -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-primary-500 text-white px-4 py-2 rounded-lg z-50">
        Skip to main content
    </a>
    
    <!-- Loading Screen -->
    <div id="loading-screen" class="fixed inset-0 bg-white dark:bg-gray-900 z-50 flex items-center justify-center">
        <div class="text-center">
            <div class="w-16 h-16 border-4 border-primary-200 border-t-primary-500 rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-gray-600 dark:text-gray-400">Loading...</p>
        </div>
    </div>
    
    <!-- Main Layout -->
    <div class="min-h-full">
        <!-- Header -->
        <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-40">
            <!-- Top Bar -->
            <div class="bg-primary-600 text-white py-2 hidden sm:block">
                <div class="container-custom">
                    <div class="flex justify-between items-center text-sm">
                        <div class="flex items-center space-x-6">
                            <span>📞 Hotline: 1900-1234</span>
                            <span>✉️ Email: support@laravelshop.com</span>
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="#" class="hover:text-primary-200 transition-colors">Hỗ trợ</a>
                            <a href="#" class="hover:text-primary-200 transition-colors">Theo dõi đơn hàng</a>
                            <div class="flex items-center space-x-2">
                                <span>Tiếng Việt</span>
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Header -->
            <div class="container-custom py-4">
                <div class="flex items-center justify-between">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ url('/') }}" class="flex items-center space-x-3">
                            <img src="{{ asset('frontend/images/logo-mail.png') }}" alt="Laravel Shop" class="h-10 w-10">
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">Laravel Shop</span>
                        </a>
                    </div>
                    
                    <!-- Search Bar -->
                    <div class="flex-1 max-w-2xl mx-8 hidden lg:block">
                        <form class="relative">
                            <input type="text" 
                                   placeholder="Tìm kiếm sản phẩm..." 
                                   class="w-full pl-4 pr-12 py-3 border border-gray-300 dark:border-gray-600 rounded-full bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-primary-500 hover:bg-primary-600 text-white p-2 rounded-full transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                    
                    <!-- Right Side Actions -->
                    <div class="flex items-center space-x-4">
                        <!-- Wishlist -->
                        <button class="relative p-2 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <span class="absolute -top-1 -right-1 bg-danger-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                        </button>
                        
                        <!-- Cart -->
                        <button class="relative p-2 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                            </svg>
                            <span class="absolute -top-1 -right-1 bg-primary-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">5</span>
                        </button>
                        
                        <!-- User Account -->
                        <div class="relative">
                            <button class="flex items-center space-x-2 p-2 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="hidden sm:block">Tài khoản</span>
                            </button>
                        </div>
                        
                        <!-- Mobile Menu Button -->
                        <button id="mobile-menu-button" class="lg:hidden p-2 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Mobile Search -->
                <div class="mt-4 lg:hidden">
                    <form class="relative">
                        <input type="text" 
                               placeholder="Tìm kiếm sản phẩm..." 
                               class="w-full pl-4 pr-12 py-3 border border-gray-300 dark:border-gray-600 rounded-full bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-primary-500 hover:bg-primary-600 text-white p-2 rounded-full transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                <div class="container-custom">
                    <div class="flex items-center justify-between py-3">
                        <!-- Categories -->
                        <div class="flex items-center space-x-8">
                            <button class="flex items-center space-x-2 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                                <span>Danh mục</span>
                            </button>
                            
                            <div class="hidden md:flex items-center space-x-6">
                                <a href="#" class="nav-link">Trang chủ</a>
                                <a href="#" class="nav-link">Sản phẩm</a>
                                <a href="#" class="nav-link">Khuyến mãi</a>
                                <a href="#" class="nav-link">Tin tức</a>
                                <a href="#" class="nav-link">Liên hệ</a>
                            </div>
                        </div>
                        
                        <!-- Promotions -->
                        <div class="hidden lg:flex items-center space-x-4 text-sm">
                            <span class="bg-danger-100 text-danger-800 px-3 py-1 rounded-full">🔥 Sale 50%</span>
                            <span class="bg-success-100 text-success-800 px-3 py-1 rounded-full">🚚 Miễn phí ship</span>
                            <span class="bg-warning-100 text-warning-800 px-3 py-1 rounded-full">💳 Trả góp 0%</span>
                        </div>
                    </div>
                </div>
            </nav>
        </header>
        
        <!-- Main Content -->
        <main id="main-content" class="flex-1">
            @yield('content')
        </main>
        
        <!-- Footer -->
        <footer class="bg-gray-900 text-white">
            <div class="container-custom py-12">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Company Info -->
                    <div>
                        <div class="flex items-center space-x-3 mb-4">
                            <img src="{{ asset('frontend/images/logo-mail.png') }}" alt="Laravel Shop" class="h-8 w-8">
                            <span class="text-xl font-bold">Laravel Shop</span>
                        </div>
                        <p class="text-gray-400 mb-4">Nền tảng thương mại điện tử hiện đại, mang đến trải nghiệm mua sắm tuyệt vời.</p>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.746-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.001z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Quick Links -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Liên kết nhanh</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Về chúng tôi</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Tuyển dụng</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Tin tức</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Liên hệ</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Hỗ trợ</a></li>
                        </ul>
                    </div>
                    
                    <!-- Customer Service -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Hỗ trợ khách hàng</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Hướng dẫn mua hàng</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Chính sách đổi trả</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Bảo hành</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Giao hàng</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Thanh toán</a></li>
                        </ul>
                    </div>
                    
                    <!-- Contact Info -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Thông tin liên hệ</h3>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span class="text-gray-400">1900-1234</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-gray-400">support@laravelshop.com</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="text-gray-400">123 Đường ABC, Quận XYZ, TP.HCM</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Bottom Footer -->
                <div class="border-t border-gray-800 mt-8 pt-8">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <p class="text-gray-400 text-sm">© 2024 Laravel Shop. Tất cả quyền được bảo lưu.</p>
                        <div class="flex items-center space-x-6 mt-4 md:mt-0">
                            <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Điều khoản sử dụng</a>
                            <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Chính sách bảo mật</a>
                            <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Cookie Policy</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    
    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="bg-white dark:bg-gray-800 w-80 h-full shadow-xl">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">Menu</span>
                    <button id="mobile-menu-close" class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <nav class="space-y-4">
                    <a href="#" class="block py-2 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">Trang chủ</a>
                    <a href="#" class="block py-2 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">Sản phẩm</a>
                    <a href="#" class="block py-2 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">Khuyến mãi</a>
                    <a href="#" class="block py-2 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">Tin tức</a>
                    <a href="#" class="block py-2 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">Liên hệ</a>
                </nav>
            </div>
        </div>
    </div>
    
    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-6 right-6 bg-primary-500 hover:bg-primary-600 text-white p-3 rounded-full shadow-lg transition-all duration-300 opacity-0 invisible">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
    </button>
    
    <!-- Scripts -->
    @vite(['resources/js/app.js'])
    <script src="{{ asset('frontend/js/dark-light-mode.js') }}"></script>
    <script src="{{ asset('frontend/js/performance-optimizer.js') }}"></script>
    
    <!-- Modern JavaScript -->
    <script>
        // Loading Screen
        window.addEventListener('load', () => {
            const loadingScreen = document.getElementById('loading-screen');
            if (loadingScreen) {
                loadingScreen.style.opacity = '0';
                setTimeout(() => {
                    loadingScreen.style.display = 'none';
                }, 300);
            }
        });
        
        // Mobile Menu
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
        const mobileMenuClose = document.getElementById('mobile-menu-close');
        
        if (mobileMenuButton && mobileMenuOverlay) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenuOverlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });
            
            mobileMenuClose.addEventListener('click', () => {
                mobileMenuOverlay.classList.add('hidden');
                document.body.style.overflow = 'auto';
            });
            
            mobileMenuOverlay.addEventListener('click', (e) => {
                if (e.target === mobileMenuOverlay) {
                    mobileMenuOverlay.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
            });
        }
        
        // Back to Top Button
        const backToTopButton = document.getElementById('back-to-top');
        if (backToTopButton) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 300) {
                    backToTopButton.classList.remove('opacity-0', 'invisible');
                } else {
                    backToTopButton.classList.add('opacity-0', 'invisible');
                }
            });
            
            backToTopButton.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
        
        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);
        
        // Observe all elements with animate-on-scroll class
        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });
        
        // Lazy loading for images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    </script>
</body>
</html>
