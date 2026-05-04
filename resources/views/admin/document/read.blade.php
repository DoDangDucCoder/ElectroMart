@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-document-page">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Tài liệu</span>
                <h1>Kho tài liệu</h1>
                <p>Quản lý danh sách tệp đang có trên nơi lưu trữ tài liệu hiện tại của hệ thống.</p>
            </div>

            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($contents->count()) }}</strong>
                    <span>Tệp trong bảng documents</span>
                </article>
                <article>
                    <strong>{{ $documentDiskLabel }}</strong>
                    <span>Disk hiện tại · {{ $documentDiskType }}</span>
                </article>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <div class="g-admin-panel-head">
                <div>
                    <div>Tải tài liệu lên</div>
                    <p>Tài liệu được lưu trong disk hiện tại và ghi metadata vào bảng documents.</p>
                </div>
            </div>
            <div class="g-admin-panel-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.documents.store') }}" method="post" enctype="multipart/form-data" class="g-admin-form-grid">
                    @csrf
                    <div class="form-group">
                        <label for="document">Tệp tài liệu</label>
                        <input type="file" name="document" id="document" class="form-control">
                        <span class="g-admin-cell-note">Hỗ trợ pdf, doc, docx, txt, csv, xls, xlsx, jpg, jpeg, png, webp. Tối đa 5MB.</span>
                    </div>
                    <div class="form-group">
                        <label for="category">Nhóm tài liệu</label>
                        <input type="text" name="category" id="category" class="form-control" value="{{ old('category') }}" placeholder="Ví dụ: hướng dẫn, nội bộ">
                    </div>
                    <div class="g-admin-inline-actions g-admin-form-span-2">
                        <button type="submit" class="btn btn-info">Tải lên</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="g-admin-panel-card" style="margin-top:18px;">
            <div class="g-admin-panel-head">
                <div>
                    <div>Danh sách tài liệu</div>
                    <p>Tải xuống, xem nhanh và xóa các tệp được quản lý trong bảng documents.</p>
                </div>
            </div>
            <div class="g-admin-panel-body">
                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if ($contents->isEmpty())
                    <div class="g-admin-empty-state">
                        Chưa có tệp nào trong kho tài liệu hiện tại hoặc nơi lưu trữ chưa có dữ liệu.
                    </div>
                @else
                    <div class="g-admin-document-grid">
                        @foreach ($contents as $document)
                            <article class="g-admin-document-card">
                                <div class="g-admin-document-preview">
                                    @if ($document['type'] === 'image' && $document['preview_url'])
                                        <img src="{{ $document['preview_url'] }}" alt="{{ $document['name'] }}">
                                    @else
                                        <div class="g-admin-document-icon">
                                            <i class="fa fa-file-text-o"></i>
                                        </div>
                                    @endif
                                </div>

                                <div class="g-admin-document-body">
                                    <h3>{{ $document['name'] }}</h3>
                                    <ul class="g-admin-note-list">
                                        <li>Đường dẫn: {{ $document['path'] }}</li>
                                        <li>Định dạng: {{ $document['mimetype'] }}</li>
                                        <li>Kích thước: {{ $document['size_label'] }}</li>
                                        @if (! empty($document['category']))
                                            <li>Nhóm: {{ $document['category'] }}</li>
                                        @endif
                                        <li>Cập nhật: {{ $document['last_modified_label'] }}</li>
                                    </ul>
                                </div>

                                <div class="g-admin-action-stack">
                                    <a href="{{ $document['download_url'] }}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-download"></i> Tải xuống
                                    </a>

                                    @if ($document['preview_url'])
                                        <a href="{{ $document['preview_url'] }}" target="_blank" class="btn btn-default btn-sm">
                                            <i class="fa fa-external-link"></i> Mở file
                                        </a>
                                    @endif

                                    <form action="{{ $document['delete_url'] }}" method="POST"
                                        onsubmit="return confirm('Bạn có chắc là muốn xóa tài liệu này không?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> Xóa file
                                        </button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
