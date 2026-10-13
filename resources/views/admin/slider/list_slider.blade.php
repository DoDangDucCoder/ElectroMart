@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-slider-list">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Nội dung / Trình chiếu</span>
                <h1>Quản lý banner</h1>
                <p>Theo dõi trạng thái hiển thị của banner và xóa những mục không còn sử dụng.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ $all_slide->total() }}</strong>
                    <span>Tổng banner</span>
                </article>
                <article>
                    <strong>{{ $all_slide->count() }}</strong>
                    <span>Đang hiển thị trang này</span>
                </article>
            </div>
        </section>

        @if (session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Danh sách trình chiếu</strong>
                    <p>Bật tắt trạng thái và xóa trực tiếp ngay trên bảng hiện tại.</p>
                </div>
                <div class="g-admin-inline-actions">
                    <a href="{{ url('/add-slider') }}" class="btn btn-info">Thêm trình chiếu</a>
                </div>
            </header>
            <div class="g-admin-panel-body">
                <div class="table-responsive">
                    <table class="table table-striped b-t b-light g-admin-data-table">
                        <thead>
                            <tr>
                                <th>Tên mục</th>
                                <th>Hình ảnh</th>
                                <th>Tiêu đề nổi bật</th>
                                <th>Phong cách</th>
                                <th>Trạng thái</th>
                                <th style="width:80px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($all_slide as $slide)
                                <tr>
                                    <td>
                                        <strong>{{ $slide->slider_name }}</strong>
                                        @if (!empty($slide->slider_alt))
                                            <span class="g-admin-cell-note">Văn bản thay thế / tiêu đề: {{ $slide->slider_alt }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $sliderImagePath = 'uploads/slider/' . $slide->slider_image;
                                        @endphp
                                        <img src="{{ file_exists(public_path($sliderImagePath)) ? asset($sliderImagePath) : asset('uploads/product/no-image.svg') }}"
                                            alt="{{ $slide->slider_name }}" height="120" width="240">
                                    </td>
                                    <td>
                                        <strong>{{ $slide->slider_alt ?: 'Chưa có tiêu đề riêng' }}</strong>
                                        <span class="g-admin-cell-note">
                                            {{ \Illuminate\Support\Str::limit((string) $slide->slider_desc, 120) ?: 'Giao diện cửa hàng sẽ dùng lời dẫn mặc định cho mục trình chiếu này.' }}
                                        </span>
                                        @if (! empty($slide->slider_link_url))
                                            <span class="g-admin-cell-note">Link: {{ $slide->slider_link_url }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="g-admin-status-chip is-info">
                                            {{ match ($slide->slider_tone ?? null) {
                                                'mobile' => 'Điện thoại',
                                                'gaming' => 'Gaming',
                                                'lifestyle' => 'Khuyến mãi',
                                                'accessory' => 'Phụ kiện',
                                                default => 'Tự động',
                                            } }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($slide->slider_status == 1)
                                            <form action="{{ url('/unactive-slide/' . $slide->slider_id) }}" method="POST"
                                                style="display:inline-block;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" style="border:0;background:none;padding:0;">
                                                    <span class="fa-thumb-styling fa fa-thumbs-up"></span>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ url('/active-slide/' . $slide->slider_id) }}" method="POST"
                                                style="display:inline-block;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" style="border:0;background:none;padding:0;">
                                                    <span class="fa-thumb-styling fa fa-thumbs-down"></span>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ url('/edit-slider/' . $slide->slider_id) }}" class="active styling-edit"
                                            style="display:inline-block;margin-right:8px;">
                                            <i class="fa fa-pencil text-info text"></i>
                                        </a>
                                        <form action="{{ url('/delete-slide/' . $slide->slider_id) }}" method="POST"
                                            style="display:inline-block;"
                                            onsubmit="return confirm('Bạn có chắc muốn xóa mục trình chiếu này không?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="active styling-edit"
                                                style="border:0;background:none;padding:0;">
                                                <i class="fa fa-times text-danger text"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="g-admin-empty-state">Chưa có mục trình chiếu nào hoặc bảng dữ liệu trình chiếu chưa được cấu hình.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <footer class="panel-footer">
                @include('admin.partials.pagination', ['paginator' => $all_slide, 'label' => 'slider'])
            </footer>
        </section>
    </div>
@endsection
