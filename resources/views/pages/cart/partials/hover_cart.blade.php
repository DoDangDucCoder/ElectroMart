@if ($items->isNotEmpty())
    <ul class="hover-cart">
        @foreach ($items as $item)
            @php
                $image = $item['product_image'] ? asset($item['product_image']) : asset('uploads/product/no-image.svg');
            @endphp
            <li>
                <a href="#">
                    <img src="{{ $image }}">
                    <p>{{ $item['product_name'] }}</p>
                    <p>{{ number_format($item['product_price'], 0, ',', '.') }} VND</p>
                    <p>Số lượng: {{ $item['product_qty'] }}</p>
                </a>
                <p>
                    <a style="text-align:center;font-size:20px" class="delele-hover-cart" href="{{ url('/del-product/' . $item['session_id']) }}">
                        <i class="fa fa-times"></i>
                    </a>
                </p>
            </li>
        @endforeach
    </ul>
@endif
