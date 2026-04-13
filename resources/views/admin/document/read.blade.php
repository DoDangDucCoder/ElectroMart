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
                    <span>Tệp đang hiển thị</span>
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
                    <div>Danh sách tài liệu</div>
                    <p>Tải xuống, xem nhanh và xóa các tệp đang có trong kho tài liệu.</p>
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

                                    <form action="{{ url('delete_document/' . $document['path']) }}" method="POST"
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
