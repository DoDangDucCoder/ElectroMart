@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-mail-preview">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Email hệ thống</span>
                <h1>Xem trước email hệ thống</h1>
                <p>
                    Kiểm tra nhanh giao diện email đang chạy thật, đối chiếu bản lưu và mở từng biến thể trong tab riêng khi cần kiểm thử.
                </p>
            </div>

            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format(count($previews)) }}</strong>
                    <span>Biến thể đang có</span>
                </article>
                <article>
                    <strong>{{ $selectedPreview['snapshot'] ?? 'Chưa có' }}</strong>
                    <span>Bản lưu đang khóa</span>
                </article>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <div class="g-admin-panel-head">
                <div>
                    <div>Danh mục xem trước</div>
                    <p>Chọn một biến thể để xem HTML email thật đang hiển thị trong ứng dụng.</p>
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
                <span class="g-admin-status-chip is-info">{{ $selectedPreview['snapshot'] }}</span>
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
