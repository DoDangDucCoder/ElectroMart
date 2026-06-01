@extends('admin_layout')

@section('admin_content')
    @php
        $orderItems = method_exists($getorder, 'getCollection') ? $getorder->getCollection() : collect($getorder);
        $pendingOrders = $orderItems->where('order_status', 1)->count();
        $processingOrders = $orderItems->where('order_status', 2)->count();
        $shippingOrders = $orderItems->where('order_status', 3)->count();
        $completedOrders = $orderItems->where('order_status', 4)->count();
        $cancelledOrders = $orderItems->where('order_status', 5)->count();
        $statusLabels = [
            1 => ['Chờ xử lý', 'is-warning'],
            2 => ['Đang xử lý', 'is-info'],
            3 => ['Đang giao', 'is-info'],
            4 => ['Đã giao hàng', 'is-success'],
            5 => ['Đã hủy', 'is-danger'],
        ];
    @endphp

    <div class="g-admin-page-shell g-admin-order-list">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Đơn hàng</span>
                <h1>Quản lý đơn hàng</h1>
                <p>
                    Theo dõi trạng thái xử lý, kiểm tra lý do hủy và đi vào chi tiết từng đơn để cập nhật số lượng
                    hoặc xác nhận giao hàng.
                </p>
            </div>

            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($getorder->total()) }}</strong>
                    <span>Tổng đơn đang quản lý</span>
                </article>
                <article>
                    <strong>{{ number_format($pendingOrders) }}</strong>
                    <span>Chờ xử lý trên trang này</span>
                </article>
            </div>
        </section>

        <section class="g-admin-kpi-grid">
            <article class="g-admin-kpi-card">
                <span>Chờ xử lý</span>
                <strong>{{ number_format($pendingOrders) }}</strong>
                <p>Cần kiểm tra tồn kho và xác nhận xử lý.</p>
            </article>
            <article class="g-admin-kpi-card">
                <span>Đang xử lý</span>
                <strong>{{ number_format($processingOrders) }}</strong>
                <p>Đơn đã được duyệt và đang chuẩn bị hàng.</p>
            </article>
            <article class="g-admin-kpi-card">
                <span>Đang giao</span>
                <strong>{{ number_format($shippingOrders) }}</strong>
                <p>Đơn đã bàn giao vận chuyển.</p>
            </article>
            <article class="g-admin-kpi-card">
                <span>Đã giao</span>
                <strong>{{ number_format($completedOrders) }}</strong>
                <p>Đã chuyển sang trạng thái hoàn tất giao hàng.</p>
            </article>
            <article class="g-admin-kpi-card">
                <span>Đã hủy</span>
                <strong>{{ number_format($cancelledOrders) }}</strong>
                <p>Có lý do hủy để đối chiếu với khách hàng.</p>
            </article>
        </section>

        <section class="g-admin-panel-card">
            <div class="g-admin-panel-head">
                <div>
                    <div>Danh sách đơn hàng</div>
                    <p>Theo dõi trạng thái, lý do hủy và truy cập nhanh vào từng đơn cần xử lý.</p>
                </div>
            </div>
            <div class="g-admin-panel-body">
                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif

                @if ($orderItems->isEmpty())
                    <div class="g-admin-empty-state">Chưa có đơn hàng nào để hiển thị trên môi trường này.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light g-admin-data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mã đơn</th>
                                    <th>Ngày đặt</th>
                                    <th>Trạng thái</th>
                                    <th>Lý do hủy</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($getorder as $index => $ord)
                                    <tr>
                                        <td>{{ $getorder->firstItem() + $index }}</td>
                                        <td>
                                            <div class="g-admin-order-code">{{ $ord->order_code }}</div>
                                        </td>
                                        <td>{{ $ord->created_at }}</td>
                                        <td>
                                            @php
                                                [$statusLabel, $statusClass] = $statusLabels[(int) $ord->order_status] ?? ['Chưa xác định', ''];
                                            @endphp
                                            <span class="g-admin-status-chip {{ $statusClass }}">{{ $statusLabel }}</span>
                                        </td>
                                        <td>{{ (int) $ord->order_status === 5 ? $ord->order_destroy : 'Không có' }}</td>
                                        <td>
                                            <div class="g-admin-order-table-actions">
                                                <a href="{{ URL::to('/view-order/' . $ord->order_code) }}" class="btn btn-xs btn-success">
                                                    <i class="fa fa-eye"></i> Xem
                                                </a>

                                                <form action="{{ url('/delete-order/' . $ord->order_code) }}" method="POST"
                                                    onsubmit="return confirm('Bạn có chắc muốn xóa đơn hàng này không?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-danger">
                                                        <i class="fa fa-times"></i> Xóa
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="panel-footer">
                @include('admin.partials.pagination', ['paginator' => $getorder, 'label' => 'đơn hàng'])
            </div>
        </section>
    </div>
@endsection
