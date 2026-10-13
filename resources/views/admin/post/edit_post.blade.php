@extends('admin_layout')

@section('admin_content')
    @php
        $featuredImagePreview = $post->featured_image_path && file_exists(public_path($post->featured_image_path))
            ? asset($post->featured_image_path)
            : asset('uploads/product/no-image.svg');
    @endphp

    <div class="g-admin-page-shell g-admin-post-edit">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Nội dung / Bài viết</span>
                <h1>Cập nhật bài viết</h1>
                <p>Chỉnh sửa nội dung, ảnh đại diện và metadata để bài viết tiếp tục hiển thị đúng trên website.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ $post->post_status == 0 ? 'Đang hoạt động' : 'Tạm ẩn' }}</strong>
                    <span>Trạng thái hiện tại</span>
                </article>
                <article>
                    <strong>{{ number_format($cate_post->count()) }}</strong>
                    <span>Danh mục có thể chuyển</span>
                </article>
            </div>
        </section>

        <div class="g-admin-panel-grid two-col">
            <section class="g-admin-panel-card">
                <header class="g-admin-panel-head">
                    <div>
                        <strong>Nội dung bài viết</strong>
                        <p>Cập nhật tiêu đề, nội dung, SEO và ảnh đại diện cho bài viết đang có.</p>
                    </div>
                </header>
                <div class="g-admin-panel-body">
                    @if (session('message'))
                        <div class="alert alert-success">{{ session('message') }}</div>
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

                    <form role="form" action="{{ url('/update-post/' . $post->post_id) }}" method="post"
                        enctype="multipart/form-data" class="g-admin-site-info-form">
                        @csrf
                        <div class="form-group">
                            <label for="post_title">Tên bài viết</label>
                            <input type="text" name="post_title" class="form-control" id="slug"
                                value="{{ old('post_title', $post->post_title) }}" onkeyup="ChangeToSlug();" required>
                        </div>
                        <div class="form-group">
                            <label for="post_slug">Đường dẫn tĩnh (slug)</label>
                            <input type="text" name="post_slug" class="form-control" id="convert_slug"
                                value="{{ old('post_slug', $post->post_slug) }}">
                        </div>
                        <div class="form-group">
                            <label for="post_desc">Tóm tắt bài viết</label>
                            <textarea rows="6" class="form-control" name="post_desc" id="ckeditor1">{{ old('post_desc', $post->post_desc) }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="post_content">Nội dung bài viết</label>
                            <textarea rows="8" class="form-control" name="post_content" id="ckeditor2">{{ old('post_content', $post->post_content) }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="post_meta_keywords">Meta từ khóa</label>
                            <textarea rows="3" class="form-control" name="post_meta_keywords" id="post_meta_keywords">{{ old('post_meta_keywords', $post->post_meta_keywords) }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="post_meta_desc">Meta mô tả</label>
                            <textarea rows="3" class="form-control" name="post_meta_desc" id="post_meta_desc">{{ old('post_meta_desc', $post->post_meta_desc) }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="post_image">Hình ảnh bài viết</label>
                            <input type="file" name="post_image" class="form-control" id="post_image" accept="image/*">
                            @if ($post->featured_image_path)
                                <div class="g-admin-surface" style="margin-top:12px;">
                                    <h3>Ảnh hiện tại</h3>
                                    <img src="{{ $featuredImagePreview }}" alt="{{ $post->post_title }}"
                                        style="height:100px;width:auto;">
                                    <label class="g-admin-check-card" style="margin-top:12px;">
                                        <input type="checkbox" name="remove_image" value="1"
                                            {{ old('remove_image') ? 'checked' : '' }}>
                                        <span>Gỡ ảnh đại diện hiện tại khi lưu</span>
                                    </label>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="cate_post_id">Danh mục bài viết</label>
                            <select name="cate_post_id" id="cate_post_id" class="form-control input-sm m-bot15">
                                <option value="">-- Chọn danh mục --</option>
                                @foreach ($cate_post as $cate)
                                    <option value="{{ $cate->cate_post_id }}"
                                        {{ (string) old('cate_post_id', $post->cate_post_id) === (string) $cate->cate_post_id ? 'selected' : '' }}>
                                        {{ $cate->cate_post_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="post_status">Trạng thái hiển thị</label>
                            <select name="post_status" id="post_status" class="form-control input-sm m-bot15">
                                <option value="0"
                                    {{ (string) old('post_status', $post->post_status) === '0' ? 'selected' : '' }}>Đang hoạt động
                                </option>
                                <option value="1"
                                    {{ (string) old('post_status', $post->post_status) === '1' ? 'selected' : '' }}>Tạm ẩn
                                </option>
                            </select>
                        </div>
                        <div class="g-admin-inline-actions">
                            <button type="submit" class="btn btn-info">Cập nhật bài viết</button>
                            <a href="{{ url('/all-post') }}" class="btn btn-default">Quay lại danh sách</a>
                        </div>
                    </form>
                </div>
            </section>

            <section class="g-admin-panel-card">
                <header class="g-admin-panel-head">
                    <div>
                        <strong>Thông tin hiện tại</strong>
                        <p>Tóm tắt nhanh về bài viết đang chỉnh sửa để tránh cập nhật nhầm nội dung.</p>
                    </div>
                </header>
                <div class="g-admin-panel-body">
                    <div class="g-admin-surface">
                        <h3>{{ $post->post_title }}</h3>
                        <ul class="g-admin-note-list">
                            <li>Đường dẫn tĩnh (slug) hiện tại: <strong>{{ $post->post_slug }}</strong></li>
                            <li>Danh mục: <strong>{{ optional($post->category)->name ?? 'Chưa chọn' }}</strong></li>
                            <li>Trạng thái: <strong>{{ $post->post_status == 0 ? 'Đang hoạt động' : 'Tạm ẩn' }}</strong></li>
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
