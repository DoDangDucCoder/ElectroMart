@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-category-post-edit">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Nội dung / Danh mục bài viết</span>
                <h1>Cập nhật danh mục bài viết</h1>
                <p>Điều chỉnh tên, mô tả và trạng thái hiển thị của danh mục bài viết.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ $category_post->cate_post_name }}</strong>
                    <span>Danh mục đang chỉnh</span>
                </article>
                <article>
                    <strong>{{ $category_post->cate_post_status == 0 ? 'Đang hoạt động' : 'Tạm ẩn' }}</strong>
                    <span>Trạng thái hiện tại</span>
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
                    <p>Đổi tên, đường dẫn tĩnh (slug) hoặc trạng thái hiển thị của nhóm nội dung tin tức.</p>
                </div>
            </header>
            <div class="g-admin-panel-body">
                <form action="{{ url('/update-category-post/' . $category_post->cate_post_id) }}" method="post"
                    class="g-admin-site-info-form">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="slug">Tên danh mục</label>
                        <input type="text" name="cate_post_name" value="{{ old('cate_post_name', $category_post->cate_post_name) }}"
                            class="form-control" onkeyup="ChangeToSlug();" id="slug" placeholder="Tên danh mục">
                    </div>
                    <div class="form-group">
                        <label for="convert_slug">Đường dẫn tĩnh (slug)</label>
                        <input type="text" name="cate_post_slug" value="{{ old('cate_post_slug', $category_post->cate_post_slug) }}"
                            class="form-control" id="convert_slug" placeholder="Đường dẫn tĩnh (slug)">
                    </div>
                    <div class="form-group">
                        <label for="cate_post_desc">Mô tả danh mục</label>
                        <textarea style="resize:none" rows="8" class="form-control" name="cate_post_desc" id="cate_post_desc"
                            placeholder="Mô tả danh mục">{{ old('cate_post_desc', $category_post->cate_post_desc) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="cate_post_status">Trạng thái hiển thị</label>
                        <select name="cate_post_status" id="cate_post_status" class="form-control input-sm m-bot15">
                            @if ((string) old('cate_post_status', (string) $category_post->cate_post_status) === '0')
                                <option selected value="0">Đang hoạt động</option>
                                <option value="1">Tạm ẩn</option>
                            @else
                                <option value="0">Đang hoạt động</option>
                                <option selected value="1">Tạm ẩn</option>
                            @endif
                        </select>
                    </div>
                    <button type="submit" name="update_post_cate" class="btn btn-info">Cập nhật danh mục bài viết</button>
                </form>
            </div>
        </section>
    </div>
@endsection
