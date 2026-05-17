<div class="cat-x__grid" data-cat-grid>
    @forelse ($products as $product)
        @include('pages.category.partials.product_card_x', ['product' => $product])
    @empty
        <div style="grid-column: 1 / -1;">
            @include('pages.category.partials.empty-state')
        </div>
    @endforelse
</div>
