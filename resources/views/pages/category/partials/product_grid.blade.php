@php
    /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $products */
    $showPagination = $showPagination ?? true;
    $emptyMessage = $emptyMessage ?? 'Hiện chưa có sản phẩm nào.';
    $emptyMessageClasses = $emptyMessageClasses ?? 'text-center text-muted';
@endphp

<div class="row">
@include('pages.category.partials.product_cards', [
        'products' => $products,
        'emptyMessage' => $emptyMessage,
        'emptyMessageClasses' => $emptyMessageClasses,
        'cardColumnClass' => $cardColumnClass ?? 'col-sm-3',
        'showQuickView' => $showQuickView ?? false,
        'showCartToggle' => $showCartToggle ?? false,
        'showWishlist' => $showWishlist ?? true,
        'showCompare' => $showCompare ?? true,
    ])
</div>

@if ($showPagination && $products instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
    <div class="col-sm-12">
        <div class="text-center">
            {{ $products->links('vendor.pagination.shop-pagination') }}
        </div>
    </div>
@endif
