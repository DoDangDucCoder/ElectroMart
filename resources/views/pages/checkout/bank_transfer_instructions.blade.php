@extends('layouts.home-tech')

@section('body_class', 'g-bank-transfer-instructions')

@section('content')
    <section class="dd-bank-transfer-page">
        <div class="dd-bank-transfer-container">
            @include('partials.storefront.breadcrumb', [
                'class' => 'bt-crumb',
                'separator' => '›',
                'items' => [
                    ['label' => 'Giỏ hàng', 'url' => url('/gio-hang')],
                    ['label' => 'Thanh toán', 'url' => url('/payment')],
                    ['label' => 'Hướng dẫn chuyển khoản', 'current' => true],
                ],
            ])

            <div class="dd-bank-transfer-card">
                <div class="dd-bank-transfer-header">
                    <h1>
                        @if ($order->payment_status === 'paid')
                            <i class="fa fa-check-circle dd-bank-transfer-title-icon dd-bank-transfer-title-icon--paid"></i> Đơn hàng đã thanh toán
                        @elseif ($order->status === 'cancelled')
                            <i class="fa fa-times-circle dd-bank-transfer-title-icon dd-bank-transfer-title-icon--cancelled"></i> Đơn hàng đã hủy
                        @else
                            <i class="fa fa-university"></i> Hướng dẫn chuyển khoản
                        @endif
                    </h1>
                    <div class="dd-bank-transfer-order-code">Mã đơn hàng: {{ $order->code }}</div>
                    <div class="dd-bank-transfer-status-row">
                        <span class="dd-bank-transfer-status-badge {{ $order->status === 'cancelled' ? 'is-red' : ($order->status === 'completed' ? 'is-green' : 'is-blue') }}">
                            {{ $statusLabel }}
                        </span>
                        <span class="dd-bank-transfer-status-badge {{ $order->payment_status === 'paid' ? 'is-green' : ($order->payment_status === 'failed' ? 'is-red' : 'is-yellow') }}">
                            {{ $paymentStatusLabel }}
                        </span>
                    </div>
                </div>

                @if ($order->payment_status === 'paid')
                    <div class="dd-bank-transfer-alert dd-bank-transfer-alert--success">
                        <strong><i class="fa fa-check-circle"></i> Đã thanh toán</strong>
                        <p>Đơn hàng của bạn đã được thanh toán thành công. Chúng tôi sẽ xử lý và giao hàng sớm nhất.</p>
                    </div>
                @elseif ($order->status === 'cancelled')
                    <div class="dd-bank-transfer-alert dd-bank-transfer-alert--error">
                        <strong><i class="fa fa-times-circle"></i> Đơn hàng đã hủy</strong>
                        <p>Đơn hàng này đã bị hủy. Nếu bạn đã chuyển khoản, vui lòng liên hệ với chúng tôi để được hỗ trợ hoàn tiền.</p>
                    </div>
                @else
                    <div class="dd-bank-transfer-alert">
                        <strong><i class="fa fa-info-circle"></i> Đơn hàng đang chờ thanh toán</strong>
                        <p>Vui lòng chuyển khoản theo thông tin bên dưới. Đơn hàng sẽ được xác nhận sau khi chúng tôi nhận được thanh toán.</p>
                    </div>
                @endif

                @if ($order->status !== 'cancelled')
                    <div class="dd-bank-transfer-info-section">
                        <h3><i class="fa fa-university"></i> Thông tin chuyển khoản</h3>
                        <div class="dd-bank-transfer-info-row">
                            <span class="dd-bank-transfer-info-label">Ngân hàng</span>
                            <span class="dd-bank-transfer-info-value">{{ $bankInfo['bank_name'] ?? 'N/A' }}</span>
                        </div>
                        @if (! empty($bankInfo['branch']))
                            <div class="dd-bank-transfer-info-row">
                                <span class="dd-bank-transfer-info-label">Chi nhánh</span>
                                <span class="dd-bank-transfer-info-value">{{ $bankInfo['branch'] }}</span>
                            </div>
                        @endif
                        <div class="dd-bank-transfer-info-row">
                            <span class="dd-bank-transfer-info-label">Số tài khoản</span>
                            <span class="dd-bank-transfer-info-value is-highlight">{{ $bankInfo['account_number'] ?? 'N/A' }}</span>
                        </div>
                        <div class="dd-bank-transfer-info-row">
                            <span class="dd-bank-transfer-info-label">Chủ tài khoản</span>
                            <span class="dd-bank-transfer-info-value">{{ $bankInfo['account_holder'] ?? 'N/A' }}</span>
                        </div>
                        <div class="dd-bank-transfer-info-row">
                            <span class="dd-bank-transfer-info-label">Nội dung chuyển khoản</span>
                            <span class="dd-bank-transfer-info-value is-highlight">{{ $transferContent }}</span>
                        </div>
                        <div class="dd-bank-transfer-total-amount">
                            <div class="dd-bank-transfer-total-label">Số tiền cần chuyển</div>
                            <div class="dd-bank-transfer-total-value">{{ number_format($order->total, 0, ',', '.') }}đ</div>
                        </div>
                    </div>
                @endif

                <div class="dd-bank-transfer-info-section">
                    <h3><i class="fa fa-shopping-cart"></i> Thông tin đơn hàng</h3>
                    <div class="dd-bank-transfer-info-row">
                        <span class="dd-bank-transfer-info-label">Tổng tiền hàng</span>
                        <span class="dd-bank-transfer-info-value">{{ number_format($order->subtotal, 0, ',', '.') }}đ</span>
                    </div>
                    @if ($order->discount_total > 0)
                        <div class="dd-bank-transfer-info-row">
                            <span class="dd-bank-transfer-info-label">Giảm giá</span>
                            <span class="dd-bank-transfer-info-value is-discount">-{{ number_format($order->discount_total, 0, ',', '.') }}đ</span>
                        </div>
                    @endif
                    <div class="dd-bank-transfer-info-row">
                        <span class="dd-bank-transfer-info-label">Phí vận chuyển</span>
                        <span class="dd-bank-transfer-info-value">{{ number_format($order->shipping_total, 0, ',', '.') }}đ</span>
                    </div>
                    <div class="dd-bank-transfer-info-row dd-bank-transfer-info-row--total">
                        <span class="dd-bank-transfer-info-label dd-bank-transfer-info-label--total">Tổng cộng</span>
                        <span class="dd-bank-transfer-info-value dd-bank-transfer-info-value--total">{{ number_format($order->total, 0, ',', '.') }}đ</span>
                    </div>
                </div>

                @if ($order->payment_status !== 'paid' && $order->status !== 'cancelled')
                    <div class="dd-bank-transfer-next-steps">
                        <h4><i class="fa fa-list-ol"></i> Các bước tiếp theo</h4>
                        <ol>
                            <li>Mở ứng dụng ngân hàng hoặc đến quầy giao dịch</li>
                            <li>Chuyển khoản đúng số tiền <strong>{{ number_format($order->total, 0, ',', '.') }}đ</strong></li>
                            <li>Nhập nội dung chuyển khoản: <strong>{{ $transferContent }}</strong></li>
                            <li>Hoàn tất giao dịch và chờ xác nhận từ chúng tôi</li>
                            <li>Đơn hàng sẽ được xử lý sau khi nhận được thanh toán (thường trong vòng 1-2 giờ)</li>
                        </ol>
                    </div>
                @endif

                <div class="dd-bank-transfer-actions">
                    <a href="{{ $currentCustomer ? url('/account/orders') : url('/view-history-order/' . $order->code) }}" class="dd-bank-transfer-action dd-bank-transfer-action--primary">
                        <i class="fa fa-history"></i> Xem lịch sử đơn hàng
                    </a>
                    <a href="{{ url('/') }}" class="dd-bank-transfer-action dd-bank-transfer-action--secondary">
                        <i class="fa fa-shopping-cart"></i> Tiếp tục mua sắm
                    </a>
                </div>
            </div>

            @if ($order->payment_status !== 'paid' && $order->status !== 'cancelled')
                <div class="dd-bank-transfer-card dd-bank-transfer-note-card">
                    <p class="dd-bank-transfer-note">
                        <i class="fa fa-info-circle"></i>
                        Lưu ý: Vui lòng chuyển khoản đúng nội dung để đơn hàng được xác nhận nhanh nhất.
                        Nếu cần hỗ trợ, vui lòng liên hệ hotline: {{ config('services.storefront_support.phone_display', '1900.0000') }}
                    </p>
                </div>
            @endif
        </div>
    </section>
@endsection
