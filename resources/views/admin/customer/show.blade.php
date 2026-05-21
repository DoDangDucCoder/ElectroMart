@extends('admin_layout')

@section('admin_content')
    @php
        $formatMoney = static fn ($value): string => number_format((float) $value, 0, ',', '.') . 'đ';
        $statusLabels = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'processing' => 'Đang xử lý',
            'shipped' => 'Đang giao',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            'refunded' => 'Đã hoàn tiền',
        ];
        $statusClasses = [
            'pending' => 'warning',
            'confirmed' => 'info',
            'processing' => 'primary',
            'shipped' => 'info',
            'completed' => 'success',
            'cancelled' => 'default',
            'refunded' => 'default',
        ];
    @endphp

    <div class="g-admin-page-shell">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Khách hàng / Hồ sơ</span>
                <h1>{{ $customer->name }}</h1>
                <p>Hồ sơ khách hàng lấy từ customers và các bảng liên quan. Trường mật khẩu, token và reset metadata không được hiển thị.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($orderStats['orders_count']) }}</strong>
                    <span>Đơn hàng</span>
                </article>
                <article>
                    <strong>{{ $formatMoney($orderStats['total_spending']) }}</strong>
                    <span>Tổng chi tiêu</span>
                </article>
                <article>
                    <strong>{{ $customer->is_active ? 'Active' : 'Inactive' }}</strong>
                    <span>Trạng thái</span>
                </article>
            </div>
        </section>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <p style="display:flex;gap:10px;align-items:center;">
            <a href="{{ route('admin.customers') }}" class="btn btn-default">
                <i class="fa fa-arrow-left"></i> Quay lại danh sách
            </a>
            {{-- Fix #10: Nút Khóa / Mở khóa tài khoản khách hàng --}}
            <form method="POST" action="{{ route('admin.customers.toggle-status', $customer) }}"
                  onsubmit="return confirm('{{ $customer->is_active ? 'Xác nhận khóa tài khoản ' . $customer->name . '?' : 'Xác nhận mở khóa tài khoản ' . $customer->name . '?' }}')">
                @csrf
                <button type="submit" class="btn {{ $customer->is_active ? 'btn-danger' : 'btn-success' }}">
                    <i class="fa {{ $customer->is_active ? 'fa-lock' : 'fa-unlock' }}"></i>
                    {{ $customer->is_active ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}
                </button>
            </form>
        </p>

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Thông tin hồ sơ</strong>
                    <p>Thông tin định danh cơ bản, không bao gồm dữ liệu xác thực nhạy cảm.</p>
                </div>
                <span class="g-admin-status-chip">#{{ $customer->id }}</span>
            </header>
            <div class="g-admin-panel-body">
                <div class="row" style="row-gap:12px;">
                    <div class="col-md-4"><strong>Họ tên</strong><br>{{ $customer->name }}</div>
                    <div class="col-md-4"><strong>Email</strong><br><a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a></div>
                    <div class="col-md-4"><strong>Số điện thoại</strong><br>{{ $customer->phone ?: 'Chưa cập nhật' }}</div>
                    <div class="col-md-4"><strong>Ngày tạo</strong><br>{{ $customer->created_at ? $customer->created_at->format('d/m/Y H:i') : 'Không rõ' }}</div>
                    <div class="col-md-4"><strong>Email xác thực</strong><br>{{ $customer->email_verified_at ? $customer->email_verified_at->format('d/m/Y H:i') : 'Chưa xác thực' }}</div>
                    <div class="col-md-4">
                        <strong>Trạng thái</strong><br>
                        <span class="label label-{{ $customer->is_active ? 'success' : 'default' }}">
                            {{ $customer->is_active ? 'Đang hoạt động' : 'Tạm khóa' }}
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <section class="g-admin-panel-card" style="margin-top:18px;">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Đơn hàng gần đây</strong>
                    <p>Hiển thị tối đa 10 đơn hàng mới nhất từ bảng orders.</p>
                </div>
                <span class="g-admin-status-chip">{{ number_format($customer->orders->count()) }} đơn hiển thị</span>
            </header>
            <div class="g-admin-panel-body">
                @if ($customer->orders->isEmpty())
                    <div class="g-admin-empty-state">
                        <h3>Khách hàng chưa có đơn hàng</h3>
                        <p>Khi có đơn hàng, lịch sử mua hàng sẽ xuất hiện tại đây.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Ngày đặt</th>
                                    <th>Sản phẩm</th>
                                    <th>Thanh toán</th>
                                    <th>Trạng thái</th>
                                    <th>Tổng tiền</th>
                                    <th>Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customer->orders as $order)
                                    @php $status = (string) $order->status; @endphp
                                    <tr>
                                        <td>{{ $order->code }}</td>
                                        <td>{{ ($order->placed_at ?: $order->created_at)?->format('d/m/Y H:i') ?: 'Không rõ' }}</td>
                                        <td>{{ number_format((int) $order->items_count) }}</td>
                                        <td>{{ $order->payment_status }}</td>
                                        <td>
                                            <span class="label label-{{ $statusClasses[$status] ?? 'default' }}">
                                                {{ $statusLabels[$status] ?? $status }}
                                            </span>
                                        </td>
                                        <td><strong>{{ $formatMoney($order->total) }}</strong></td>
                                        <td><a href="{{ url('/view-order/' . $order->code) }}" class="btn btn-default btn-xs">Xem đơn</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </section>

        <div class="row" style="margin-top:18px;">
            <div class="col-md-6">
                <section class="g-admin-panel-card">
                    <header class="g-admin-panel-head">
                        <div>
                            <strong>Địa chỉ</strong>
                            <p>Dữ liệu từ customer_addresses.</p>
                        </div>
                    </header>
                    <div class="g-admin-panel-body">
                        @forelse ($customer->addresses as $address)
                            <div style="padding:12px;border:1px solid #e5e7eb;border-radius:6px;margin-bottom:10px;">
                                <strong>{{ $address->label ?: 'Địa chỉ' }}</strong>
                                @if ($address->is_default)
                                    <span class="label label-success" style="margin-left:6px;">Mặc định</span>
                                @endif
                                <div style="margin-top:6px;">
                                    {{ $address->recipient_name }}{{ $address->recipient_phone ? ' - ' . $address->recipient_phone : '' }}<br>
                                    {{ $address->address_line1 }}
                                    @if ($address->address_line2)
                                        <br>{{ $address->address_line2 }}
                                    @endif
                                    <br>{{ $address->country ?: 'Vietnam' }}
                                </div>
                            </div>
                        @empty
                            <div class="g-admin-empty-state">
                                <h3>Chưa có địa chỉ</h3>
                                <p>Khách hàng chưa lưu địa chỉ giao hàng.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
            <div class="col-md-6">
                <section class="g-admin-panel-card">
                    <header class="g-admin-panel-head">
                        <div>
                            <strong>Phương thức thanh toán</strong>
                            <p>Dữ liệu từ customer_payment_methods, hiển thị dạng đã che.</p>
                        </div>
                    </header>
                    <div class="g-admin-panel-body">
                        @forelse ($customer->paymentMethods as $method)
                            <div style="padding:12px;border:1px solid #e5e7eb;border-radius:6px;margin-bottom:10px;">
                                <strong>{{ $method->providerLabel() }}</strong>
                                @if ($method->is_default)
                                    <span class="label label-success" style="margin-left:6px;">Mặc định</span>
                                @endif
                                <div style="margin-top:6px;">
                                    {{ $method->maskedNumber() }}
                                    @if ($method->expiryLabel())
                                        <br>Hết hạn: {{ $method->expiryLabel() }}
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="g-admin-empty-state">
                                <h3>Chưa có phương thức thanh toán</h3>
                                <p>Đây là trạng thái rỗng hợp lệ nếu khách hàng chưa lưu phương thức thanh toán.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>

        <div class="row" style="margin-top:18px;">
            <div class="col-md-6">
                <section class="g-admin-panel-card">
                    <header class="g-admin-panel-head">
                        <div>
                            <strong>Hóa đơn VAT</strong>
                            <p>Dữ liệu từ vat_invoices.</p>
                        </div>
                    </header>
                    <div class="g-admin-panel-body">
                        @forelse ($customer->vatInvoices as $invoice)
                            <div style="padding:12px;border:1px solid #e5e7eb;border-radius:6px;margin-bottom:10px;">
                                <strong>{{ $invoice->code }}</strong>
                                <span class="label label-default" style="margin-left:6px;">{{ $invoice->statusLabel() }}</span>
                                <div style="margin-top:6px;">
                                    {{ $invoice->company_name ?: 'Cá nhân' }}<br>
                                    Giá trị: {{ $formatMoney($invoice->value) }}
                                </div>
                            </div>
                        @empty
                            <div class="g-admin-empty-state">
                                <h3>Chưa có hóa đơn VAT</h3>
                                <p>Không có yêu cầu xuất hóa đơn cho khách hàng này.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
            <div class="col-md-6">
                <section class="g-admin-panel-card">
                    <header class="g-admin-panel-head">
                        <div>
                            <strong>Yêu cầu đổi trả</strong>
                            <p>Dữ liệu từ order_returns.</p>
                        </div>
                    </header>
                    <div class="g-admin-panel-body">
                        @forelse ($customer->orderReturns as $return)
                            <div style="padding:12px;border:1px solid #e5e7eb;border-radius:6px;margin-bottom:10px;">
                                <strong>{{ $return->code }}</strong>
                                <span class="label label-{{ $return->statusClass() }}" style="margin-left:6px;">{{ $return->statusLabel() }}</span>
                                <div style="margin-top:6px;">
                                    {{ \Illuminate\Support\Str::limit((string) ($return->reason ?? ''), 120) }}<br>
                                    Hoàn tiền: {{ $formatMoney($return->refund_amount) }}
                                </div>
                            </div>
                        @empty
                            <div class="g-admin-empty-state">
                                <h3>Chưa có yêu cầu đổi trả</h3>
                                <p>Không có hồ sơ đổi trả hoặc hoàn tiền cho khách hàng này.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>

        <section class="g-admin-panel-card" style="margin-top:18px;">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Metadata an toàn</strong>
                    <p>Các khóa nhạy cảm liên quan mật khẩu/token đã được loại bỏ trước khi hiển thị.</p>
                </div>
            </header>
            <div class="g-admin-panel-body">
                @if (empty($safeMeta))
                    <div class="g-admin-empty-state">
                        <h3>Không có metadata công khai</h3>
                        <p>Không có dữ liệu meta phù hợp để hiển thị.</p>
                    </div>
                @else
                    <pre style="white-space:pre-wrap;">{{ json_encode($safeMeta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @endif
            </div>
        </section>
    </div>
@endsection
