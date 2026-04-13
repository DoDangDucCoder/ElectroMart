@if ($items->isNotEmpty())
    <ul class="hover-cart">
        @foreach ($items as $item)
            @php
                $image = $item['product_image'] ? asset($item['product_image']) : asset('uploads/product/no-image.svg');
            @endphp
            <li>
                <div class="hover-cart-item-copy">
                    <img src="{{ $image }}">
                    <p>{{ $item['product_name'] }}</p>
                    <p>{{ number_format($item['product_price'], 0, ',', '.') }} VND</p>
                    <p>Số lượng: {{ $item['product_qty'] }}</p>
                </div>
                <p>
                    <button type="button" class="delele-hover-cart btn btn-link" data-hover-cart-remove="true"
                        data-session-id="{{ $item['session_id'] }}">
                        <i class="fa fa-times"></i>
                    </button>
                </p>
            </li>
        @endforeach
    </ul>
@endif
