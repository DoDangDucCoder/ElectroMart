@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-delivery-page">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Vận chuyển</span>
                <h1>Cấu hình phí vận chuyển</h1>
                <p>Thiết lập phí vận chuyển theo tỉnh/thành phố và xã/phường/đặc khu.</p>
            </div>

            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($provinces->count()) }}</strong>
                    <span>Tỉnh thành sẵn có</span>
                </article>
                <article>
                    <strong>Theo khu vực</strong>
                    <span>Cập nhật mức phí theo địa giới hành chính</span>
                </article>
            </div>
        </section>

        <div class="g-admin-panel-grid two-col g-admin-delivery-layout">
            <section class="g-admin-panel-card">
                <div class="g-admin-panel-head">
                    <div>
                        <div>Thêm phí vận chuyển</div>
                        <p>Chọn đủ 2 cấp địa giới trước khi lưu giá trị phí ship.</p>
                    </div>
                </div>
                <div class="g-admin-panel-body">
                    @if (session('message'))
                        <div class="alert alert-success">{{ session('message') }}</div>
                    @endif

                    <form class="g-admin-site-info-form">
                        @csrf

                        <div class="form-group">
                            <label for="province">Chọn tỉnh/thành phố</label>
                            <select name="province_code" id="province" class="form-control input-sm m-bot15 choose province">
                                <option value="">--Chọn tỉnh thành phố--</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province->code }}">{{ $province->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="wards">Chọn xã/phường/đặc khu</label>
                            <select name="ward_code" id="wards" class="form-control input-sm m-bot15 wards">
                                <option value="">--Chọn xã/phường/đặc khu--</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="fee_ship">Phí vận chuyển</label>
                            <input type="text" name="fee_ship" class="form-control fee_ship" id="fee_ship" placeholder="Ví dụ: 30000">
                        </div>

                        <div class="g-admin-inline-actions">
                            <button type="button" name="add_delivery" class="btn btn-info add_delivery">Thêm phí vận chuyển</button>
                        </div>
                    </form>

                    <div id="delivery_notify" style="margin-top:16px;"></div>

                    @if ($provinces->isEmpty())
                        <div class="g-admin-empty-state">
                            Chưa có dữ liệu tỉnh thành để cấu hình phí vận chuyển trên môi trường này.
                        </div>
                    @endif
                </div>
            </section>

            <section class="g-admin-panel-card">
                <div class="g-admin-panel-head">
                    <div>
                        <div>Danh sách phí ship hiện tại</div>
                        <p>Theo dõi các mức phí vận chuyển đang áp dụng theo từng khu vực.</p>
                    </div>
                </div>
                <div class="g-admin-panel-body">
                    <div id="load_delivery"></div>
                </div>
            </section>
        </div>
    </div>
@endsection
