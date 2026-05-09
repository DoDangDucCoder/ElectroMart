@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Chức năng quản trị</span>
                <h1>{{ $title ?? 'Chức năng' }}</h1>
                <p>Chức năng này chưa được kích hoạt trong phiên bản hiện tại.</p>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Trạng thái: Đang tạm khóa hoặc đang phát triển</strong>
                    <p>Vui lòng quay lại Dashboard để tiếp tục sử dụng các chức năng quản trị đang hoạt động.</p>
                </div>
            </header>
            <div class="g-admin-panel-body">
                <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                    <i class="fa fa-dashboard"></i> Quay về Dashboard
                </a>
            </div>
        </section>
    </div>
@endsection
