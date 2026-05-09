@extends('admin_layout')

@section('admin_content')
<link rel="stylesheet" href="{{ asset('backend/css/admin-dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('backend/css/admin-pages.css') }}">

@php
    $statusLabels = [
        'pending' => ['label' => 'Chờ xử lý', 'class' => 'amber'],
        'approved' => ['label' => 'Đã chấp thuận', 'class' => 'green'],
        'rejected' => ['label' => 'Từ chối', 'class' => 'red'],
        'refunded' => ['label' => 'Đã hoàn tiền', 'class' => 'blue'],
    ];
@endphp

<div class="adm-page">
    <div class="adm-page-title">
        <h1><i class="fa fa-undo" style="color:var(--adm-blue);margin-right:6px"></i>Quản lý trả hàng / hoàn tiền</h1>
        <div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> / Đơn hàng / Trả hàng</div>
    </div>

    @if (session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="adm-stats">
        <div class="adm-stat"><div class="icon blue"><i class="fa fa-clipboard"></i></div><div><div class="num">{{ number_format($stats['total']) }}</div><div class="lbl">Tổng yêu cầu</div></div></div>
        <div class="adm-stat"><div class="icon amber"><i class="fa fa-clock-o"></i></div><div><div class="num">{{ number_format($stats['pending']) }}</div><div class="lbl">Chờ xử lý</div></div></div>
        <div class="adm-stat"><div class="icon green"><i class="fa fa-check-circle"></i></div><div><div class="num">{{ number_format($stats['approved']) }}</div><div class="lbl">Đã chấp thuận</div></div></div>
        <div class="adm-stat"><div class="icon red"><i class="fa fa-ban"></i></div><div><div class="num">{{ number_format($stats['rejected']) }}</div><div class="lbl">Từ chối</div></div></div>
        <div class="adm-stat"><div class="icon teal"><i class="fa fa-money"></i></div><div><div class="num">{{ number_format($stats['refunded']) }}</div><div class="lbl">Đã hoàn tiền</div></div></div>
    </div>

    <form method="GET" action="{{ route('admin.returns') }}" class="adm-toolbar" style="align-items:flex-end;flex-wrap:wrap">
        <div class="adm-search" style="min-width:300px">
            <i class="fa fa-search"></i>
            <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Tìm mã trả hàng, đơn hàng, khách, lý do...">
        </div>
        <div>
            <label class="adm-filter-label" for="return-status">Trạng thái</label>
            <select id="return-status" name="status" class="adm-select">
                <option value="all" @selected($filters['status'] === 'all')>Tất cả</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ $statusLabels[$status]['label'] ?? $status }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="adm-filter-label" for="return-date-from">Từ ngày</label>
            <input id="return-date-from" type="date" name="date_from" value="{{ $filters['date_from'] }}" class="adm-select">
        </div>
        <div>
            <label class="adm-filter-label" for="return-date-to">Đến ngày</label>
            <input id="return-date-to" type="date" name="date_to" value="{{ $filters['date_to'] }}" class="adm-select">
        </div>
        <div>
            <label class="adm-filter-label" for="return-per-page">Mỗi trang</label>
            <select id="return-per-page" name="per_page" class="adm-select">
                @foreach ([15, 25, 50] as $size)
                    <option value="{{ $size }}" @selected((int) $filters['per_page'] === $size)>{{ $size }}</option>
                @endforeach
            </select>
        </div>
        <button class="adm-btn primary" type="submit"><i class="fa fa-filter"></i> Lọc</button>
        <a class="adm-btn outline" href="{{ route('admin.returns') }}"><i class="fa fa-refresh"></i> Xóa lọc</a>
    </form>

    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Mã trả hàng</th>
                    <th>Đơn hàng</th>
                    <th>Khách hàng</th>
                    <th>Sản phẩm</th>
                    <th>Lý do</th>
                    <th>SL</th>
                    <th>Hoàn tiền</th>
                    <th>Trạng thái</th>
                    <th>Ngày yêu cầu</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($returns as $return)
                    @php
                        $status = $statusLabels[$return->status] ?? ['label' => $return->status, 'class' => 'gray'];
                        $customerName = $return->customer?->name ?? 'Khách đã xóa';
                    @endphp
                    <tr>
                        <td class="id">{{ $return->code }}</td>
                        <td>
                            @if ($return->order)
                                <a href="{{ url('/view-order/' . $return->order->code) }}" class="dd-link">{{ $return->order->code }}</a>
                                <small style="display:block;color:var(--adm-muted)">Tổng: {{ number_format((float) $return->order->total, 0, ',', '.') }}đ</small>
                            @else
                                <span style="color:var(--adm-muted)">Đơn đã xóa</span>
                            @endif
                        </td>
                        <td>
                            <div class="adm-user-cell">
                                <div class="u-avatar">{{ mb_substr($customerName, 0, 1) }}</div>
                                <span class="u-name">{{ $customerName }}</span>
                            </div>
                            @if ($return->customer?->email)
                                <small style="display:block;color:var(--adm-muted);margin-top:4px">{{ $return->customer->email }}</small>
                            @endif
                        </td>
                        <td style="min-width:200px">
                            <strong>{{ $return->orderItem?->product_name ?? 'Toàn bộ đơn / chưa chỉ định' }}</strong>
                            @if ($return->orderItem?->sku)
                                <small style="display:block;color:var(--adm-muted)">SKU: {{ $return->orderItem->sku }}</small>
                            @endif
                        </td>
                        <td style="min-width:240px;max-width:360px;color:var(--adm-muted)">{{ \Illuminate\Support\Str::limit((string) ($return->reason ?? ''), 160) }}</td>
                        <td>{{ number_format($return->quantity) }}</td>
                        <td>
                            <strong>{{ number_format((float) $return->refund_amount, 0, ',', '.') }}đ</strong>
                            @if ($return->refund_method)
                                <small style="display:block;color:var(--adm-muted)">{{ $return->refund_method }}</small>
                            @endif
                            @if ($return->refunded_at)
                                <small style="display:block;color:var(--adm-muted)">Hoàn: {{ $return->refunded_at->format('d/m/Y H:i') }}</small>
                            @endif
                        </td>
                        <td><span class="adm-badge {{ $status['class'] }}">{{ $status['label'] }}</span></td>
                        <td style="white-space:nowrap">{{ $return->created_at?->format('d/m/Y H:i') }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.returns.status', array_merge(['orderReturn' => $return], request()->query())) }}" style="display:flex;gap:6px;align-items:center">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="adm-select" style="height:32px;width:130px">
                                    @foreach ($statuses as $nextStatus)
                                        <option value="{{ $nextStatus }}" @selected($return->status === $nextStatus)>{{ $statusLabels[$nextStatus]['label'] ?? $nextStatus }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="adm-btn outline" style="height:32px;padding:0 10px"><i class="fa fa-check"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align:center;padding:36px;color:var(--adm-muted)">
                            <i class="fa fa-undo" style="font-size:28px;display:block;margin-bottom:8px"></i>
                            Chưa có yêu cầu trả hàng phù hợp với bộ lọc hiện tại.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="adm-pagination">
            <span class="info">
                Hiển thị {{ $returns->firstItem() ?? 0 }} đến {{ $returns->lastItem() ?? 0 }} của {{ number_format($returns->total()) }} yêu cầu
            </span>
            <div class="pages">{{ $returns->links() }}</div>
        </div>
    </div>
</div>
@endsection
