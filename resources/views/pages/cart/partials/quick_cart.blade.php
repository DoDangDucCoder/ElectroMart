<table class="table table-condensed">
    <thead>
        <tr class="cart_menu">
            <td class="image">Hình ảnh</td>
            <td class="description">Tên sản phẩm</td>
            <td class="description">Thuộc tính</td>
            <td class="price">Giá</td>
            <td class="quantity">Số lượng</td>
            <td class="total">Thành tiền</td>
            <td></td>
        </tr>
    </thead>
    <tbody>
        @forelse ($items as $item)
            @php
                $image = $item['product_image'] ? asset($item['product_image']) : asset('uploads/product/no-image.svg');
                $subtotal = (float) $item['product_price'] * (int) $item['product_qty'];
            @endphp
            <tr>
                <td><img src="{{ $image }}" alt="{{ $item['product_name'] }}" width="80"></td>
                <td>{{ $item['product_name'] }}</td>
                <td>{{ $item['product_attribute'] ?? '-' }}</td>
                <td>{{ number_format($item['product_price'], 0, ',', '.') }} VND</td>
                <td>
                    <input type="number" class="cart_quantity_input cart_qty_update"
                        name="cart_qty[{{ $item['session_id'] }}]" data-session_id="{{ $item['session_id'] }}"
                        value="{{ $item['product_qty'] }}" min="1">
                </td>
                <td>{{ number_format($subtotal, 0, ',', '.') }} VND</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" data-quick-cart-remove="true"
                        data-session-id="{{ $item['session_id'] }}">
                        Xóa
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">Giỏ hàng trống.</td>
            </tr>
        @endforelse

        @if ($items->isNotEmpty())
            <tr class="cart_timing">
                <td colspan="7">
                    <strong>Tạm tính:</strong> {{ number_format($totals['subtotal'], 0, ',', '.') }} VND<br>
                    <strong>Giảm giá:</strong> -{{ number_format($totals['discount'], 0, ',', '.') }} VND<br>
                    <strong>Phí vận chuyển:</strong> {{ number_format($totals['shipping'], 0, ',', '.') }} VND<br>
                    <strong>Tổng:</strong> {{ number_format($totals['total'], 0, ',', '.') }} VND
                </td>
            </tr>
        @endif
    </tbody>
</table>
