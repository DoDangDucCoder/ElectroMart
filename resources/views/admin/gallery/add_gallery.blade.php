@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-gallery-page">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Sản phẩm / Thư viện ảnh</span>
                <h1>Thư viện ảnh sản phẩm</h1>
                <p>Quản lý ảnh hiển thị cho sản phẩm đang chọn và cập nhật trực tiếp trong cùng một màn hình.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>#{{ $pro_id }}</strong>
                    <span>Mã sản phẩm đang thao tác</span>
                </article>
                <article>
                    <strong>Nhiều ảnh</strong>
                    <span>Upload nhiều ảnh cùng lúc</span>
                </article>
            </div>
        </section>

        @if (session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Tải ảnh và quản lý gallery</strong>
                    <p>Ảnh mới sẽ xuất hiện ngay trong danh sách phía dưới sau khi tải lên.</p>
                </div>
            </header>
            <div class="g-admin-panel-body">
                <form action="{{ url('/insert-gallery/' . $pro_id) }}" method="POST" enctype="multipart/form-data"
                    class="g-admin-gallery-upload">
                    @csrf
                    <div class="g-admin-toolbar">
                        <div class="form-group" style="flex:1 1 420px;">
                            <input type="file" class="form-control" id="file" name="file[]" accept="image/*" multiple>
                            <span id="error_gallery"></span>
                        </div>
                        <div class="g-admin-inline-actions">
                            <input type="submit" name="upload" value="Tải ảnh" class="btn btn-success">
                        </div>
                    </div>
                </form>

                <div class="g-admin-surface">
                    <h3>Thư viện ảnh hiện tại</h3>
                    <input type="hidden" value="{{ $pro_id }}" name="pro_id" class="pro_id">
                    <form>
                        @csrf
                        <div id="gallery_load"></div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
