<div class="tab-content">
    <div class="tab-pane fade active in" id="tshirt">
        @include('pages.category.partials.product_grid', [
            'products' => $products,
            'emptyMessage' => 'Hiện chưa có sản phẩm trong danh mục này',
            'emptyMessageClasses' => 'text-center text-danger',
            'showPagination' => false,
        ])
    </div>
</div>
