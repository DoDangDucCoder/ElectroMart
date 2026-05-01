@extends('layout')

@php
    $isSuccess = request()->has('just_placed');
    $customer = $orderView['customer'];
    $shipping = $orderView['shipping'];
    $payment = $orderView['payment'];
    $totals = $orderView['totals'];
    $bankInfo = config('services.bank_transfer', []);
    $isBankTransfer = ($payment['provider_label'] === 'Chuyển khoản ngân hàng' || str_contains(strtolower($payment['provider_label']), 'bank'));
@endphp

@section('body_class', 'g-order-detail-redesign minimal-layout')

@section('content')
    <style>
        .g-order-detail-redesign {
            background-color: #f5f5f5;
            color: #333;
            font-family: 'Inter', sans-serif;
        }

        .order-success-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            gap: 40px;
        }

        .order-success-left {
            flex: 1;
            padding-bottom: 50px;
        }

        .order-success-right {
            width: 420px;
            background-color: #fafafa;
            border-left: 1px solid #e1e1e1;
            padding: 20px;
            min-height: 100vh;
        }

        .success-logo {
            margin-bottom: 40px;
        }

        .success-logo img {
            max-width: 200px;
        }

        .success-header {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .success-icon {
            width: 70px;
            height: 70px;
            border: 2px solid #338dbc;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #338dbc;
            font-size: 35px;
            flex-shrink: 0;
        }

        .success-title-area h1 {
            font-size: 20px;
            font-weight: 600;
            margin: 0 0 5px 0;
        }

        .success-title-area p {
            color: #717171;
            font-size: 15px;
            margin: 0;
        }

        .order-info-card {
            background: #fff;
            border: 1px solid #e1e1e1;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 25px;
        }

        .order-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .info-block h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .info-block p {
            font-size: 14px;
            line-height: 1.6;
            color: #555;
            margin-bottom: 5px;
        }

        .bank-transfer-instructions {
            background: #fffdf2;
            border: 1px solid #f9e2b0;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 25px;
        }

        .bank-grid {
            display: flex;
            gap: 30px;
            align-items: flex-start;
        }

        .qr-code {
            width: 180px;
            background: #fff;
            padding: 10px;
            border: 1px solid #e1e1e1;
            border-radius: 8px;
        }

        .qr-code img {
            width: 100%;
        }

        .bank-details {
            flex: 1;
        }

        .bank-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed #ddd;
            font-size: 14px;
        }

        .bank-row:last-child {
            border-bottom: none;
        }

        .bank-label {
            color: #717171;
        }

        .bank-value {
            font-weight: 600;
            color: #333;
        }

        .bank-value.highlight {
            color: #d32f2f;
            font-size: 16px;
        }

        .btn-continue-shopping {
            display: inline-block;
            background: #338dbc;
            color: #fff;
            padding: 15px 30px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
        }

        .btn-continue-shopping:hover {
            background: #2b78a0;
            color: #fff;
        }

        /* Sidebar items */
        .summary-item {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            align-items: center;
        }

        .summary-img {
            width: 60px;
            height: 60px;
            border: 1px solid #e1e1e1;
            border-radius: 8px;
            background: #fff;
            position: relative;
        }

        .summary-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .summary-qty {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #338dbc;
            color: #fff;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .summary-info {
            flex: 1;
        }

        .summary-name {
            font-size: 14px;
            font-weight: 500;
        }

        .summary-price {
            font-size: 14px;
            font-weight: 600;
        }

        .totals-area {
            border-top: 1px solid #e1e1e1;
            padding-top: 20px;
            margin-top: 20px;
        }

        .total-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
            color: #717171;
        }

        .total-line.grand-total {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e1e1e1;
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .shopw-order-detail-support-strip {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            margin: -10px 0 24px;
            font-size: 14px;
        }

        .shopw-order-detail-support-strip a {
            color: #338dbc;
            font-weight: 600;
            text-decoration: none;
        }

        .shopw-order-detail-support-strip a:hover {
            color: #2b78a0;
        }

        @media (max-width: 991px) {
            .order-success-container {
                flex-direction: column;
            }
            .order-success-right {
                width: 100%;
                border-left: none;
                min-height: auto;
            }
            .bank-grid {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>

    <div class="order-success-container">
        <div class="order-success-left">
            <div class="success-logo">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('uploads/brand/dangduc-logo-horizontal-width-600.png') }}" alt="Logo">
                </a>
            </div>

            <div class="success-header">
                <div class="success-icon">
                    <i class="fa fa-check"></i>
                </div>
                <div class="success-title-area">
                    <h1>Đặt hàng thành công</h1>
                    <p>Mã đơn hàng: #{{ $orderView['code'] }}</p>
                    <p>Cảm ơn bạn đã mua sắm tại DangDuc!</p>
                </div>
            </div>

            <div class="shopw-order-detail-support-strip">
                <a href="{{ url('/history') }}">Xem lịch sử đơn hàng</a>
                <a href="{{ url('/') }}">Tiếp tục mua hàng</a>
            </div>

            @if($isBankTransfer && $orderView['status'] === 'pending')
                <div class="bank-transfer-instructions">
                    <div class="why-title" style="margin-bottom: 20px;"><i class="fa fa-university"></i> Hướng dẫn thanh toán chuyển khoản</div>
                    <div class="bank-grid">
                        <div class="qr-code">
                            @php
                                $qrAmount = (int) $totals['total'];
                                $qrMemo = 'DH ' . $orderView['code'];
                                $qrBank = $bankInfo['bank_id'] ?? 'ICB';
                                $qrAccount = $bankInfo['account_number'] ?? '';
                                $qrUrl = "https://img.vietqr.io/image/{$qrBank}-{$qrAccount}-compact2.jpg?amount={$qrAmount}&addInfo={$qrMemo}&accountName=" . urlencode($bankInfo['account_holder'] ?? '');
                            @endphp
                            <img src="{{ $qrUrl }}" alt="QR Thanh toán">
                            <p style="text-align: center; font-size: 12px; margin-top: 10px; color: #717171;">Quét QR để thanh toán nhanh</p>
                        </div>
                        <div class="bank-details">
                            <div class="bank-row">
                                <span class="bank-label">Ngân hàng</span>
                                <span class="bank-value">{{ $bankInfo['bank_name'] ?? 'N/A' }}</span>
                            </div>
                            <div class="bank-row">
                                <span class="bank-label">Số tài khoản</span>
                                <span class="bank-value highlight">{{ $bankInfo['account_number'] ?? 'N/A' }}</span>
                            </div>
                            <div class="bank-row">
                                <span class="bank-label">Chủ tài khoản</span>
                                <span class="bank-value">{{ $bankInfo['account_holder'] ?? 'N/A' }}</span>
                            </div>
                            <div class="bank-row">
                                <span class="bank-label">Số tiền</span>
                                <span class="bank-value highlight">{{ number_format($totals['total'], 0, ',', '.') }}đ</span>
                            </div>
                            <div class="bank-row">
                                <span class="bank-label">Nội dung</span>
                                <span class="bank-value highlight">DH {{ $orderView['code'] }}</span>
                            </div>
                        </div>
                    </div>
                    <p style="margin-top: 20px; font-size: 13px; color: #856404; background: #fff3cd; padding: 10px; border-radius: 4px;">
                        <i class="fa fa-info-circle"></i> Vui lòng chuyển đúng số tiền và nội dung để hệ thống tự động xác nhận đơn hàng.
                    </p>
                </div>
            @endif

            <div class="order-info-card">
                <div class="order-info-grid">
                    <div class="info-block">
                        <h3>Thông tin giao hàng</h3>
                        <p><strong>{{ $shipping['name'] }}</strong></p>
                        <p>{{ $shipping['phone'] }}</p>
                        <p>{{ $shipping['address'] }}</p>
                    </div>
                    <div class="info-block">
                        <h3>Thanh toán & Vận chuyển</h3>
                        <p>Phương thức thanh toán: <strong>{{ $payment['provider_label'] }}</strong></p>
                        <p>Phương thức vận chuyển: <strong>Giao hàng tận nơi</strong></p>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 15px; align-items: center;">
                <a href="{{ url('/') }}" class="btn-continue-shopping">Tiếp tục mua hàng</a>
                <a href="javascript:window.print()" style="color: #338dbc; text-decoration: none; font-size: 14px;"><i class="fa fa-print"></i> In đơn hàng</a>
            </div>
        </div>

        <div class="order-success-right">
            <div class="order-summary-sidebar">
                <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 20px;">Đơn hàng ({{ count($orderView['items']) }} sản phẩm)</h2>

                <div class="summary-items-list">
                    @foreach ($orderView['items'] as $item)
                        @php
                            $image = !empty($item['image']) ? asset($item['image']) : asset('uploads/product/no-image.svg');
                        @endphp
                        <div class="summary-item">
                            <div class="summary-img">
                                <img src="{{ $image }}" alt="{{ $item['name'] }}">
                                <span class="summary-qty">{{ $item['quantity'] }}</span>
                            </div>
                            <div class="summary-info">
                                <div class="summary-name">{{ $item['name'] }}</div>
                                <div style="font-size: 12px; color: #999;">{{ $item['attributes'] }}</div>
                            </div>
                            <div class="summary-price">{{ number_format($item['subtotal'], 0, ',', '.') }}đ</div>
                        </div>
                    @endforeach
                </div>

                <div class="totals-area">
                    <div class="total-line">
                        <span>Tạm tính</span>
                        <span>{{ number_format($totals['subtotal'], 0, ',', '.') }}đ</span>
                    </div>
                    @if($totals['discount'] > 0)
                    <div class="total-line">
                        <span>Giảm giá</span>
                        <span>-{{ number_format($totals['discount'], 0, ',', '.') }}đ</span>
                    </div>
                    @endif
                    @if(!empty($orderView['coupon_code']))
                    <div class="total-line">
                        <span>Mã giảm giá</span>
                        <span>{{ $orderView['coupon_code'] }}</span>
                    </div>
                    @endif
                    <div class="total-line">
                        <span>Phí vận chuyển</span>
                        <span>{{ number_format($totals['shipping'], 0, ',', '.') }}đ</span>
                    </div>
                    <div class="total-line grand-total">
                        <span>Tổng cộng</span>
                        <span>{{ number_format($totals['total'], 0, ',', '.') }}đ</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

