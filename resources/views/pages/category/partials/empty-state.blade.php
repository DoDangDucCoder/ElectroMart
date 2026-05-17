@include('partials.storefront.empty-state', [
    'title' => $title ?? 'Hiện chưa có sản phẩm nào trong danh mục này.',
    'message' => $message ?? 'Hãy thử chuyển danh mục, thay đổi bộ lọc hoặc quay lại trang chủ.',
    'icon' => $icon ?? 'fa fa-cubes',
    'compact' => $compact ?? true,
])
