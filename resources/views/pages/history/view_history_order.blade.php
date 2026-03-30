@extends('layout')

@section('body_class', 'g-order-detail')

@section('content')
    @php
        $customer = $orderView['customer'];
        $shipping = $orderView['shipping'];
        $payment = $orderView['payment'];
        $totals = $orderView['totals'];
        $supportConfig = config('services.storefront_support', []);
        $supportPhoneDisplay = $supportConfig['phone_display'] ?? '1900.0000';
        $supportPhoneHref = $supportConfig['phone_href'] ?? 'tel:19000000';
        $orderDetailQuickLinks = [
            [
                'icon' => 'fa-history',
                'label' => 'Quay lại lịch sử đơn',
                'value' => 'Xem danh sách tất cả đơn hàng của bạn',
                'url' => url('/history'),
            ],
            [
                'icon' => 'fa-phone',
                'label' => config('storefront.labels.hotline_support', 'Hotline hỗ trợ'),
                'value' => $supportPhoneDisplay,
                'url' => $supportPhoneHref,
            ],
            [
                'icon' => 'fa-lightbulb-o',
                'label' => 'Gợi ý sản phẩm',
                'value' => 'Tiếp tục mua sắm với gợi ý phù hợp',
                'url' => url('/goi-y-san-pham'),
            ],
        ];
    @endphp

    <section class="shopw-order-detail-page">
        <div class="shopw-order-detail-breadcrumbs">
            <ol class="breadcrumb">
                <li><a href="{{ url('/') }}">Trang chủ</a></li>
                <li><a href="{{ url('/history') }}">Lịch sử đơn hàng</a></li>
                <li class="active">Chi tiết đơn #{{ $orderView['code'] }}</li>
            </ol>
        </div>

        @if (session('message'))
            <div class="alert alert-success">{!! session('message') !!}</div>
        @elseif(session('error'))
            <div class="alert alert-danger">{!! session('error') !!}</div>
        @endif

        <div class="shopw-order-detail-hero">
            <div class="shopw-order-detail-hero-copy">
                <span class="shopw-order-detail-kicker">Chi tiết đơn hàng</span>
                <h1>Đơn hàng #{{ $orderView['code'] }}</h1>
                <p>Đặt lúc {{ $orderView['placed_at_label'] }}. Theo dõi trạng thái, thông tin giao hàng và toàn bộ sản phẩm trong đơn.</p>
            </div>
            <div class="shopw-order-detail-hero-meta">
                <span class="shopw-history-badge is-{{ $orderView['status'] }}">
                    {{ $orderView['status_label'] }}
                </span>
                <strong>{{ number_format($totals['total'], 0, ',', '.') }} VND</strong>
                @if ($orderView['can_cancel'])
                    <button type="button" class="shopw-order-detail-inline-link is-button"
                        data-toggle="modal" data-target="#cancelOrderModal"
                        data-order-code="{{ $orderView['code'] }}">
                        <i class="fa fa-ban"></i>Hủy đơn
                    </button>
                @endif
            </div>
        </div>

        @if (!empty($orderView['cancel_reason']))
            <div class="shopw-order-detail-alert is-cancelled">
                <i class="fa fa-info-circle"></i>
                <span>Lý do hủy: {{ $orderView['cancel_reason'] }}</span>
            </div>
        @endif

        <div class="shopw-order-detail-support-strip">
            @foreach ($orderDetailQuickLinks as $quickLink)
                <a href="{{ $quickLink['url'] }}" class="shopw-order-detail-support-item">
                    <span class="shopw-order-detail-support-icon">
                        <i class="fa {{ $quickLink['icon'] }}"></i>
                    </span>
                    <span class="shopw-order-detail-support-copy">
                        <strong>{{ $quickLink['label'] }}</strong>
                        <small>{{ $quickLink['value'] }}</small>
                    </span>
                    <span class="shopw-order-detail-support-arrow">
                        <i class="fa fa-angle-right"></i>
                    </span>
                </a>
            @endforeach
        </div>

        <div class="shopw-order-detail-layout">
            <div class="shopw-order-detail-main">
                <div class="shopw-order-detail-card">
                    <div class="shopw-order-detail-card-head">
                        <h2>Sản phẩm trong đơn</h2>
                        <p>Thông tin đơn hàng được lưu lại tại thời điểm bạn xác nhận đặt mua.</p>
                    </div>

                    <div class="shopw-order-detail-items">
                        @foreach ($orderView['items'] as $item)
                            @php
                                $image = !empty($item['image'])
                                    ? asset($item['image'])
                                    : asset('uploads/product/no-image.svg');
                            @endphp
                            <article class="shopw-order-detail-item">
                                <div class="shopw-order-detail-item-media">
                                    <img src="{{ $image }}" alt="{{ $item['name'] }}">
                                </div>
                                <div class="shopw-order-detail-item-copy">
                                    <h3>{{ $item['name'] }}</h3>
                                    <div class="shopw-order-detail-item-meta">
                                        <span>{{ $item['attributes'] }}</span>
                                        <span>SL: {{ number_format($item['quantity']) }}</span>
                                    </div>
                                    <div class="shopw-order-detail-item-pricing">
                                        <strong>{{ number_format($item['subtotal'], 0, ',', '.') }} VND</strong>
                                        <small>{{ number_format($item['price'], 0, ',', '.') }} VND / sản phẩm</small>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>

            <aside class="shopw-order-detail-sidebar">
                <div class="shopw-order-detail-card">
                    <div class="shopw-order-detail-card-head">
                        <h2>Thông tin khách hàng</h2>
                    </div>
                    <ul class="shopw-order-detail-info-list">
                        <li>
                            <span>Họ tên</span>
                            <strong>{{ $customer['name'] }}</strong>
                        </li>
                        <li>
                            <span>Email</span>
                            <strong>{{ $customer['email'] ?: 'Chưa có' }}</strong>
                        </li>
                        <li>
                            <span>Điện thoại</span>
                            <strong>{{ $customer['phone'] ?: 'Chưa có' }}</strong>
                        </li>
                    </ul>
                </div>

                <div class="shopw-order-detail-card">
                    <div class="shopw-order-detail-card-head">
                        <h2>Giao hàng</h2>
                    </div>
                    <ul class="shopw-order-detail-info-list">
                        <li>
                            <span>Người nhận</span>
                            <strong>{{ $shipping['name'] ?: 'Chưa có' }}</strong>
                        </li>
                        <li>
                            <span>Điện thoại</span>
                            <strong>{{ $shipping['phone'] ?: 'Chưa có' }}</strong>
                        </li>
                        <li>
                            <span>Email</span>
                            <strong>{{ $shipping['email'] ?: 'Chưa có' }}</strong>
                        </li>
                        <li>
                            <span>Địa chỉ</span>
                            <strong>{{ $shipping['address'] ?: 'Chưa có' }}</strong>
                        </li>
                        <li>
                            <span>Phương thức</span>
                            <strong>{{ $shipping['method_label'] }}</strong>
                        </li>
                        <li>
                            <span>Ghi chú</span>
                            <strong>{{ $shipping['notes'] ?: 'Không có ghi chú' }}</strong>
                        </li>
                        @if (!empty($shipping['tracking_number']))
                            <li>
                                <span>Mã vận đơn</span>
                                <strong>{{ $shipping['tracking_number'] }}</strong>
                            </li>
                        @endif
                    </ul>
                </div>

                <div class="shopw-order-detail-card">
                    <div class="shopw-order-detail-card-head">
                        <h2>Thanh toán</h2>
                    </div>
                    <ul class="shopw-order-detail-info-list">
                        <li>
                            <span>Phương thức</span>
                            <strong>{{ $payment['provider_label'] }}</strong>
                        </li>
                        <li>
                            <span>Trạng thái</span>
                            <strong>{{ $payment['status_label'] }}</strong>
                        </li>
                        <li>
                            <span>Số tiền</span>
                            <strong>{{ number_format($payment['amount'], 0, ',', '.') }} VND</strong>
                        </li>
                        @if (!empty($payment['reference']))
                            <li>
                                <span>Mã tham chiếu</span>
                                <strong>{{ $payment['reference'] }}</strong>
                            </li>
                        @endif
                    </ul>
                </div>

                <div class="shopw-order-detail-card">
                    <div class="shopw-order-detail-card-head">
                        <h2>Tổng kết đơn hàng</h2>
                    </div>
                    <ul class="shopw-order-detail-summary-list">
                        <li>
                            <span>Tạm tính</span>
                            <strong>{{ number_format($totals['subtotal'], 0, ',', '.') }} VND</strong>
                        </li>
                        <li>
                            <span>Giảm giá</span>
                            <strong class="is-discount">-{{ number_format($totals['discount'], 0, ',', '.') }} VND</strong>
                        </li>
                        <li>
                            <span>Phí vận chuyển</span>
                            <strong>{{ number_format($totals['shipping'], 0, ',', '.') }} VND</strong>
                        </li>
                        <li class="is-grand-total">
                            <span>Tổng thanh toán</span>
                            <strong>{{ number_format($totals['total'], 0, ',', '.') }} VND</strong>
                        </li>
                    </ul>

                    @if (!empty($orderView['coupon_code']))
                        <div class="shopw-order-detail-chip">
                            <span>Coupon</span>
                            <strong>{{ $orderView['coupon_code'] }}</strong>
                        </div>
                    @endif

                    @if (!empty($orderView['note']))
                        <div class="shopw-order-detail-note">
                            <span>Ghi chú đơn hàng</span>
                            <p>{{ $orderView['note'] }}</p>
                        </div>
                    @endif
                </div>
            </aside>
        </div>
    </section>

    @include('pages.history.partials.cancel_order_modal')
@endsection

