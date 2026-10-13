@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-mail-preview">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Email marketing</span>
                <h1>Gửi mã giảm giá hàng loạt</h1>
                <p>
                    Chọn mã giảm giá đã tạo rồi gửi email cho khách hàng đã đăng ký. Khung bên dưới chỉ là bản xem trước nội dung email trước khi gửi.
                </p>
            </div>

            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($campaignStats['all'] ?? 0) }}</strong>
                    <span>Email khách hợp lệ</span>
                </article>
                <article>
                    <strong>{{ number_format($campaignStats['vip'] ?? 0) }}</strong>
                    <span>Khách thân thiết</span>
                </article>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <div class="g-admin-panel-head">
                <div>
                    <div>Gửi mã giảm giá cho khách hàng</div>
                    <p>Đây là chức năng chính: chủ shop tạo mã giảm giá, sau đó bấm gửi email hàng loạt cho khách đã đăng ký.</p>
                </div>
                <a href="{{ url('/insert-coupon') }}" class="btn btn-primary btn-sm">Tạo mã mới</a>
            </div>
            <div class="g-admin-panel-body">
                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if ($campaignCoupons->isEmpty())
                    <div class="g-admin-empty-state">Chưa có mã giảm giá đang hoạt động, còn lượt sử dụng và còn hạn để gửi email.</div>
                @else
                    <div class="g-admin-kpi-grid">
                        @foreach ($campaignCoupons as $coupon)
                            @php
                                $condition = $coupon->type === 'percent' ? 1 : 2;
                                $value = (float) $coupon->value;
                                $usageLimit = (int) ($coupon->usage_limit ?? 0);
                                $discountLabel = $coupon->type === 'percent'
                                    ? 'Giảm ' . rtrim(rtrim(number_format($value, 2, ',', '.'), '0'), ',') . '%'
                                    : 'Giảm ' . number_format($value, 0, ',', '.') . 'đ';
                                $dateLabel = ($coupon->starts_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') ?? 'Chưa đặt')
                                    . ' - '
                                    . ($coupon->expires_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') ?? 'Chưa đặt');
                                $sendParams = [
                                    'coupon_time' => $usageLimit,
                                    'coupon_condition' => $condition,
                                    'coupon_number' => $value,
                                    'coupon_code' => $coupon->code,
                                ];
                            @endphp
                            <article class="g-admin-kpi-card">
                                <span>{{ $discountLabel }}</span>
                                <strong>{{ $coupon->code }}</strong>
                                <p>{{ $coupon->name }}<br>{{ $dateLabel }} · còn {{ number_format($usageLimit) }} lượt</p>
                                <div class="g-admin-inline-actions">
                                    <form action="{{ url('/send-coupon', $sendParams) }}" method="POST" onsubmit="return confirm('Gửi mã {{ $coupon->code }} cho tất cả khách hàng đã đăng ký có email hợp lệ?')">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">Gửi tất cả khách</button>
                                    </form>
                                    <form action="{{ url('/send-coupon-vip', $sendParams) }}" method="POST" onsubmit="return confirm('Gửi mã {{ $coupon->code }} cho khách hàng thân thiết?')">
                                        @csrf
                                        <button type="submit" class="btn btn-default btn-sm">Gửi khách VIP</button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <section class="g-admin-panel-card">
            <div class="g-admin-panel-head">
                <div>
                    <div>Mẫu email sẽ gửi</div>
                    <p>Chọn biến thể để kiểm tra nội dung email trước khi bấm gửi hàng loạt ở phía trên.</p>
                </div>
                <a href="{{ $selectedRenderUrl }}" target="_blank" rel="noopener" class="btn btn-primary btn-sm">
                    Mở bản xem trước ở tab mới
                </a>
            </div>
            <div class="g-admin-panel-body">
                <div class="g-admin-kpi-grid">
                    @foreach ($previews as $key => $preview)
                        <article class="g-admin-kpi-card {{ $selectedPreviewKey === $key ? 'is-active' : '' }}">
                            <span>{{ $preview['category'] }}</span>
                            <strong>{{ $preview['label'] }}</strong>
                            <p>{{ $preview['description'] }}</p>
                            <div class="g-admin-inline-actions">
                                <a href="{{ route('admin.mail.preview', ['preview' => $key]) }}"
                                    class="btn btn-default btn-sm">Xem trong khung</a>
                                <a href="{{ route('admin.mail.preview.render', ['preview' => $key]) }}"
                                    target="_blank" rel="noopener"
                                    class="btn btn-link btn-sm">HTML trực tiếp</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <div class="g-admin-panel-head">
                <div>
                    <div>{{ $selectedPreview['label'] }}</div>
                    <p>{{ $selectedPreview['description'] }}</p>
                </div>
                <span class="g-admin-status-chip is-info">{{ $selectedPreview['snapshot_label'] ?? $selectedPreview['label'] }}</span>
            </div>
            <div class="g-admin-panel-body">
                <iframe
                    id="mail-preview-frame"
                    title="Xem trước email {{ $selectedPreview['label'] }}"
                    src="{{ $selectedRenderUrl }}"
                    style="width:100%;min-height:960px;border:1px solid #d9e6f5;border-radius:18px;background:#eef3fb;"></iframe>
            </div>
        </section>
    </div>
@endsection
