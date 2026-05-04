@extends('admin_layout')

@section('admin_content')
    @php
        $currentOrder = $order ?? $getorder->first();
        $details = $order_details ?? collect();
        $shippingData = $shipping ?? null;
        $customerData = $customer ?? null;
        $statusLabel = data_get($tracking, 'status_label', 'Đang xử lý');
        $statusTone = data_get($tracking, 'status_chip_tone', 'in-progress');
        $steps = collect(data_get($tracking, 'steps', []));
        $subSteps = collect(data_get($tracking, 'sub_steps', []));
        $carrier = data_get($tracking, 'carrier', []);
        $routePoints = collect($route_points ?? []);
    @endphp

    <div class="g-admin-page-shell g-admin-order-track">
        <style>
            .g-admin-order-track .track-hero-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                justify-content: flex-end;
            }

            .g-admin-order-track .track-status-chip {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                border-radius: 999px;
                padding: 7px 12px;
                font-size: 12px;
                font-weight: 700;
                background: #e0f2fe;
                color: #0369a1;
            }

            .g-admin-order-track .track-status-chip.success {
                background: #dcfce7;
                color: #15803d;
            }

            .g-admin-order-track .track-status-chip.danger {
                background: #fee2e2;
                color: #b91c1c;
            }

            .g-admin-order-track .track-grid {
                display: grid;
                grid-template-columns: minmax(0, 1.5fr) minmax(280px, 0.8fr);
                gap: 18px;
            }

            .g-admin-order-track .track-step-list,
            .g-admin-order-track .track-route-list {
                display: grid;
                gap: 14px;
                margin: 0;
                padding: 0;
                list-style: none;
            }

            .g-admin-order-track .track-step {
                display: grid;
                grid-template-columns: 36px minmax(0, 1fr);
                gap: 12px;
            }

            .g-admin-order-track .track-step-marker {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: #f1f5f9;
                color: #64748b;
                font-weight: 800;
                font-size: 12px;
            }

            .g-admin-order-track .track-step.done .track-step-marker {
                background: #dcfce7;
                color: #15803d;
            }

            .g-admin-order-track .track-step.active .track-step-marker {
                background: #ccfbf1;
                color: #0f766e;
                box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.12);
            }

            .g-admin-order-track .track-step-title {
                margin: 0;
                color: #111827;
                font-size: 14px;
                font-weight: 800;
            }

            .g-admin-order-track .track-step-time,
            .g-admin-order-track .track-muted {
                color: #64748b;
                font-size: 12px;
            }

            .g-admin-order-track .track-detail-list {
                display: grid;
                gap: 10px;
            }

            .g-admin-order-track .track-detail-list div {
                display: flex;
                justify-content: space-between;
                gap: 14px;
                border-bottom: 1px solid #edf2f7;
                padding-bottom: 9px;
            }

            .g-admin-order-track .track-detail-list span {
                color: #64748b;
                font-size: 12px;
            }

            .g-admin-order-track .track-detail-list strong {
                color: #111827;
                font-size: 12px;
                text-align: right;
            }

            .g-admin-order-track .track-route-card {
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                padding: 12px;
                background: #f8fafc;
            }

            .g-admin-order-track .track-route-card strong {
                display: block;
                color: #111827;
                font-size: 13px;
            }

            .g-admin-order-track .track-items-table th,
            .g-admin-order-track .track-items-table td {
                font-size: 12px;
                vertical-align: middle;
            }

            @media (max-width: 991px) {
                .g-admin-order-track .track-grid {
                    grid-template-columns: 1fr;
                }

                .g-admin-order-track .track-hero-actions {
                    justify-content: flex-start;
                }
            }
        </style>

        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Theo dõi đơn hàng</span>
                <h1>Đơn {{ $currentOrder?->order_code ?? 'Không xác định' }}</h1>
                <p>Kiểm tra trạng thái vận chuyển, thông tin người nhận và các dòng sản phẩm trong đơn hàng.</p>
            </div>

            <div class="track-hero-actions">
                <span class="track-status-chip {{ $statusTone }}">
                    <i class="fa fa-truck"></i>
                    {{ $statusLabel }}
                </span>
                @if ($currentOrder)
                    <a href="{{ url('/view-order/' . $currentOrder->order_code) }}" class="btn btn-default btn-sm">
                        <i class="fa fa-eye"></i> Chi tiết
                    </a>
                    <a target="_blank" href="{{ url('/print-order/' . $currentOrder->order_code) }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-print"></i> In đơn
                    </a>
                @endif
            </div>
        </section>

        <div class="track-grid">
            <section class="g-admin-panel-card">
                <div class="g-admin-panel-head">
                    <div>
                        <div>Lộ trình xử lý</div>
                        <p>Dòng thời gian vận hành đơn hàng từ lúc tạo đơn tới khi giao hàng.</p>
                    </div>
                </div>
                <div class="g-admin-panel-body">
                    @if ($steps->isEmpty())
                        <div class="g-admin-empty-state">Chưa có dữ liệu theo dõi cho đơn hàng này.</div>
                    @else
                        <ol class="track-step-list">
                            @foreach ($steps as $step)
                                <li class="track-step {{ data_get($step, 'is_done') ? 'done' : '' }} {{ data_get($step, 'is_active') ? 'active' : '' }}">
                                    <span class="track-step-marker">
                                        @if (data_get($step, 'is_done'))
                                            <i class="fa fa-check"></i>
                                        @else
                                            {{ data_get($step, 'index') }}
                                        @endif
                                    </span>
                                    <div>
                                        <p class="track-step-title">{{ data_get($step, 'label') }}</p>
                                        <div class="track-step-time">{{ data_get($step, 'datetime_label', 'Chưa xác định') }}</div>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </section>

            <div>
                <section class="g-admin-panel-card">
                    <div class="g-admin-panel-head">
                        <div>
                            <div>Thông tin vận chuyển</div>
                            <p>Đơn vị vận chuyển và thời gian giao dự kiến.</p>
                        </div>
                    </div>
                    <div class="g-admin-panel-body">
                        <div class="track-detail-list">
                            <div>
                                <span>Đơn vị</span>
                                <strong>{{ data_get($carrier, 'name', 'Chưa xác định') }}</strong>
                            </div>
                            <div>
                                <span>Mã vận đơn</span>
                                <strong>{{ data_get($carrier, 'tracking_number', 'Chưa xác định') }}</strong>
                            </div>
                            <div>
                                <span>Dự kiến giao</span>
                                <strong>{{ data_get($tracking, 'eta_label', 'Chưa xác định') }}</strong>
                            </div>
                            <div>
                                <span>Khung giờ</span>
                                <strong>{{ data_get($tracking, 'eta_window', 'Chưa xác định') }}</strong>
                            </div>
                            <div>
                                <span>Tài xế</span>
                                <strong>{{ data_get($carrier, 'driver_name', 'Chưa phân công') }}</strong>
                            </div>
                            <div>
                                <span>Điện thoại</span>
                                <strong>{{ data_get($carrier, 'driver_phone', 'Chưa có') }}</strong>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="g-admin-panel-card" style="margin-top:18px">
                    <div class="g-admin-panel-head">
                        <div>
                            <div>Người nhận</div>
                            <p>Thông tin giao hàng lấy từ đơn hàng hiện tại.</p>
                        </div>
                    </div>
                    <div class="g-admin-panel-body">
                        <div class="track-detail-list">
                            <div>
                                <span>Khách hàng</span>
                                <strong>{{ data_get($customerData, 'customer_name', 'Chưa có dữ liệu') }}</strong>
                            </div>
                            <div>
                                <span>Người nhận</span>
                                <strong>{{ data_get($shippingData, 'shipping_name', 'Chưa có dữ liệu') }}</strong>
                            </div>
                            <div>
                                <span>Điện thoại</span>
                                <strong>{{ data_get($shippingData, 'shipping_phone', 'Chưa có dữ liệu') }}</strong>
                            </div>
                            <div>
                                <span>Địa chỉ</span>
                                <strong>{{ data_get($shippingData, 'shipping_address', 'Chưa có dữ liệu') }}</strong>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="g-admin-panel-grid two-col" style="margin-top:18px">
            <section class="g-admin-panel-card">
                <div class="g-admin-panel-head">
                    <div>
                        <div>Điểm trung chuyển</div>
                        <p>Các mốc route nội bộ dùng để hỗ trợ chăm sóc khách hàng.</p>
                    </div>
                </div>
                <div class="g-admin-panel-body">
                    @if ($subSteps->isEmpty() && $routePoints->isEmpty())
                        <div class="g-admin-empty-state">Chưa có mốc vận chuyển nào.</div>
                    @else
                        <div class="track-route-list">
                            @foreach ($subSteps as $point)
                                <div class="track-route-card">
                                    <strong>{{ data_get($point, 'label') }}</strong>
                                    <span class="track-muted">{{ data_get($point, 'sub') }}</span>
                                </div>
                            @endforeach
                            @foreach ($routePoints as $point)
                                <div class="track-route-card">
                                    <strong>{{ data_get($point, 'label') }}</strong>
                                    <span class="track-muted">{{ data_get($point, 'lat') }}, {{ data_get($point, 'lng') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>

            <section class="g-admin-panel-card">
                <div class="g-admin-panel-head">
                    <div>
                        <div>Sản phẩm trong đơn</div>
                        <p>Danh sách sản phẩm đang được xử lý trong đơn hàng.</p>
                    </div>
                </div>
                <div class="g-admin-panel-body">
                    @if ($details->isEmpty())
                        <div class="g-admin-empty-state">Không có dòng sản phẩm nào trong đơn hàng này.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped b-t b-light track-items-table">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Thuộc tính</th>
                                        <th>Số lượng</th>
                                        <th>Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($details as $detail)
                                        <tr>
                                            <td>{{ $detail->product_name }}</td>
                                            <td>{{ $detail->product_attribute ?: 'Mặc định' }}</td>
                                            <td>{{ number_format((int) $detail->product_sales_quantity) }}</td>
                                            <td>{{ number_format((float) $detail->product_price * (int) $detail->product_sales_quantity, 0, ',', '.') }}đ</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
@endsection
