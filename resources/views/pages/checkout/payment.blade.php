@extends('layouts.home-tech')
@section('content')
    <section id="cart_items">
        <div class="container">
            @include('partials.storefront.breadcrumb', [
                'class' => 'breadcrumbs',
                'items' => [
                    [
                        'label' => 'Thanh toán giỏ hàng',
                        'current' => true,
                    ],
                ],
            ])

            <div class="review-payment">
                <h2>Xem lại giỏ hàng</h2>
            </div>

            <div class="table-responsive cart_info">
                <table class="table table-condensed">
                    <thead>
                        <tr class="cart_menu">
                            <td class="image">Hình ảnh</td>
                            <td class="description">Tên sản phẩm</td>
                            <td class="price">Giá</td>
                            <td class="quantity">Số lượng</td>
                            <td class="total">Thành tiền</td>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cartItems as $item)
                            @php
                                $image = \App\Support\AssetUrl::product($item['product_image'] ?? null);
                                $subtotal = (float) $item['product_price'] * (int) $item['product_qty'];
                            @endphp
                            <tr>
                                <td class="cart_product">
                                    <img src="{{ $image }}" alt="{{ $item['product_name'] }}" width="90">
                                </td>
                                <td class="cart_description">
                                    <p>{{ $item['product_name'] }}</p>
                                    @if (! empty($item['product_attribute']))
                                        <small>{{ $item['product_attribute'] }}</small>
                                    @endif
                                </td>
                                <td class="cart_price">
                                    <p>{{ number_format($item['product_price'], 0, ',', '.') }}đ</p>
                                </td>
                                <td class="cart_quantity">{{ $item['product_qty'] }}</td>
                                <td class="cart_total">
                                    <p class="cart_total_price">{{ number_format($subtotal, 0, ',', '.') }}đ</p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Giỏ hàng trống.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="cart_totals mt-3">
                <ul class="list-unstyled">
                    <li>Tạm tính: <strong>{{ number_format($cartTotals['subtotal'], 0, ',', '.') }}đ</strong></li>
                    <li>Giảm giá: <strong class="text-danger">-{{ number_format($cartTotals['discount'], 0, ',', '.') }}đ</strong></li>
                    <li>Phí vận chuyển: <strong>{{ number_format($cartTotals['shipping'], 0, ',', '.') }}đ</strong></li>
                    <li>Tổng cộng: <strong style="font-size: 20px; color: brown;">{{ number_format($cartTotals['total'], 0, ',', '.') }}đ</strong></li>
                </ul>
            </div>

            <div class="payment-options">
                <p>Vui lòng chọn phương thức thanh toán ở bước trước và xác nhận đơn hàng.</p>
                <a class="btn btn-primary" href="{{ url('/checkout') }}">Quay lại thanh toán</a>
            </div>
        </div>
    </section>
@endsection
