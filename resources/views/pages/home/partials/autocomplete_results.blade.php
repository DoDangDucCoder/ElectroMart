@if ($products->isNotEmpty())
    <ul class="dropdown-menu shopw-search-suggest-list" style="display:block; position:absolute">
        @foreach ($products as $item)
            <li class="li_search_ajax">
                <a href="{{ url('/chi-tiet/' . $item->product_slug) }}">
                    <span class="shopw-search-suggest-item">
                        <strong>{{ $item->product_name }}</strong>
                        <small>
                            @if ($item->sku)
                                {{ $item->sku }} |
                            @endif
                            {{ number_format($item->final_price ?? $item->product_price, 0, ',', '.') }} VND
                        </small>
                    </span>
                </a>
            </li>
        @endforeach
    </ul>
@endif

