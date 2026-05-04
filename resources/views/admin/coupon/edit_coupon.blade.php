@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-coupon-edit">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Mã giảm giá</span>
                <h1>Chỉnh sửa mã giảm giá</h1>
                <p>Cập nhật thông tin, thời gian hiệu lực, hạn mức và trạng thái sử dụng của mã giảm giá.</p>
            </div>

            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ $legacyCoupon->coupon_code }}</strong>
                    <span>Mã hiện tại</span>
                </article>
                <article>
                    <strong>{{ number_format((int) ($coupon->used_count ?? 0)) }}</strong>
                    <span>Lượt đã dùng</span>
                </article>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <div class="g-admin-panel-head">
                <div>
                    <div>Thông tin mã giảm giá</div>
                    <p>Các thay đổi sẽ được lưu vào bảng coupons của schema hiện tại.</p>
                </div>
                <a href="{{ url('/list-coupon') }}" class="btn btn-default btn-sm">Quay lại danh sách</a>
            </div>
            <div class="g-admin-panel-body">
                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ url('/update-coupon/' . $coupon->id) }}" method="post" class="g-admin-form-grid">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="coupon_name">Tên mã giảm giá</label>
                        <input type="text" name="coupon_name" class="form-control" id="coupon_name"
                            value="{{ old('coupon_name', $legacyCoupon->coupon_name) }}">
                    </div>

                    <div class="form-group">
                        <label for="coupon_code">Mã giảm giá</label>
                        <input type="text" name="coupon_code" class="form-control" id="coupon_code"
                            value="{{ old('coupon_code', $legacyCoupon->coupon_code) }}">
                    </div>

                    <div class="form-group">
                        <label for="start_coupon">Ngày bắt đầu</label>
                        <input type="text" name="coupon_date_start" class="form-control" id="start_coupon"
                            value="{{ old('coupon_date_start', $legacyCoupon->coupon_date_start) }}" placeholder="DD/MM/YYYY">
                    </div>

                    <div class="form-group">
                        <label for="end_coupon">Ngày kết thúc</label>
                        <input type="text" name="coupon_date_end" class="form-control" id="end_coupon"
                            value="{{ old('coupon_date_end', $legacyCoupon->coupon_date_end) }}" placeholder="DD/MM/YYYY">
                    </div>

                    <div class="form-group">
                        <label for="coupon_time">Tổng số lượt dùng</label>
                        <input type="number" name="coupon_time" class="form-control" id="coupon_time"
                            value="{{ old('coupon_time', $legacyCoupon->coupon_time ?: 1) }}" min="1">
                    </div>

                    <div class="form-group">
                        <label for="usage_per_customer">Lượt dùng mỗi khách</label>
                        <input type="number" name="usage_per_customer" class="form-control" id="usage_per_customer"
                            value="{{ old('usage_per_customer', $coupon->usage_per_customer) }}" min="1" placeholder="Không giới hạn nếu bỏ trống">
                    </div>

                    <div class="form-group">
                        <label for="coupon_condition">Loại giảm</label>
                        <select name="coupon_condition" id="coupon_condition" class="form-control input-sm m-bot15">
                            <option value="1" @selected((string) old('coupon_condition', $legacyCoupon->coupon_condition) === '1')>Giảm theo phần trăm</option>
                            <option value="2" @selected((string) old('coupon_condition', $legacyCoupon->coupon_condition) === '2')>Giảm theo tiền</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="coupon_number">Giá trị giảm</label>
                        <input type="number" step="0.01" name="coupon_number" class="form-control" id="coupon_number"
                            value="{{ old('coupon_number', $legacyCoupon->coupon_number) }}" min="0">
                    </div>

                    <div class="form-group">
                        <label for="min_order_total">Đơn hàng tối thiểu</label>
                        <input type="number" step="0.01" name="min_order_total" class="form-control" id="min_order_total"
                            value="{{ old('min_order_total', $coupon->min_order_total) }}" min="0" placeholder="Không yêu cầu nếu bỏ trống">
                    </div>

                    <div class="form-group">
                        <label for="max_discount">Giảm tối đa</label>
                        <input type="number" step="0.01" name="max_discount" class="form-control" id="max_discount"
                            value="{{ old('max_discount', $coupon->max_discount) }}" min="0" placeholder="Không giới hạn nếu bỏ trống">
                    </div>

                    <div class="form-group">
                        <label for="is_active">Trạng thái</label>
                        <select name="is_active" id="is_active" class="form-control">
                            <option value="1" @selected((string) old('is_active', (int) $coupon->is_active) === '1')>Đang kích hoạt</option>
                            <option value="0" @selected((string) old('is_active', (int) $coupon->is_active) === '0')>Đã khóa</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="is_stackable">Cộng dồn mã</label>
                        <select name="is_stackable" id="is_stackable" class="form-control">
                            <option value="0" @selected((string) old('is_stackable', (int) $coupon->is_stackable) === '0')>Không cho cộng dồn</option>
                            <option value="1" @selected((string) old('is_stackable', (int) $coupon->is_stackable) === '1')>Cho cộng dồn</option>
                        </select>
                    </div>

                    <div class="g-admin-inline-actions g-admin-form-span-2">
                        <button type="submit" class="btn btn-info">Cập nhật mã</button>
                        <a href="{{ url('/list-coupon') }}" class="btn btn-default">Hủy</a>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
