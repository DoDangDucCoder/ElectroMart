<!-- Component thẻ sản phẩm hiện đại -->
@props(['product', 'showRating' => true, 'showSold' => true])

@php
$product = $product ?? (object) [
    'id' => 1,
    'name' => 'iPhone 15 Pro Max',
    'price' => 25000000,
    'old_price' => 30000000,
    'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
    'rating' => 4.8,
    'sold' => 1234,
    'is_featured' => true,
    'discount' => 20
];
@endphp

<div class="product-card group cursor-pointer">
    <div class="relative overflow-hidden">
        <img src="{{ $product->image }}" 
             alt="{{ $product->name }}" 
             class="product-image"
             loading="lazy">
        
        <!-- Nhãn trạng thái -->
        <div class="absolute top-4 left-4">
            @if($product->is_featured ?? false)
            <span class="badge badge-danger">Nổi bật</span>
            @endif
            @if(($product->discount ?? 0) > 0)
            <span class="badge badge-success">-{{ $product->discount }}%</span>
            @endif
        </div>
        
        <!-- Thao tác nhanh -->
        <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <button class="w-10 h-10 bg-white dark:bg-gray-800 rounded-full shadow-lg flex items-center justify-center hover:bg-primary-500 hover:text-white transition-colors mb-2"
                    onclick="addToWishlist({{ $product->id }})">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
            </button>
            <button class="w-10 h-10 bg-white dark:bg-gray-800 rounded-full shadow-lg flex items-center justify-center hover:bg-primary-500 hover:text-white transition-colors"
                    onclick="quickView({{ $product->id }})">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            </button>
        </div>
    </div>
    
    <div class="p-6">
        <h3 class="product-title">{{ $product->name }}</h3>
        
        @if($showRating)
        <!-- Đánh giá -->
        <div class="flex items-center mb-3">
            <div class="flex text-yellow-400">
                @for($i = 0; $i < 5; $i++)
                <svg class="w-4 h-4 {{ $i < floor($product->rating ?? 4.8) ? 'fill-current' : 'text-gray-300 dark:text-gray-600' }}" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                @endfor
            </div>
            <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">({{ $product->rating ?? 4.8 }})</span>
        </div>
        @endif
        
        <!-- Giá -->
        <div class="flex items-center justify-between mb-4">
            <div>
                <div class="product-price">{{ number_format($product->price, 0, ',', '.') }}đ</div>
                @if(($product->old_price ?? 0) > 0)
                <div class="product-price-old">{{ number_format($product->old_price, 0, ',', '.') }}đ</div>
                @endif
            </div>
            @if($showSold)
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Đã bán: {{ $product->sold ?? 1234 }}
            </div>
            @endif
        </div>
        
        <!-- Nút thêm vào giỏ hàng -->
        <button class="btn btn-primary w-full" onclick="addToCart({{ $product->id }})">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
            </svg>
            Thêm vào giỏ
        </button>
    </div>
</div>

<script>
function addToCart(productId) {
    // Chức năng thêm vào giỏ hàng
    console.log('Đang thêm sản phẩm vào giỏ hàng:', productId);
    // Cài đặt logic giỏ hàng thực tế tại đây
}

function addToWishlist(productId) {
    // Chức năng thêm vào danh sách yêu thích
    console.log('Đang thêm sản phẩm vào danh sách yêu thích:', productId);
    // Cài đặt logic danh sách yêu thích thực tế tại đây
}

function quickView(productId) {
    // Chức năng xem nhanh
    console.log('Xem nhanh sản phẩm:', productId);
    // Cài đặt logic xem nhanh thực tế tại đây
}
</script>
