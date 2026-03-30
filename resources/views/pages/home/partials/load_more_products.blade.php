@php
    $lastLoadedId = $lastLoadedId ?? null;
    $isExhausted = $isExhausted ?? false;
@endphp

@if ($products->isNotEmpty())
    @php
        $isSingleFeaturedCard = $products->count() === 1;
    @endphp
    @include('pages.category.partials.product_cards', [
        'products' => $products,
        'cardColumnClass' => $isSingleFeaturedCard
            ? 'col-xs-12 shopw-home-featured-card'
            : 'col-xs-12 col-sm-6 col-md-4',
        'showQuickView' => true,
        'showCartToggle' => true,
    ])

    <div id="load_more" class="col-sm-12 shopw-load-more-row">
        <button type="button" name="load_more_button" class="btn btn-primary form-control shopw-load-more-button"
            data-id="{{ $lastLoadedId }}" id="load_more_button">
            Xem thêm sản phẩm
        </button>
    </div>
@elseif ($isExhausted)
    <div id="load_more" class="col-sm-12 shopw-load-more-row">
        <div class="shopw-load-more-end">Bạn đã xem hết sản phẩm mới nhất hiện có.</div>
    </div>
@else
    <div class="col-sm-12">
        <p class="text-center text-muted">Hiện chưa có sản phẩm nào.</p>
    </div>
@endif

