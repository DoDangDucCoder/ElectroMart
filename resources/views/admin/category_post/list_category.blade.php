@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-category-post-list">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Nội dung / Danh mục bài viết</span>
                <h1>Quản lý danh mục bài viết</h1>
                <p>Theo dõi danh mục bài viết hiện có và cập nhật nhanh khi cần chỉnh sửa nội dung.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ $category_post->total() }}</strong>
                    <span>Tổng danh mục</span>
                </article>
                <article>
                    <strong>{{ $category_post->count() }}</strong>
                    <span>Đang hiển thị trang này</span>
                </article>
            </div>
        </section>

        @if (session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @elseif (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Danh sách danh mục</strong>
                    <p>Kiểm tra tên, mô tả và trạng thái hiển thị của từng danh mục bài viết.</p>
                </div>
                <div class="g-admin-inline-actions">
                    <a href="{{ url('/add-category-post') }}" class="btn btn-info">Thêm danh mục</a>
                </div>
            </header>
            <div class="g-admin-panel-body">
                <div class="table-responsive">
                    <table class="table table-striped b-t b-light g-admin-data-table">
                        <thead>
                            <tr>
                                <th>Tên danh mục bài viết</th>
                                <th>Mô tả</th>
                                <th>Đường dẫn tĩnh (slug)</th>
                                <th>Trạng thái</th>
                                <th style="width:80px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($category_post as $cate_post)
                                <tr>
                                    <td><strong>{{ $cate_post->cate_post_name }}</strong></td>
                                    <td>{{ \Illuminate\Support\Str::limit((string) $cate_post->cate_post_desc, 120) }}</td>
                                    <td>{{ $cate_post->cate_post_slug }}</td>
                                    <td>
                                        @if ($cate_post->cate_post_status == 0)
                                            <span class="label label-success">Đang hoạt động</span>
                                        @else
                                            <span class="label label-default">Tạm ẩn</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ url('/edit-category-post/' . $cate_post->cate_post_id) }}"
                                            class="active styling-edit">
                                            <i class="fa fa-pencil-square-o text-success text-active"></i>
                                        </a>
                                        <form action="{{ url('/delete-category-post/' . $cate_post->cate_post_id) }}"
                                            method="POST" style="display:inline-block;"
                                            onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này không?')">
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
                                    <td colspan="6">
                                        <div class="g-admin-empty-state">Chưa có danh mục bài viết nào.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <footer class="panel-footer">
                @include('admin.partials.pagination', ['paginator' => $category_post, 'label' => 'danh mục'])
            </footer>
        </section>
    </div>
@endsection
