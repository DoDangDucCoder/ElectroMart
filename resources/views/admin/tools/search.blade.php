@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-admin-search">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Tìm kiếm nhanh</span>
                <h1>Tìm đúng màn hình quản trị</h1>
                <p>Nhập tên màn hình, nhóm chức năng hoặc tác vụ để đi thẳng tới khu quản trị đang hoạt động.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($results->count()) }}</strong>
                    <span>{{ $query !== '' ? 'Kết quả khớp' : 'Gợi ý nhanh' }}</span>
                </article>
                <article>
                    <strong>{{ number_format($availableCount) }}</strong>
                    <span>Màn hình đang truy cập được</span>
                </article>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Nhập từ khóa</strong>
                    <p>Ví dụ: sản phẩm, đơn hàng, mã giảm giá, phân quyền, chat, báo cáo.</p>
                </div>
            </header>
            <div class="g-admin-panel-body">
                <form action="{{ route('admin.tools.search') }}" method="GET" class="g-admin-workspace-search-form" autocomplete="off">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="admin_workspace_query">Từ khóa</label>
                        <input id="admin_workspace_query" type="search" name="q" class="form-control"
                            value="{{ $query }}" placeholder="Nhập từ khóa tìm trong admin">
                    </div>
                    <div class="g-admin-inline-actions">
                        <button type="submit" class="btn btn-info">Tìm kiếm</button>
                        <a href="{{ route('admin.tools.search') }}" class="btn btn-default">Xóa từ khóa</a>
                    </div>
                </form>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>{{ $query !== '' ? 'Kết quả tìm kiếm' : 'Lối tắt phổ biến' }}</strong>
                    <p>
                        @if ($query !== '')
                            Từ khóa hiện tại: <strong>{{ $query }}</strong>
                        @else
                            Chưa nhập từ khóa, hệ thống đang gợi ý những màn hình truy cập nhanh phù hợp với tài khoản này.
                        @endif
                    </p>
                </div>
            </header>
            <div class="g-admin-panel-body">
                @if ($results->isEmpty())
                    <div class="g-admin-empty-state">
                        Không tìm thấy màn hình quản trị phù hợp với từ khóa này. Hãy thử bằng các cụm như
                        <code>sản phẩm</code>, <code>đơn hàng</code>, <code>mã giảm giá</code> hoặc <code>hộp thư chat</code>.
                    </div>
                @else
                    <div class="g-admin-workspace-grid">
                        @foreach ($results as $destination)
                            <a href="{{ $destination['url'] }}" class="g-admin-workspace-card">
                                <span class="g-admin-workspace-icon">
                                    <i class="fa {{ $destination['icon'] }}" aria-hidden="true"></i>
                                </span>
                                <span class="g-admin-workspace-copy">
                                    <strong>{{ $destination['title'] }}</strong>
                                    <small>{{ $destination['description'] }}</small>
                                    <span class="g-admin-workspace-section">{{ $destination['section'] }}</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        @if ($query !== '' && $results->isNotEmpty() && $popularDestinations->isNotEmpty())
            <section class="g-admin-panel-card">
                <header class="g-admin-panel-head">
                    <div>
                        <strong>Gợi ý thêm</strong>
                        <p>Một số màn hình phổ biến khác đang mở được với tài khoản hiện tại.</p>
                    </div>
                </header>
                <div class="g-admin-panel-body">
                    <div class="g-admin-workspace-grid">
                        @foreach ($popularDestinations as $destination)
                            <a href="{{ $destination['url'] }}" class="g-admin-workspace-card">
                                <span class="g-admin-workspace-icon">
                                    <i class="fa {{ $destination['icon'] }}" aria-hidden="true"></i>
                                </span>
                                <span class="g-admin-workspace-copy">
                                    <strong>{{ $destination['title'] }}</strong>
                                    <small>{{ $destination['description'] }}</small>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </div>
@endsection
