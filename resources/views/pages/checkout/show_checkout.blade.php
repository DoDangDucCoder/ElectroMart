@extends('layout')
@section('content')
    <section id="cart_items">
        <div class="container">
            <div class="breadcrumbs">
                <ol class="breadcrumb">
                    <li><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="active">Thanh toán giỏ hàng</li>
                </ol>
            </div>

            @if (session('message'))
                <div class="alert alert-success">{!! session('message') !!}</div>
            @elseif(session('error'))
                <div class="alert alert-danger">{!! session('error') !!}</div>
            @endif

            <div class="shopper-informations">
                <div class="row">
                    <div class="col-md-6 form-style">
                        <div class="bill-to">
                            <p>Điền thông tin giao hàng</p>
                            <form id="checkout-form" method="POST">
                                @csrf
                                <input type="text" name="shipping_email" class="shipping_email form-control" placeholder="Email" required>
                                <input type="text" name="shipping_name" class="shipping_name form-control" placeholder="Họ và tên" required>
                                <input type="text" name="shipping_address" class="shipping_address form-control" placeholder="Địa chỉ" required>
                                <input type="text" name="shipping_phone" class="shipping_phone form-control" placeholder="Số điện thoại" required>
                                <textarea name="shipping_notes" class="shipping_notes form-control" rows="4" placeholder="Ghi chú"></textarea>

                                <div class="row mt-3">
                                    <div class="col-sm-4">
                                        <select name="city_id" class="form-control choose city" id="city">
                                            <option value="">---Chọn tỉnh / thành---</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <select name="province_id" class="form-control choose province" id="province">
                                            <option value="">---Chọn quận / huyện---</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <select name="ward_id" class="form-control wards" id="wards">
                                            <option value="">---Chọn phường / xã---</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-2">
                                    <button type="button" class="btn btn-default calculate_delivery">Tính phí vận chuyển</button>
                                </div>

                                <div class="mt-3">
                                    <label for="payment_select">Chọn phương thức thanh toán</label>
                                    <select name="shipping_method" class="form-control input-sm payment_select">
                                        <option value="cod">Thanh toán khi nhận hàng</option>
                                        <option value="bank_transfer">Chuyển khoản ngân hàng</option>
                                    </select>
                                </div>

                                <input type="hidden" name="order_fee" class="order_fee" value="{{ $shipping_fee }}">
                                <input type="hidden" name="order_coupon" class="order_coupon" value="{{ $coupon['code'] ?? 'no' }}">

                                <button type="button" class="btn btn-primary btn-sm mt-3 send_order">Xác nhận đơn hàng</button>
                            </form>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="table-responsive cart_info">
                            <form action="{{ url('/update-cart') }}" method="POST">
                                @csrf
                                <table class="table table-condensed">
                                    <thead>
                                        <tr class="cart_menu">
                                            <td class="image">Hình ảnh</td>
                                            <td class="description">Tên sản phẩm</td>
                                            <td class="price">Giá</td>
                                            <td class="quantity">Số lượng</td>
                                            <td class="total">Thành tiền</td>
                                            <td></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($cartItems as $item)
                                            @php
                                                $image = $item['product_image'] ? asset($item['product_image']) : asset('frontend/images/no-image.png');
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
                                                <td class="cart_quantity">
                                                    <input class="cart_quantity" type="number" min="1"
                                                        name="cart_qty[{{ $item['session_id'] }}]"
                                                        value="{{ $item['product_qty'] }}">
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
                                                <td colspan="6" class="text-center text-muted">Giỏ hàng trống.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                @if ($cartItems->isNotEmpty())
                                    <div class="d-flex justify-content-between align-items-center">
                                        <button type="submit" name="update_qty" class="btn btn-default btn-sm">Cập nhật giỏ hàng</button>
                                        <a class="btn btn-default btn-sm" href="{{ url('/del-all-product') }}">Xóa tất cả</a>
                                    </div>
                                @endif
                            </form>
                        </div>

                        <div class="cart_totals mt-4">
                            <ul class="list-unstyled">
                                <li>Tạm tính: <strong>{{ number_format($cartTotals['subtotal'], 0, ',', '.') }}đ</strong></li>
                                <li>Giảm giá: <strong class="text-danger">-{{ number_format($cartTotals['discount'], 0, ',', '.') }}đ</strong></li>
                                <li>Phí vận chuyển: <strong>{{ number_format($cartTotals['shipping'], 0, ',', '.') }}đ</strong></li>
                                <li>Tổng cộng: <strong style="font-size: 20px; color: brown;">{{ number_format($cartTotals['total'], 0, ',', '.') }}đ</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
