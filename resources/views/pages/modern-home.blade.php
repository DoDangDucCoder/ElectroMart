@extends('layouts.modern')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-r from-primary-600 to-primary-800 text-white overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-20"></div>
    <div class="relative container-custom py-20 lg:py-32">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="animate-fade-in">
                <h1 class="text-4xl lg:text-6xl font-bold mb-6 leading-tight">
                    Khám phá thế giới 
                    <span class="text-yellow-300">mua sắm</span>
                    hiện đại
                </h1>
                <p class="text-xl lg:text-2xl mb-8 text-gray-200">
                    Hàng triệu sản phẩm chất lượng cao với giá tốt nhất thị trường
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <button class="btn btn-lg bg-white text-primary-600 hover:bg-gray-100 px-8 py-4 rounded-full font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                        🛒 Mua sắm ngay
                    </button>
                    <button class="btn btn-lg border-2 border-white text-white hover:bg-white hover:text-primary-600 px-8 py-4 rounded-full font-semibold transition-all duration-300">
                        📱 Tải app
                    </button>
                </div>
            </div>
            <div class="animate-slide-up">
                <div class="relative">
                    <img src="{{ asset('frontend/images/hero-image.jpg') }}" 
                         alt="Modern Shopping" 
                         class="w-full h-96 lg:h-[500px] object-cover rounded-2xl shadow-2xl"
                         onerror="this.src='https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80'">
                    <div class="absolute -bottom-6 -right-6 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-xl">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-primary-600">1M+</div>
                            <div class="text-gray-600 dark:text-gray-400">Sản phẩm</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-16 bg-white dark:bg-gray-800">
    <div class="container-custom">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center animate-on-scroll">
                <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Miễn phí vận chuyển</h3>
                <p class="text-gray-600 dark:text-gray-400">Đơn hàng từ 500k</p>
            </div>
            
            <div class="text-center animate-on-scroll">
                <div class="w-16 h-16 bg-success-100 dark:bg-success-900 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Bảo hành chính hãng</h3>
                <p class="text-gray-600 dark:text-gray-400">12-24 tháng</p>
            </div>
            
            <div class="text-center animate-on-scroll">
                <div class="w-16 h-16 bg-warning-100 dark:bg-warning-900 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Trả góp 0%</h3>
                <p class="text-gray-600 dark:text-gray-400">Lãi suất 0%</p>
            </div>
            
            <div class="text-center animate-on-scroll">
                <div class="w-16 h-16 bg-danger-100 dark:bg-danger-900 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-danger-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Hỗ trợ 24/7</h3>
                <p class="text-gray-600 dark:text-gray-400">Luôn sẵn sàng</p>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-16 bg-gray-50 dark:bg-gray-900">
    <div class="container-custom">
        <div class="text-center mb-12">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Danh mục sản phẩm
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-400">
                Khám phá các danh mục sản phẩm đa dạng
            </p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            @foreach($categories ?? [] as $category)
            <a href="#" class="group animate-on-scroll">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 text-center hover:shadow-lg transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <span class="text-2xl">{{ $category->icon ?? '📱' }}</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                        {{ $category->name ?? 'Điện thoại' }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ $category->product_count ?? '1,234' }} sản phẩm
                    </p>
                </div>
            </a>
            @endforeach
            
            <!-- Default categories if no data -->
            @if(empty($categories))
            <a href="#" class="group animate-on-scroll">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 text-center hover:shadow-lg transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <span class="text-2xl">📱</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                        Điện thoại
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">1,234 sản phẩm</p>
                </div>
            </a>
            
            <a href="#" class="group animate-on-scroll">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 text-center hover:shadow-lg transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-success-100 dark:bg-success-900 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <span class="text-2xl">💻</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                        Laptop
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">856 sản phẩm</p>
                </div>
            </a>
            
            <a href="#" class="group animate-on-scroll">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 text-center hover:shadow-lg transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-warning-100 dark:bg-warning-900 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <span class="text-2xl">🎧</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                        Phụ kiện
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">2,156 sản phẩm</p>
                </div>
            </a>
            
            <a href="#" class="group animate-on-scroll">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 text-center hover:shadow-lg transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-danger-100 dark:bg-danger-900 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <span class="text-2xl">📺</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                        TV & Audio
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">432 sản phẩm</p>
                </div>
            </a>
            
            <a href="#" class="group animate-on-scroll">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 text-center hover:shadow-lg transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <span class="text-2xl">🏠</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                        Gia dụng
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">678 sản phẩm</p>
                </div>
            </a>
            
            <a href="#" class="group animate-on-scroll">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 text-center hover:shadow-lg transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <span class="text-2xl">🎮</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                        Gaming
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">345 sản phẩm</p>
                </div>
            </a>
            @endif
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-16 bg-white dark:bg-gray-800">
    <div class="container-custom">
        <div class="flex items-center justify-between mb-12">
            <div>
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Sản phẩm nổi bật
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-400">
                    Những sản phẩm được yêu thích nhất
                </p>
            </div>
            <button class="btn btn-outline hidden sm:flex">
                Xem tất cả
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
        
        <div class="grid-responsive">
            @foreach($featured_products ?? [] as $product)
            <div class="product-card animate-on-scroll">
                <div class="relative overflow-hidden">
                    <img src="{{ $product->image ?? 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80' }}" 
                         alt="{{ $product->name ?? 'Product' }}" 
                         class="product-image">
                    
                    <!-- Badges -->
                    <div class="absolute top-4 left-4">
                        @if($product->is_featured ?? true)
                        <span class="badge badge-danger">Hot</span>
                        @endif
                        @if($product->discount ?? 0 > 0)
                        <span class="badge badge-success">-{{ $product->discount ?? 20 }}%</span>
                        @endif
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <button class="w-10 h-10 bg-white dark:bg-gray-800 rounded-full shadow-lg flex items-center justify-center hover:bg-primary-500 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    <h3 class="product-title">{{ $product->name ?? 'iPhone 15 Pro Max' }}</h3>
                    
                    <!-- Rating -->
                    <div class="flex items-center mb-3">
                        <div class="flex text-yellow-400">
                            @for($i = 0; $i < 5; $i++)
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            @endfor
                        </div>
                        <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">({{ $product->rating ?? 4.8 }})</span>
                    </div>
                    
                    <!-- Price -->
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <div class="product-price">{{ number_format($product->price ?? 25000000, 0, ',', '.') }} VND</div>
                            @if($product->old_price ?? 0 > 0)
                            <div class="product-price-old">{{ number_format($product->old_price ?? 30000000, 0, ',', '.') }} VND</div>
                            @endif
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Đã bán: {{ $product->sold ?? 1234 }}
                        </div>
                    </div>
                    
                    <!-- Add to Cart Button -->
                    <button class="btn btn-primary w-full">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                        </svg>
                        Thêm vào giỏ
                    </button>
                </div>
            </div>
            @endforeach
            
            <!-- Default products if no data -->
            @if(empty($featured_products))
            @for($i = 0; $i < 8; $i++)
            <div class="product-card animate-on-scroll">
                <div class="relative overflow-hidden">
                    <img src="https://images.unsplash.com/photo-{{ 1523275335684 + $i }}?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" 
                         alt="Product {{ $i + 1 }}" 
                         class="product-image">
                    
                    <div class="absolute top-4 left-4">
                        <span class="badge badge-danger">Hot</span>
                    </div>
                    
                    <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <button class="w-10 h-10 bg-white dark:bg-gray-800 rounded-full shadow-lg flex items-center justify-center hover:bg-primary-500 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    <h3 class="product-title">iPhone 15 Pro Max {{ $i + 1 }}</h3>
                    
                    <div class="flex items-center mb-3">
                        <div class="flex text-yellow-400">
                            @for($j = 0; $j < 5; $j++)
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            @endfor
                        </div>
                        <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">(4.8)</span>
                    </div>
                    
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <div class="product-price">{{ number_format(25000000 + ($i * 1000000), 0, ',', '.') }} VND</div>
                            <div class="product-price-old">{{ number_format(30000000 + ($i * 1000000), 0, ',', '.') }} VND</div>
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Đã bán: {{ 1234 + ($i * 100) }}
                        </div>
                    </div>
                    
                    <button class="btn btn-primary w-full">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                        </svg>
                        Thêm vào giỏ
                    </button>
                </div>
            </div>
            @endfor
            @endif
        </div>
        
        <div class="text-center mt-12">
            <button class="btn btn-primary btn-lg">
                Xem tất cả sản phẩm
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="py-16 bg-primary-600 text-white">
    <div class="container-custom">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-3xl lg:text-4xl font-bold mb-4">
                Đăng ký nhận tin tức
            </h2>
            <p class="text-xl mb-8 text-primary-100">
                Nhận thông tin về sản phẩm mới và khuyến mãi độc quyền
            </p>
            
            <form class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                <input type="email" 
                       placeholder="Nhập email của bạn" 
                       class="flex-1 px-4 py-3 rounded-full text-gray-900 focus:outline-none focus:ring-2 focus:ring-white">
                <button type="submit" 
                        class="btn bg-white text-primary-600 hover:bg-gray-100 px-8 py-3 rounded-full font-semibold">
                    Đăng ký
                </button>
            </form>
            
            <p class="text-sm text-primary-200 mt-4">
                Chúng tôi cam kết không spam. Hủy đăng ký bất cứ lúc nào.
            </p>
        </div>
    </div>
</section>

<!-- Brands Section -->
<section class="py-16 bg-gray-50 dark:bg-gray-900">
    <div class="container-custom">
        <div class="text-center mb-12">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Thương hiệu đối tác
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-400">
                Những thương hiệu uy tín hàng đầu thế giới
            </p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8">
            @foreach(['Apple', 'Samsung', 'Sony', 'LG', 'Xiaomi', 'Huawei'] as $brand)
            <div class="text-center animate-on-scroll">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ substr($brand, 0, 1) }}</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $brand }}</h3>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
