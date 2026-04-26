@if ($items->isNotEmpty())
    <ul class="hover-cart">
        @foreach ($items as $item)
            @php
                $image = \App\Support\AssetUrl::product($item['product_image'] ?? null);
            @endphp
            <li style="position: relative; padding-right: 35px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #f1f5f9;">
                <div class="hover-cart-item-copy" style="display: flex; gap: 15px; align-items: center;">
                    <img src="{{ $image }}" alt="{{ $item['product_name'] }}" style="width: 65px; height: 65px; border-radius: 12px; object-fit: contain; background: #f8fafc; border: 1px solid #edf2f7; padding: 5px;">
                    <div style="flex: 1; min-width: 0;">
                        <p style="white-space: normal; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4; margin: 0; font-weight: 700; font-size: 14px; color: #0f172a;">{{ $item['product_name'] }}</p>
                        <p style="margin: 6px 0; color: #d50018; font-weight: 800; font-size: 16px;">{{ number_format($item['product_price'], 0, ',', '.') }}đ</p>
                        <p style="margin: 0; font-size: 12px; color: #64748b; font-weight: 600;">Số lượng: {{ $item['product_qty'] }}</p>
                    </div>
                </div>
                <button type="button" class="delele-hover-cart" data-hover-cart-remove="true"
                    data-session-id="{{ $item['session_id'] }}" title="Xóa sản phẩm"
                    style="position: absolute; right: 0; top: 0; border: none; background: #f8fafc; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #94a3b8; cursor: pointer; transition: all 0.2s; border: 1px solid #eee;">
                    <i class="fa fa-times" style="font-size: 14px;"></i>
                </button>
            </li>
        @endforeach
    </ul>
    <div class="hover-cart-actions">
        <a href="{{ url('/gio-hang') }}" class="hover-cart-btn hover-cart-btn-secondary">Giỏ hàng</a>
        <a href="{{ url('/checkout') }}" class="hover-cart-btn hover-cart-btn-primary">Thanh toán</a>
    </div>
@else
    <div style="text-align: center; padding: 20px;">
        <i class="fa fa-shopping-basket" style="font-size: 40px; color: #e2e8f0; margin-bottom: 10px; display: block;"></i>
        <p style="margin: 0; color: #94a3b8;">Giỏ hàng trống</p>
    </div>
@endif
