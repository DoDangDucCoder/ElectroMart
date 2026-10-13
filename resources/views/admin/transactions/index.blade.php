@extends('admin_layout')

@section('admin_content')

@php
    $statusLabels = [
        'pending' => ['label' => 'Chờ thanh toán', 'class' => 'amber'],
        'processing' => ['label' => 'Đang xử lý', 'class' => 'blue'],
        'succeeded' => ['label' => 'Thành công', 'class' => 'green'],
        'failed' => ['label' => 'Thất bại', 'class' => 'red'],
        'refunded' => ['label' => 'Đã hoàn tiền', 'class' => 'purple'],
    ];

    $methodLabels = [
        'cod' => 'COD',
        'cash' => 'Tiền mặt',
        'bank_transfer' => 'Chuyển khoản',
        'card' => 'Thẻ',
        'momo' => 'MoMo',
        'vnpay' => 'VNPay',
        'paypal' => 'PayPal',
    ];

    $formatMoney = static fn ($amount, $currency = 'VND') => number_format((float) $amount, 0, ',', '.') . ($currency === 'VND' ? 'đ' : ' ' . $currency);
@endphp

<div class="adm-page">
    <div class="adm-page-title">
        <h1><i class="fa fa-credit-card" style="color:var(--adm-blue);margin-right:6px"></i>Thanh toán / giao dịch</h1>
        <div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> / Đơn hàng / Thanh toán</div>
    </div>

    <div class="adm-stats">
        <div class="adm-stat" data-filter-field="status" data-filter-value="all"><div class="icon blue"><i class="fa fa-credit-card"></i></div><div><div class="num">{{ number_format($stats['total']) }}</div><div class="lbl">Tổng thanh toán</div></div></div>
        <div class="adm-stat" data-filter-field="status" data-filter-value="pending"><div class="icon amber"><i class="fa fa-clock-o"></i></div><div><div class="num">{{ number_format($stats['pending']) }}</div><div class="lbl">Chờ thanh toán</div></div></div>
        <div class="adm-stat" data-filter-field="status" data-filter-value="processing"><div class="icon teal"><i class="fa fa-spinner"></i></div><div><div class="num">{{ number_format($stats['processing']) }}</div><div class="lbl">Đang xử lý</div></div></div>
        <div class="adm-stat" data-filter-field="status" data-filter-value="succeeded"><div class="icon green"><i class="fa fa-check-circle"></i></div><div><div class="num">{{ number_format($stats['succeeded']) }}</div><div class="lbl">Thành công</div></div></div>
        <div class="adm-stat" data-filter-field="status" data-filter-value="failed"><div class="icon red"><i class="fa fa-exclamation-circle"></i></div><div><div class="num">{{ number_format($stats['failed_or_refunded']) }}</div><div class="lbl">Lỗi / hoàn tiền</div></div></div>
        <div class="adm-stat"><div class="icon purple"><i class="fa fa-money"></i></div><div><div class="num" style="font-size:18px">{{ $formatMoney($stats['total_amount']) }}</div><div class="lbl">Tổng giá trị</div></div></div>
    </div>

    <form method="GET" action="{{ route('admin.transactions') }}" class="adm-toolbar" style="align-items:flex-end;flex-wrap:wrap">
        <div class="adm-search">
            <i class="fa fa-search"></i>
            <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Tìm mã giao dịch, mã đơn, khách hàng...">
        </div>
        <div>
            <label class="adm-filter-label" for="payment-method">Phương thức</label>
            <select id="payment-method" name="method" class="adm-select">
                <option value="all" @selected($filters['method'] === 'all')>Tất cả</option>
                @foreach ($methods as $method)
                    <option value="{{ $method }}" @selected($filters['method'] === $method)>{{ $methodLabels[$method] ?? 'Không rõ' }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="adm-filter-label" for="payment-status">Trạng thái</label>
            <select id="payment-status" name="status" class="adm-select">
                <option value="all" @selected($filters['status'] === 'all')>Tất cả</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ $statusLabels[$status]['label'] ?? 'Không rõ' }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="adm-filter-label" for="payment-date-from">Từ ngày</label>
            <input id="payment-date-from" type="date" name="date_from" value="{{ $filters['date_from'] }}" class="adm-select">
        </div>
        <div>
            <label class="adm-filter-label" for="payment-date-to">Đến ngày</label>
            <input id="payment-date-to" type="date" name="date_to" value="{{ $filters['date_to'] }}" class="adm-select">
        </div>

        <input type="hidden" name="sort" value="{{ $filters['sort'] ?? 'newest' }}">

        <button class="adm-btn primary" type="submit"><i class="fa fa-filter"></i> Lọc</button>
        <a class="adm-btn outline" href="{{ route('admin.transactions.export', request()->except('page')) }}"><i class="fa fa-download"></i> Xuất CSV</a>
        <a class="adm-btn outline" href="{{ route('admin.transactions') }}"><i class="fa fa-refresh"></i> Xóa lọc</a>
    </form>

    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>@include('admin.partials.sort_header', ['label' => 'Mã giao dịch', 'asc' => 'oldest', 'desc' => 'newest', 'filters' => $filters])</th>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Phương thức</th>
                    <th>Trạng thái</th>
                    <th>@include('admin.partials.sort_header', ['label' => 'Số tiền', 'asc' => 'amount_asc', 'desc' => 'amount_desc', 'filters' => $filters])</th>
                    <th>@include('admin.partials.sort_header', ['label' => 'Ngày tạo', 'asc' => 'date_asc', 'desc' => 'date_desc', 'filters' => $filters])</th>
                    <th>Ghi nhận thanh toán</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    @php
                        $status = $statusLabels[$payment->status] ?? ['label' => 'Không rõ', 'class' => 'gray'];
                        $method = $methodLabels[$payment->provider] ?? 'Không rõ';
                        $customer = $payment->order?->customer;
                        $customerName = $customer?->name ?? 'Khách đã xóa';
                        $paymentRecordedLabel = $payment->paid_at
                            ? $payment->paid_at->format('d/m/Y H:i')
                            : $status['label'];
                    @endphp
                    <tr>
                        <td>
                            <strong class="id">GD{{ str_pad((string) $payment->id, 4, '0', STR_PAD_LEFT) }}</strong>
                            @if ($payment->reference)
                                <small style="display:block;color:var(--adm-muted);margin-top:4px">Ref: {{ $payment->reference }}</small>
                            @endif
                        </td>
                        <td>
                            @if ($payment->order)
                                <a href="{{ url('/view-order/' . $payment->order->code) }}" class="dd-link">{{ $payment->order->code }}</a>
                            @else
                                <span style="color:var(--adm-muted)">Đơn đã xóa</span>
                            @endif
                        </td>
                        <td style="text-align:center;vertical-align:middle">
                            <strong>{{ $customerName }}</strong>
                            @if ($customer?->email)
                                <small style="display:block;color:var(--adm-muted);margin-top:4px">{{ $customer->email }}</small>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $method }}</strong>
                        </td>
                        <td><span class="adm-badge {{ $status['class'] }}">{{ $status['label'] }}</span></td>
                        <td><strong>{{ $formatMoney($payment->amount, $payment->currency) }}</strong></td>
                        <td style="white-space:nowrap">{{ $payment->created_at?->format('d/m/Y H:i') }}</td>
                        <td style="white-space:nowrap">
                            <span style="color:{{ $payment->paid_at ? 'inherit' : 'var(--adm-muted)' }}">{{ $paymentRecordedLabel }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:36px;color:var(--adm-muted)">
                            <i class="fa fa-credit-card" style="font-size:28px;display:block;margin-bottom:8px"></i>
                            Chưa có thanh toán phù hợp với bộ lọc hiện tại.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @include('admin.partials.pagination', ['paginator' => $payments, 'label' => 'thanh toán'])
    </div>
</div>
@endsection
