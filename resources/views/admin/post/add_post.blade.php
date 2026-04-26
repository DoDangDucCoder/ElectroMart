@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-post-create">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Nội dung / Bài viết</span>
                <h1>Thêm bài viết</h1>
                <p>Tạo bài viết mới với tiêu đề, nội dung, SEO và ảnh đại diện để hiển thị trên website.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($cate_post->count()) }}</strong>
                    <span>Danh mục có thể chọn</span>
                </article>
                <article>
                    <strong>2</strong>
                    <span>Vùng nội dung CKEditor</span>
                </article>
            </div>
        </section>

        <div class="g-admin-panel-grid two-col">
            <section class="g-admin-panel-card">
                <header class="g-admin-panel-head">
                    <div>
                        <strong>Thông tin bài viết</strong>
                        <p>Điền đầy đủ nội dung, hình ảnh và metadata trước khi xuất bản bài viết.</p>
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

                    <form role="form" action="{{ url('/save-post') }}" method="post" enctype="multipart/form-data"
                        class="g-admin-site-info-form">
                        @csrf
                        <div class="form-group">
                            <label for="post_title">Tên bài viết</label>
                            <input type="text" name="post_title" class="form-control" id="slug"
                                value="{{ old('post_title') }}" onkeyup="ChangeToSlug();" placeholder="Tên bài viết"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="post_slug">Đường dẫn tĩnh (slug)</label>
                            <input type="text" name="post_slug" class="form-control" id="convert_slug"
                                value="{{ old('post_slug') }}" placeholder="duong-dan-tinh-bai-viet">
                        </div>
                        <div class="form-group">
                            <label for="post_desc">Tóm tắt bài viết</label>
                            <textarea rows="6" class="form-control" name="post_desc" id="ckeditor1"
                                placeholder="Nhập tóm tắt">{{ old('post_desc') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="post_content">Nội dung bài viết</label>
                            <textarea rows="8" class="form-control" name="post_content" id="ckeditor2"
                                placeholder="Nhập nội dung">{{ old('post_content') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="post_meta_keywords">Meta từ khóa</label>
                            <textarea rows="3" class="form-control" name="post_meta_keywords" id="post_meta_keywords"
                                placeholder="Nhập meta từ khóa">{{ old('post_meta_keywords') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="post_meta_desc">Meta mô tả</label>
                            <textarea rows="3" class="form-control" name="post_meta_desc" id="post_meta_desc"
                                placeholder="Nhập meta mô tả">{{ old('post_meta_desc') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="post_image">Hình ảnh bài viết</label>
                            <input type="file" name="post_image" class="form-control" id="post_image" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label for="cate_post_id">Danh mục bài viết</label>
                            <select name="cate_post_id" id="cate_post_id" class="form-control input-sm m-bot15">
                                <option value="">-- Chọn danh mục --</option>
                                @foreach ($cate_post as $cate)
                                    <option value="{{ $cate->cate_post_id }}"
                                        {{ (string) $cate->cate_post_id === (string) old('cate_post_id') ? 'selected' : '' }}>
                                        {{ $cate->cate_post_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="post_status">Hiển thị</label>
                            <select name="post_status" id="post_status" class="form-control input-sm m-bot15">
                                <option value="0" {{ old('post_status', '0') === '0' ? 'selected' : '' }}>Hiển thị
                                </option>
                                <option value="1" {{ old('post_status') === '1' ? 'selected' : '' }}>Ẩn</option>
                            </select>
                        </div>
                        <div class="g-admin-inline-actions">
                            <button type="submit" class="btn btn-info">Thêm bài viết</button>
                            <a href="{{ url('/all-post') }}" class="btn btn-default">Xem danh sách bài viết</a>
                        </div>
                    </form>
                </div>
            </section>

            <section class="g-admin-panel-card">
                <header class="g-admin-panel-head">
                    <div>
                        <strong>Gợi ý biên tập</strong>
                        <p>Một vài điểm nên kiểm tra trước khi xuất bản để bài viết hiển thị tốt hơn.</p>
                    </div>
                </header>
                <div class="g-admin-panel-body">
                    <div class="g-admin-surface">
                        <h3>Checklist nhanh</h3>
                        <ul class="g-admin-note-list">
                            <li>Tiêu đề nên rõ nghĩa và đủ từ khóa chính.</li>
                            <li>Đường dẫn tĩnh (slug) ngắn gọn để thuận tiện cho SEO và chia sẻ liên kết.</li>
                            <li>Ảnh đại diện nên đúng tỷ lệ để hiển thị đẹp ở danh sách bài viết.</li>
                            <li>Meta mô tả nên ngắn gọn, đúng nội dung bài viết.</li>
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
