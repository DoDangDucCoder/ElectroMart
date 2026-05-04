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

    <div class="g-admin-page-shell">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Khách hàng / CRM</span>
                <h1>Quản lý khách hàng</h1>
                <p>Dữ liệu lấy trực tiếp từ bảng customers, orders và các bảng hồ sơ khách hàng hiện đại.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($stats['total']) }}</strong>
                    <span>Tổng khách hàng</span>
                </article>
                <article>
                    <strong>{{ number_format($stats['active']) }}</strong>
                    <span>Đang hoạt động</span>
                </article>
                <article>
                    <strong>{{ number_format($stats['new_this_month']) }}</strong>
                    <span>Mới tháng này</span>
                </article>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Bộ lọc khách hàng</strong>
                    <p>Tìm theo tên, email, số điện thoại; lọc theo trạng thái và ngày tạo.</p>
                </div>
            </header>
            <div class="g-admin-panel-body">
                <form method="get" action="{{ route('admin.customers') }}" class="row" style="row-gap:12px;">
                    <div class="col-md-4">
                        <label for="q">Từ khóa</label>
                        <input type="search" name="q" id="q" value="{{ $filters['q'] }}" class="form-control" placeholder="Tên, email hoặc số điện thoại...">
                    </div>
                    <div class="col-md-2">
                        <label for="status">Trạng thái</label>
                        <select name="status" id="status" class="form-control">
                            @foreach ($statusLabels as $value => $label)
                                <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from">Từ ngày</label>
                        <input type="date" name="date_from" id="date_from" value="{{ $filters['date_from'] }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to">Đến ngày</label>
                        <input type="date" name="date_to" id="date_to" value="{{ $filters['date_to'] }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label for="per_page">Mỗi trang</label>
                        <select name="per_page" id="per_page" class="form-control">
                            @foreach ([10, 15, 25, 50] as $size)
                                <option value="{{ $size }}" @selected((int) $filters['per_page'] === $size)>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12" style="display:flex;gap:8px;align-items:center;">
                        <button type="submit" class="btn btn-info">
                            <i class="fa fa-search"></i> Lọc khách hàng
                        </button>
                        <a href="{{ route('admin.customers') }}" class="btn btn-default">Xóa lọc</a>
                    </div>
                </form>
            </div>
        </section>

        <section class="g-admin-panel-card" style="margin-top:18px;">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Danh sách khách hàng</strong>
                    <p>Mới nhất trước. Không hiển thị mật khẩu, token hoặc metadata nhạy cảm.</p>
                </div>
                <span class="g-admin-status-chip">{{ number_format($customers->total()) }} kết quả</span>
            </header>
            <div class="g-admin-panel-body">
                @if ($customers->isEmpty())
                    <div class="g-admin-empty-state">
                        <h3>Chưa có khách hàng phù hợp</h3>
                        <p>Thử thay đổi bộ lọc hoặc kiểm tra dữ liệu seed hiện tại.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width:90px;">Mã</th>
                                    <th>Khách hàng</th>
                                    <th>Email</th>
                                    <th>Số điện thoại</th>
                                    <th style="width:140px;">Ngày tạo</th>
                                    <th style="width:110px;">Đơn hàng</th>
                                    <th style="width:150px;">Tổng chi tiêu</th>
                                    <th style="width:120px;">Trạng thái</th>
                                    <th style="width:90px;">Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customers as $customer)
                                    @php
                                        $isActive = (bool) $customer->is_active;
                                        $createdAt = $customer->created_at;
                                    @endphp
                                    <tr>
                                        <td>#{{ $customer->id }}</td>
                                        <td>
                                            <strong>{{ $customer->name }}</strong>
                                            @if ($createdAt && $createdAt->greaterThanOrEqualTo(now()->subDays(30)))
                                                <span class="label label-info" style="margin-left:6px;">Mới</span>
                                            @endif
                                        </td>
                                        <td><a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a></td>
                                        <td>{{ $customer->phone ?: 'Chưa cập nhật' }}</td>
                                        <td>{{ $createdAt ? $createdAt->format('d/m/Y H:i') : 'Không rõ' }}</td>
                                        <td>{{ number_format((int) $customer->orders_count) }}</td>
                                        <td><strong>{{ $formatMoney($customer->total_spending ?? 0) }}</strong></td>
                                        <td>
                                            <span class="label label-{{ $isActive ? 'success' : 'default' }}">
                                                {{ $isActive ? 'Hoạt động' : 'Tạm khóa' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-primary btn-xs">
                                                Xem
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap;">
                        <span>
                            Hiển thị {{ number_format($customers->firstItem()) }}-{{ number_format($customers->lastItem()) }}
                            của {{ number_format($customers->total()) }} khách hàng
                        </span>
                        {{ $customers->links() }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
