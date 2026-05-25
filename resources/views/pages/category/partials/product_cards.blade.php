@php
    $emptyMessage = $emptyMessage ?? 'Hiện chưa có sản phẩm nào.';
    $emptyMessageClasses = $emptyMessageClasses ?? 'text-center text-muted';
    $emptyStateIcon = $emptyStateIcon ?? 'fa fa-cubes';
    $emptyStateActionText = $emptyStateActionText ?? 'Về trang chủ';
    $emptyStateActionUrl = $emptyStateActionUrl ?? url('/');
    $emptyStateClass = $emptyStateClass ?? 'shopw-product-empty';
    $cardColumnClass = $cardColumnClass ?? 'col-sm-3';
@endphp

@forelse ($products as $product)
    <div class="{{ $cardColumnClass }}">
        @include('partials.home.product-card', ['product' => $product])
    </div>
@empty
    <div class="col-sm-12">
        @include('partials.storefront.empty-state', [
            'title' => $emptyMessage,
            'message' => 'Hãy thử chuyển danh mục, thay đổi bộ lọc hoặc quay lại trang chủ để khám phá thêm sản phẩm.',
            'icon' => $emptyStateIcon,
            'compact' => true,
            'class' => trim($emptyStateClass . ' ' . $emptyMessageClasses),
            'actionText' => $emptyStateActionText,
            'actionUrl' => $emptyStateActionUrl,
        ])
    </div>
@endforelse
