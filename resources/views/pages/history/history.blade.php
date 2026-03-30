@extends('layout')

@section('body_class', 'g-history')

@section('content')
    @php
        $totalOrders = $historyStats['total_orders'] ?? 0;
        $activeOrders = $historyStats['active_orders'] ?? 0;
        $cancelledOrders = $historyStats['cancelled_orders'] ?? 0;
        $latestOrderCode = $historyStats['latest_order_code'] ?? null;
        $supportConfig = config('services.storefront_support', []);
        $supportPhoneDisplay = $supportConfig['phone_display'] ?? '1900.0000';
        $supportPhoneHref = $supportConfig['phone_href'] ?? 'tel:19000000';
        $historyQuickLinks = [
            [
                'icon' => 'fa-shopping-bag',
                'label' => config('storefront.labels.buy_more_products', 'Mua thêm sản phẩm'),
                'value' => config('storefront.copy.history_buy_more_value', 'Quay lại trang chủ để tiếp tục chọn hàng'),
                'url' => url('/'),
            ],
            [
                'icon' => 'fa-phone',
                'label' => config('storefront.labels.hotline_after_sales', 'Hotline hậu mãi'),
                'value' => $supportPhoneDisplay,
                'url' => $supportPhoneHref,
            ],
            [
                'icon' => 'fa-lightbulb-o',
                'label' => config('storefront.labels.recommend_for_you', 'Gợi ý cho bạn'),
                'value' => config('storefront.copy.history_recommend_value', 'Khám phá thêm từ lịch sử mua và xem'),
                'url' => url('/goi-y-san-pham'),
            ],
        ];
    @endphp

    <section class="shopw-history-page">
        <div class="shopw-history-breadcrumbs">
            <ol class="breadcrumb">
                <li><a href="{{ url('/') }}">Trang chủ</a></li>
                <li class="active">Lịch sử đơn hàng</li>
            </ol>
        </div>

        @if (session('message'))
            <div class="alert alert-success">{!! session('message') !!}</div>
        @elseif(session('error'))
            <div class="alert alert-danger">{!! session('error') !!}</div>
        @endif

        <div class="shopw-history-hero">
            <div class="shopw-history-hero-copy">
                <span class="shopw-history-kicker">Đơn hàng</span>
                <h1>Lịch sử đơn hàng của bạn</h1>
                <p>Theo dõi trạng thái, xem lại chi tiết từng đơn và gửi yêu cầu hủy trực tiếp từ tài khoản của bạn.</p>
            </div>
            <div class="shopw-history-hero-stats">
                <div class="shopw-history-stat">
                    <span>Tổng đơn hàng</span>
                    <strong>{{ number_format($totalOrders) }}</strong>
                </div>
                <div class="shopw-history-stat">
                    <span>Đơn có thể hủy</span>
                    <strong>{{ number_format($activeOrders) }}</strong>
                </div>
                <div class="shopw-history-stat">
                    <span>Đơn gần nhất</span>
                    <strong>{{ $latestOrderCode ?: 'Chưa có đơn' }}</strong>
                </div>
            </div>
        </div>

        <div class="shopw-history-support-strip">
            @foreach ($historyQuickLinks as $quickLink)
                <a href="{{ $quickLink['url'] }}" class="shopw-history-support-item">
                    <span class="shopw-history-support-icon">
                        <i class="fa {{ $quickLink['icon'] }}"></i>
                    </span>
                    <span class="shopw-history-support-copy">
                        <strong>{{ $quickLink['label'] }}</strong>
                        <small>{{ $quickLink['value'] }}</small>
                    </span>
                    <span class="shopw-history-support-arrow">
                        <i class="fa fa-angle-right"></i>
                    </span>
                </a>
            @endforeach
        </div>

        @if ($orders->count() === 0)
            <div class="shopw-history-empty">
                <div class="shopw-history-empty-icon">
                    <i class="fa fa-inbox"></i>
                </div>
                <h2>Chưa có đơn hàng nào</h2>
                <p>Hệ thống hiện chưa ghi nhận đơn nào trong tài khoản này. Hãy quay lại trang chủ để tiếp tục mua sắm.</p>
                <a href="{{ url('/') }}" class="shopw-history-primary-link">
                    <i class="fa fa-arrow-left"></i>Tiếp tục mua sắm
                </a>
            </div>
        @else
            <div class="shopw-history-results">
                <div class="shopw-history-results-head">
                    <div>
                        <h2>Danh sách đơn hàng</h2>
                        <p>{{ number_format($cancelledOrders) }} đơn đã hủy trong danh sách hiện tại.</p>
                    </div>
                    <a href="{{ url('/') }}" class="shopw-history-secondary-link">
                        <i class="fa fa-shopping-bag"></i>Mua thêm
                    </a>
                </div>

                <div class="shopw-history-order-list">
                    @foreach ($orders as $order)
                        <article class="shopw-history-order-card">
                            <div class="shopw-history-order-head">
                                <div>
                                    <span class="shopw-history-order-code">#{{ $order['code'] }}</span>
                                    <h3>{{ $order['status_label'] }}</h3>
                                    <p>Đặt lúc {{ $order['placed_at_label'] }}</p>
                                </div>
                                <div class="shopw-history-order-badges">
                                    <span class="shopw-history-badge is-{{ $order['status'] }}">
                                        {{ $order['status_label'] }}
                                    </span>
                                    <span class="shopw-history-badge is-muted">
                                        {{ $order['payment_label'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="shopw-history-order-grid">
                                <div class="shopw-history-order-metric">
                                    <span>Số sản phẩm</span>
                                    <strong>{{ number_format($order['items_count']) }}</strong>
                                </div>
                                <div class="shopw-history-order-metric">
                                    <span>Thanh toán</span>
                                    <strong>{{ $order['payment_status_label'] }}</strong>
                                </div>
                                <div class="shopw-history-order-metric">
                                    <span>Tổng đơn</span>
                                    <strong>{{ number_format($order['total'], 0, ',', '.') }} VND</strong>
                                </div>
                            </div>

                            @if (!empty($order['cancel_reason']))
                                <div class="shopw-history-order-note is-cancelled">
                                    <i class="fa fa-info-circle"></i>
                                    <span>Lý do hủy: {{ $order['cancel_reason'] }}</span>
                                </div>
                            @endif

                            <div class="shopw-history-order-actions">
                                <a href="{{ url('/view-history-order/' . $order['code']) }}" class="shopw-history-primary-link">
                                    <i class="fa fa-file-text-o"></i>Xem chi tiết
                                </a>

                                @if ($order['can_cancel'])
                                    <button type="button" class="shopw-history-secondary-link is-button"
                                        data-toggle="modal" data-target="#cancelOrderModal"
                                        data-order-code="{{ $order['code'] }}">
                                        <i class="fa fa-ban"></i>Hủy đơn hàng
                                    </button>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="shopw-history-pagination">
                    {!! $orders->links() !!}
                </div>
            </div>
        @endif
    </section>

    @include('pages.history.partials.cancel_order_modal')
@endsection

