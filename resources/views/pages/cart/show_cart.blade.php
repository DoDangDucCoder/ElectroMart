@extends('layout')
@section('content')
    <section id="cart_items">
        <div class="container">
            <div class="breadcrumbs">
                <ol class="breadcrumb">
                    <li><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="active">Giỏ hàng của bạn</li>
                </ol>
            </div>

            @if (session('message'))
                <div class="alert alert-success">{!! session('message') !!}</div>
            @elseif(session('error'))
                <div class="alert alert-danger">{!! session('error') !!}</div>
            @endif

            <div class="table-responsive cart_info">
                <form action="{{ url('/update-cart') }}" method="POST">
                    @csrf
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
                            @forelse ($cartItems as $item)
                                @php
                                    $image = $item['product_image']
                                        ? asset($item['product_image'])
                                        : asset('uploads/product/no-image.svg');
                                    $subtotal = (float) $item['product_price'] * (int) $item['product_qty'];
                                @endphp
                                <tr>
                                    <td class="cart_product">
                                        <img src="{{ $image }}" alt="{{ $item['product_name'] }}" width="90">
                                    </td>
                                    <td class="cart_description">
                                        <p>{{ $item['product_name'] }}</p>
                                    </td>
                                    <td>
                                        {{ $item['product_attribute'] ?? '-' }}
                                    </td>
                                    <td class="cart_price">
                                        <p>{{ number_format($item['product_price'], 0, ',', '.') }}đ</p>
                                    </td>
                                    <td class="cart_quantity">
                                        <div class="cart_quantity_button">
                                            <input class="cart_quantity" type="number" min="1"
                                                name="cart_qty[{{ $item['session_id'] }}]"
                                                value="{{ $item['product_qty'] }}">
                                        </div>
                                    </td>
                                    <td class="cart_total">
                                        <p class="cart_total_price">{{ number_format($subtotal, 0, ',', '.') }}đ</p>
                                    </td>
                                    <td class="cart_delete">
                                        <a class="cart_quantity_delete" href="{{ url('/del-product/' . $item['session_id']) }}">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        Giỏ hàng trống. Vui lòng chọn sản phẩm để tiếp tục.
                                    </td>
                                </tr>
                            @endforelse

                            @if ($cartItems->isNotEmpty())
                                <tr>
                                    <td>
                                        <input type="submit" value="Cập nhật giỏ hàng" name="update_qty"
                                            class="check_out btn btn-default btn-sm">
                                    </td>
                                    <td>
                                        <a class="btn btn-default check_out" href="{{ url('/del-all-product') }}">
                                            Xóa tất cả
                                        </a>
                                    </td>
                                    <td>
                                        @if ($coupon)
                                            <a class="btn btn-default check_out" href="{{ url('/unset-coupon') }}">
                                                Xóa mã khuyến mại
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if (Session::get('customer_id'))
                                            <a class="btn btn-default check_out" href="{{ url('/checkout') }}">Đặt hàng</a>
                                        @else
                                            <a class="btn btn-default check_out"
                                                href="{{ url('/dang-nhap') . '?' . http_build_query([
                                                    'redirect' => 'checkout',
                                                    'redirect_to' => url('/checkout'),
                                                ]) }}">Đăng nhập để thanh toán</a>
                                        @endif
                                    </td>
                                    <td colspan="3" class="cart_totals">
                                        <ul class="list-unstyled">
                                            <li>Tạm tính:
                                                <span class="cart_price">
                                                    {{ number_format($cartTotals['subtotal'], 0, ',', '.') }}đ
                                                </span>
                                            </li>
                                            <li>Giảm giá:
                                                <span class="cart_price text-danger">
                                                    -{{ number_format($cartTotals['discount'], 0, ',', '.') }}đ
                                                </span>
                                            </li>
                                            <li>Phí vận chuyển:
                                                <span class="cart_price">
                                                    {{ number_format($cartTotals['shipping'], 0, ',', '.') }}đ
                                                </span>
                                            </li>
                                            <li><strong>Tổng cộng:</strong>
                                                <span class="cart_price" style="font-size: 20px;color:brown">
                                                    {{ number_format($cartTotals['total'], 0, ',', '.') }}đ
                                                </span>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </form>

                @if ($cartItems->isNotEmpty())
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <form method="POST" action="{{ url('/check-coupon') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="coupon_show">Mã giảm giá</label>
                                    <input type="text" class="form-control" id="coupon_show" name="coupon"
                                        placeholder="Nhập mã giảm giá">
                                </div>
                                <button type="submit" class="btn btn-default check_coupon">Áp dụng mã</button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
