@extends('admin_layout')

@section('admin_content')
    @php
        $orderItems = method_exists($getorder, 'getCollection') ? $getorder->getCollection() : collect($getorder);
        $newOrders = $orderItems->where('order_status', 1)->count();
        $completedOrders = $orderItems->where('order_status', 2)->count();
        $cancelledOrders = $orderItems->where('order_status', 3)->count();
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
                    <strong>{{ number_format($newOrders) }}</strong>
                    <span>Đơn mới trên trang này</span>
                </article>
            </div>
        </section>

        <section class="g-admin-kpi-grid">
            <article class="g-admin-kpi-card">
                <span>Đơn mới</span>
                <strong>{{ number_format($newOrders) }}</strong>
                <p>Cần xử lý hoặc xác nhận trong bước tiếp theo.</p>
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
                                            @if ($ord->order_status == 1)
                                                <span class="g-admin-status-chip is-warning">Đơn hàng mới</span>
                                            @elseif($ord->order_status == 2)
                                                <span class="g-admin-status-chip is-success">Đã xử lý - Đã giao hàng</span>
                                            @else
                                                <span class="g-admin-status-chip is-danger">Đơn hàng đã bị hủy</span>
                                            @endif
                                        </td>
                                        <td>{{ $ord->order_status == 3 ? $ord->order_destroy : 'Không có' }}</td>
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
                <div class="row">
                    <div class="col-sm-5 text-center">
                        <small class="text-muted inline m-t-sm m-b-sm">
                            Hiển thị {{ $getorder->count() }} / {{ $getorder->total() }} đơn hàng
                        </small>
                    </div>
                    <div class="col-sm-7 text-right text-center-xs">
                        {{ $getorder->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
