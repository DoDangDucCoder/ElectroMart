@extends('admin_layout')

@section('admin_content')
@php
    $statusLabels = [
        'all' => 'Tất cả',
        'active' => 'Đang hoạt động',
        'inactive' => 'Tạm khóa',
    ];

    $formatMoney = static fn ($value): string => number_format((float) $value, 0, ',', '.') . 'đ';
@endphp

@once
    <style>
        .adm-customer-management{padding:24px 34px 30px;background:#f4f7fb;min-height:calc(100vh - 72px)}
        .adm-customer-management .adm-page-title{display:flex;gap:16px;align-items:center;margin:4px 0 24px}
        .adm-customer-management .adm-title-icon{width:58px;height:58px;border-radius:12px;background:#fff;border:1px solid var(--adm-border);box-shadow:0 6px 16px rgba(15,23,42,.08);display:grid;place-items:center;color:var(--adm-primary);font-size:25px}
        .adm-customer-management .adm-page-title h1{margin:0;font-size:28px;font-weight:800;color:var(--adm-text)}
        .adm-customer-management .adm-page-title .crumb{margin-top:6px;color:var(--adm-muted);font-size:14px}
        .adm-customer-management .adm-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:18px;margin-bottom:20px}
        .adm-customer-management .adm-stat{height:96px;background:#fff;border:1px solid var(--adm-border);border-radius:10px;box-shadow:0 6px 16px rgba(15,23,42,.06);display:flex;flex-direction:row;align-items:center;justify-content:flex-start;gap:18px;padding:0 24px}
        .adm-customer-management .adm-stat .icon{width:58px;height:58px;border-radius:16px;display:grid;place-items:center;font-size:24px}
        .adm-customer-management .adm-stat .num{font-size:28px;font-weight:800;line-height:1;color:var(--adm-text)}
        .adm-customer-management .adm-stat .lbl{margin-top:6px;color:var(--adm-muted);font-size:14px}
        .adm-customer-management .adm-toolbar{background:#fff;border:1px solid var(--adm-border);border-radius:10px;box-shadow:0 6px 16px rgba(15,23,42,.05);padding:18px;margin-bottom:18px}
        .adm-customer-management .adm-toolbar-grid{display:grid;grid-template-columns:1.25fr 1fr .9fr .9fr .7fr;gap:18px;align-items:end}
        .adm-customer-management .adm-filter-actions{display:flex;gap:10px;margin-top:12px}
        .adm-customer-management .adm-table-wrap{background:#fff;border:1px solid var(--adm-border);border-radius:10px;box-shadow:0 6px 16px rgba(15,23,42,.05);overflow:hidden}
        .adm-customer-management .adm-table-head{display:flex;justify-content:space-between;align-items:center;padding:18px 20px;border-bottom:1px solid var(--adm-border)}
        .adm-customer-management .adm-table-head h2{margin:0;font-size:18px;font-weight:800;color:var(--adm-text)}
        .adm-customer-management .adm-table-actions{display:flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:flex-end}
        .adm-customer-management .adm-table{width:100%;table-layout:fixed;border-collapse:collapse}
        .adm-customer-management .adm-table th{height:48px;background:#f8fafc;color:var(--adm-muted);font-size:12px;text-transform:uppercase;letter-spacing:.03em;text-align:center;border-bottom:1px solid var(--adm-border)}
        .adm-customer-management .adm-table td{height:62px;padding:10px 12px;border-bottom:1px solid #edf2f7;text-align:center;vertical-align:middle;color:var(--adm-text)}
        .adm-customer-management .adm-table th:nth-child(1){width:72px}
        .adm-customer-management .adm-table th:nth-child(2){width:190px}
        .adm-customer-management .adm-table th:nth-child(3){width:210px}
        .adm-customer-management .adm-table th:nth-child(4){width:130px}
        .adm-customer-management .adm-table th:nth-child(5){width:145px}
        .adm-customer-management .adm-table th:nth-child(6){width:92px}
        .adm-customer-management .adm-table th:nth-child(7){width:140px}
        .adm-customer-management .adm-table th:nth-child(8){width:130px}
        .adm-customer-management .adm-table th:nth-child(9){width:90px}
        .adm-customer-management .adm-table td:nth-child(3){overflow-wrap:anywhere}
        .adm-customer-management .adm-table td:nth-child(4),
        .adm-customer-management .adm-table td:nth-child(5),
        .adm-customer-management .adm-table td:nth-child(7){white-space:nowrap}
        .adm-customer-management .adm-user-cell{display:flex;align-items:center;justify-content:center;gap:10px;text-align:center}
        .adm-customer-management .u-avatar{width:34px;height:34px;border-radius:50%;background:#e8efff;color:var(--adm-primary);display:grid;place-items:center;font-weight:800;font-size:12px;flex:none}
        .adm-customer-management .u-name{display:block;font-weight:800;color:var(--adm-text)}
        .adm-customer-management .u-note{display:block;margin-top:3px;font-size:11px;color:var(--adm-muted)}
        .adm-customer-management .adm-id{font-weight:800;color:var(--adm-primary)}
        .adm-customer-management .adm-status{display:inline-flex;align-items:center;justify-content:center;min-width:92px;height:26px;border-radius:999px;font-size:12px;font-weight:700}
        .adm-customer-management .adm-status.green{background:#dcfce7;color:#16a34a}
        .adm-customer-management .adm-status.gray{background:#edf2f7;color:#64748b}
        .adm-customer-management .adm-btn-xs{display:inline-flex;align-items:center;justify-content:center;height:32px;padding:0 14px;border-radius:6px;background:var(--adm-primary);color:#fff;font-weight:700;border:0}
        @media (max-width:1200px){.adm-customer-management .adm-stats{grid-template-columns:repeat(2,1fr)}.adm-customer-management .adm-toolbar-grid{grid-template-columns:repeat(2,1fr)}}
    </style>
@endonce

<div class="adm-page adm-customer-management">
    <div class="adm-page-title">
        <div class="adm-title-icon"><i class="fa fa-users"></i></div>
        <div>
            <h1>Quản lý khách hàng</h1>
            <div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> / Khách hàng / CRM</div>
        </div>
    </div>

    <div class="adm-stats">
        <div class="adm-stat" data-filter-field="status" data-filter-value="all"><div class="icon blue"><i class="fa fa-users"></i></div><div><div class="num">{{ number_format($stats['total']) }}</div><div class="lbl">Tổng khách hàng</div></div></div>
        <div class="adm-stat" data-filter-field="status" data-filter-value="active"><div class="icon green"><i class="fa fa-check-circle"></i></div><div><div class="num">{{ number_format($stats['active']) }}</div><div class="lbl">Đang hoạt động</div></div></div>
        <div class="adm-stat"><div class="icon blue"><i class="fa fa-user-plus"></i></div><div><div class="num">{{ number_format($stats['new_this_month']) }}</div><div class="lbl">Mới tháng này</div></div></div>
        <div class="adm-stat"><div class="icon amber"><i class="fa fa-refresh"></i></div><div><div class="num">{{ number_format($stats['returning'] ?? 0) }}</div><div class="lbl">Khách quay lại</div></div></div>
    </div>

    <form method="get" action="{{ route('admin.customers') }}" class="adm-toolbar">
        <div class="adm-toolbar-grid">
            <div>
                <label class="adm-filter-label" for="q">Từ khóa</label>
                <div class="adm-search">
                    <i class="fa fa-search"></i>
                    <input type="search" name="q" id="q" value="{{ $filters['q'] }}" placeholder="Tìm theo tên, email, SĐT...">
                </div>
            </div>
            <div>
                <label class="adm-filter-label" for="status">Trạng thái</label>
                <select name="status" id="status" class="adm-select">
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="adm-filter-label" for="date_from">Từ ngày</label>
                <input type="date" name="date_from" id="date_from" value="{{ $filters['date_from'] }}" class="adm-select">
            </div>
            <div>
                <label class="adm-filter-label" for="date_to">Đến ngày</label>
                <input type="date" name="date_to" id="date_to" value="{{ $filters['date_to'] }}" class="adm-select">
            </div>


        </div>
        <div class="adm-filter-actions">
            <button type="submit" class="adm-btn primary"><i class="fa fa-filter"></i> Lọc khách hàng</button>
            <a href="{{ route('admin.customers') }}" class="adm-btn outline"><i class="fa fa-refresh"></i> Xóa lọc</a>
        </div>
    </form>

    <div class="adm-table-wrap">
        <div class="adm-table-head">
            <h2>Danh sách khách hàng</h2>
            <div class="adm-table-actions">
                <a href="{{ route('admin.customers.export', request()->except('page')) }}" class="adm-btn outline"><i class="fa fa-download"></i> Xuất CSV</a>
                <span class="adm-badge blue">{{ number_format($customers->total()) }} kết quả</span>
            </div>
        </div>
        <table class="adm-table">
            <thead>
                <tr>
                    <th style="width:90px">Mã KH</th>
                    <th>@include('admin.partials.sort_header', ['label' => 'Tên khách hàng', 'asc' => 'name_asc', 'desc' => 'name_desc', 'filters' => $filters])</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>@include('admin.partials.sort_header', ['label' => 'Ngày tạo', 'asc' => 'oldest', 'desc' => 'newest', 'filters' => $filters])</th>
                    <th>@include('admin.partials.sort_header', ['label' => 'Số đơn', 'asc' => 'orders_asc', 'desc' => 'orders_desc', 'filters' => $filters])</th>
                    <th>@include('admin.partials.sort_header', ['label' => 'Tổng chi tiêu', 'asc' => 'spending_asc', 'desc' => 'spending_desc', 'filters' => $filters])</th>
                    <th>Trạng thái</th>
                    <th style="width:100px">Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    @php
                        $createdAt = $customer->created_at;
                        $initials = collect(explode(' ', trim($customer->name ?: 'KH')))->filter()->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('');
                    @endphp
                    <tr>
                        <td class="adm-id">KH{{ str_pad((string) $customer->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td style="text-align:center;vertical-align:middle">
                            <strong>{{ $customer->name }}</strong>
                        </td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->phone ?: 'Chưa cập nhật' }}</td>
                        <td>{{ $createdAt ? $createdAt->format('d/m/Y H:i') : 'Không rõ' }}</td>
                        <td>{{ number_format((int) $customer->orders_count) }}</td>
                        <td>{{ $formatMoney($customer->total_spending ?? 0) }}</td>
                        <td><span class="adm-status {{ $customer->is_active ? 'green' : 'gray' }}">{{ $customer->is_active ? 'Đang hoạt động' : 'Tạm khóa' }}</span></td>
                        <td><a href="{{ route('admin.customers.show', $customer) }}" class="adm-btn-xs">Xem</a></td>
                    </tr>
                @empty
                    <tr><td colspan="9" style="padding:36px;color:var(--adm-muted)">Chưa có khách hàng phù hợp.</td></tr>
                @endforelse
            </tbody>
        </table>
        @include('admin.partials.pagination', ['paginator' => $customers, 'label' => 'khách hàng'])
    </div>
</div>
@endsection
