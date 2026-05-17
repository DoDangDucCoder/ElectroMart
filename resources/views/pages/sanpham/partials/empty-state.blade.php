@include('partials.storefront.empty-state', [
    'title' => $title ?? 'Không tìm thấy sản phẩm phù hợp',
    'message' => $message ?? 'Thử đổi từ khóa, danh mục, thương hiệu hoặc khoảng giá để xem thêm sản phẩm.',
    'icon' => $icon ?? 'fa fa-search',
    'compact' => true,
    'class' => $class ?? 'shopw-product-empty',
    'actionText' => $actionText ?? 'Xóa bộ lọc',
    'actionUrl' => $actionUrl ?? ($clearUrl ?? url('/san-pham')),
])
