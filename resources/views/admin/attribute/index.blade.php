@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-attribute-index">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Sản phẩm / Thuộc tính</span>
                <h1>Danh sách thuộc tính</h1>
                <p>Quản lý các thuộc tính dùng cho biến thể và cấu hình sản phẩm.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ $attributes->count() }}</strong>
                    <span>Tổng thuộc tính</span>
                </article>
            </div>
        </section>

        @if (session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Danh sách thuộc tính</strong>
                    <p>Cập nhật hoặc xóa thuộc tính trước khi dùng cho sản phẩm.</p>
                </div>
                <div class="g-admin-inline-actions">
                    <a href="{{ route('attribute.create') }}" class="btn btn-info">Thêm thuộc tính</a>
                </div>
            </header>
            <div class="g-admin-panel-body">
                <div class="table-responsive">
                    <table class="table g-admin-data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tên</th>
                                <th>Mô tả</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($attributes as $attr)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><strong>{{ $attr->name }}</strong></td>
                                    <td>{{ \Illuminate\Support\Str::limit((string) $attr->description, 140) }}</td>
                                    <td>
                                        <a href="{{ route('attribute.edit', $attr->id) }}" class="btn btn-primary">Sửa</a>
                                        <form action="{{ route('attribute.destroy', $attr->id) }}"
                                            onclick="return confirm('Bạn có chắc muốn xóa thuộc tính này không?')" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="g-admin-empty-state">Chưa có thuộc tính nào.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection
