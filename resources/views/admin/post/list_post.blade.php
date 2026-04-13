@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-post-list">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Nội dung / Bài viết</span>
                <h1>Liệt kê bài viết</h1>
                <p>Quản lý bài viết, lọc theo danh mục và trạng thái hiển thị trên website.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ $all_post->total() }}</strong>
                    <span>Tổng bài viết</span>
                </article>
                <article>
                    <strong>{{ $categories->count() }}</strong>
                    <span>Danh mục blog</span>
                </article>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Bộ lọc và danh sách</strong>
                    <p>Theo dõi danh sách bài viết hiện có và lọc nhanh theo nhu cầu quản trị.</p>
                </div>
                <div class="g-admin-inline-actions">
                    <a href="{{ url('/add-post') }}" class="btn btn-info">Thêm bài viết</a>
                </div>
            </header>
            <div class="g-admin-panel-body">
                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @elseif (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="g-admin-toolbar">
                    <form class="form-inline" method="get" action="{{ url('/all-post') }}">
                        <div class="form-group">
                            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm"
                                value="{{ $filters['search'] ?? '' }}">
                        </div>
                        <div class="form-group">
                            <select name="category_id" class="form-control">
                                <option value="">-- Tất cả danh mục --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->cate_post_id }}"
                                        {{ (string) ($filters['category_id'] ?? '') === (string) $category->cate_post_id ? 'selected' : '' }}>
                                        {{ $category->cate_post_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="status" class="form-control">
                                <option value="">-- Trạng thái --</option>
                                <option value="published" {{ ($filters['status'] ?? '') === 'published' ? 'selected' : '' }}>Hiển thị</option>
                                <option value="draft" {{ ($filters['status'] ?? '') === 'draft' ? 'selected' : '' }}>Ẩn</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Lọc</button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped b-t b-light g-admin-data-table" id="myTable">
                        <thead>
                            <tr>
                                <th style="width:20px;">
                                    <label class="i-checks m-b-none">
                                        <input type="checkbox"><i></i>
                                    </label>
                                </th>
                                <th>Tên bài viết</th>
                                <th>Hình ảnh</th>
                                <th>Slug</th>
                                <th>Tóm tắt</th>
                                <th>Từ khóa</th>
                                <th>Danh mục</th>
                                <th>Hiển thị</th>
                                <th style="width:30px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($all_post as $post)
                                @php
                                    $postImagePreview = $post->featured_image_path && file_exists(public_path($post->featured_image_path))
                                        ? asset($post->featured_image_path)
                                        : asset('uploads/product/no-image.svg');
                                @endphp
                                <tr>
                                    <td>
                                        <label class="i-checks m-b-none">
                                            <input type="checkbox" name="post[]"><i></i>
                                        </label>
                                    </td>
                                    <td>
                                        <strong>{{ $post->post_title }}</strong>
                                    </td>
                                    <td>
                                        <img src="{{ $postImagePreview }}"
                                            alt="{{ $post->post_title }}" style="height:80px;width:120px;">
                                    </td>
                                    <td>{{ $post->post_slug }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit(strip_tags((string) $post->post_desc), 120) }}</td>
                                    <td>{{ $post->post_meta_keywords }}</td>
                                    <td>{{ optional($post->category)->name }}</td>
                                    <td>
                                        @if ($post->post_status == 0)
                                            <span class="label label-success">Hiển thị</span>
                                        @else
                                            <span class="label label-default">Ẩn</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ url('/edit-post/' . $post->post_id) }}" class="active styling-edit">
                                            <i class="fa fa-pencil-square-o text-success text-active"></i>
                                        </a>
                                        <form action="{{ url('/delete-post/' . $post->post_id) }}" method="POST"
                                            style="display:inline-block;"
                                            onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này không?')">
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
                                    <td colspan="9">
                                        <div class="g-admin-empty-state">
                                            Chưa có bài viết nào khớp bộ lọc hiện tại.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <footer class="panel-footer">
                <div class="row">
                    <div class="col-sm-12 text-right">
                        {{ $all_post->appends(request()->query())->links() }}
                    </div>
                </div>
            </footer>
        </section>
    </div>
@endsection
