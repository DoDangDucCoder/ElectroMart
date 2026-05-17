@if ($products instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $products->lastPage() > 1)
    <div class="cat-x__pagination">
        {{ $products->onEachSide(1)->links('vendor.pagination.shop-pagination') }}
    </div>
@endif
