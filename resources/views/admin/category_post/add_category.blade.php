@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-category-post-create">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Nội dung / Danh mục bài viết</span>
                <h1>Thêm danh mục bài viết</h1>
                <p>Tạo nhóm nội dung để sắp xếp bài viết rõ ràng hơn trên website.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>Blog</strong>
                    <span>Phân nhóm nội dung</span>
                </article>
                <article>
                    <strong>SEO</strong>
                    <span>Slug và mô tả rõ ràng</span>
                </article>
            </div>
        </section>

        @if (session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @elseif (session('error'))
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

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Thông tin danh mục</strong>
                    <p>Tạo nhóm nội dung để dễ quản lý và lọc bài viết theo chủ đề.</p>
                </div>
            </header>
            <div class="g-admin-panel-body">
                <form action="{{ url('/save-category-post') }}" method="post" class="g-admin-site-info-form">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="slug">Tên danh mục</label>
                        <input type="text" name="cate_post_name" class="form-control" onkeyup="ChangeToSlug();" id="slug"
                            value="{{ old('cate_post_name') }}" placeholder="Tên danh mục">
                    </div>
                    <div class="form-group">
                        <label for="convert_slug">Slug</label>
                        <input type="text" name="cate_post_slug" class="form-control" id="convert_slug"
                            value="{{ old('cate_post_slug') }}" placeholder="slug-danh-muc">
                    </div>
                    <div class="form-group">
                        <label for="cate_post_desc">Mô tả danh mục</label>
                        <textarea style="resize:none" rows="8" class="form-control" name="cate_post_desc" id="cate_post_desc"
                            placeholder="Mô tả danh mục">{{ old('cate_post_desc') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="cate_post_status">Hiển thị</label>
                        <select name="cate_post_status" id="cate_post_status" class="form-control input-sm m-bot15">
                            <option value="0" {{ old('cate_post_status', '0') === '0' ? 'selected' : '' }}>Hiển thị</option>
                            <option value="1" {{ old('cate_post_status') === '1' ? 'selected' : '' }}>Ẩn</option>
                        </select>
                    </div>
                    <button type="submit" name="add_post_cate" class="btn btn-info">Thêm danh mục bài viết</button>
                </form>
            </div>
        </section>
    </div>
@endsection
