<div class="tab-content">
    <div class="tab-pane fade active in" id="tshirt">
        @php
            $isSingleFeaturedCard = $products->count() === 1;
            $tabGridClass = $isSingleFeaturedCard
                ? 'shopw-home-tab-products-single'
                : ($products->count() === 2 ? 'shopw-home-tab-products-duo' : 'shopw-home-tab-products-grid');
        @endphp
        <div class="row {{ $tabGridClass }}">
        @include('pages.category.partials.product_cards', [
            'products' => $products,
            'emptyMessage' => 'Hiện chưa có sản phẩm trong danh mục này.',
            'emptyStateIcon' => 'fa fa-th-large',
            'emptyStateActionText' => 'Xem danh mục khác',
            'emptyStateActionUrl' => url('/'),
            'cardColumnClass' => $isSingleFeaturedCard
                ? 'col-xs-12 shopw-home-featured-card'
                : 'col-xs-12 col-sm-6 col-md-3',
            'showQuickView' => true,
            'showCartToggle' => true,
        ])
        </div>
    </div>
</div>

