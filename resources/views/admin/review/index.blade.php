@extends('admin_layout')

@section('admin_content')
@php
    $statusLabels = [
        'pending' => ['label' => 'Chờ duyệt', 'class' => 'amber'],
        'approved' => ['label' => 'Đã duyệt', 'class' => 'green'],
        'rejected' => ['label' => 'Từ chối', 'class' => 'red'],
    ];
@endphp

@once
    <style>
        .adm-review-management{padding:24px 34px 30px;background:#f4f7fb;min-height:calc(100vh - 72px)}
        .adm-review-management .adm-page-title{display:flex;gap:16px;align-items:center;margin:4px 0 24px}
        .adm-review-management .adm-title-icon{width:58px;height:58px;border-radius:12px;background:#fff;border:1px solid var(--adm-border);box-shadow:0 6px 16px rgba(15,23,42,.08);display:grid;place-items:center;color:var(--adm-primary);font-size:25px}
        .adm-review-management .adm-page-title h1{margin:0;font-size:28px;font-weight:800;color:var(--adm-text)}
        .adm-review-management .adm-page-title .crumb{margin-top:6px;color:var(--adm-muted);font-size:14px}
        .adm-review-management .adm-page-desc{margin-top:8px;color:var(--adm-muted)}
        .adm-review-management .adm-stats{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:18px;margin-bottom:20px}
        .adm-review-management .adm-stat{height:96px;background:#fff;border:1px solid var(--adm-border);border-radius:10px;box-shadow:0 6px 16px rgba(15,23,42,.06);display:flex;flex-direction:row;align-items:center;justify-content:flex-start;gap:18px;padding:0 20px}
        .adm-review-management .adm-stat .icon{width:58px;height:58px;border-radius:16px;display:grid;place-items:center;font-size:24px}
        .adm-review-management .adm-stat .num{font-size:27px;font-weight:800;line-height:1;color:var(--adm-text)}
        .adm-review-management .adm-stat .lbl{margin-top:6px;color:var(--adm-muted);font-size:14px}
        .adm-review-management .adm-toolbar{background:#fff;border:1px solid var(--adm-border);border-radius:10px;box-shadow:0 6px 16px rgba(15,23,42,.05);padding:18px;margin-bottom:18px}
        .adm-review-management .adm-toolbar-grid{display:grid;grid-template-columns:1.5fr .8fr .65fr .8fr .8fr;gap:18px;align-items:end}
        .adm-review-management .adm-filter-actions{display:flex;gap:10px;margin-top:12px}
        .adm-review-management .adm-table-wrap{background:#fff;border:1px solid var(--adm-border);border-radius:10px;box-shadow:0 6px 16px rgba(15,23,42,.05);overflow-x:auto}
        .adm-review-management .adm-table-head{display:flex;justify-content:space-between;align-items:center;padding:18px 20px;border-bottom:1px solid var(--adm-border)}
        .adm-review-management .adm-table-head h2{margin:0;font-size:18px;font-weight:800;color:var(--adm-text)}
        .adm-review-management .adm-table{width:100%;min-width:1280px;table-layout:fixed;border-collapse:collapse}
        .adm-review-management .adm-table th{height:48px;background:#f8fafc;color:var(--adm-muted);font-size:12px;text-transform:uppercase;letter-spacing:.03em;text-align:center;border-bottom:1px solid var(--adm-border);white-space:nowrap}
        .adm-review-management .adm-table td{height:78px;padding:10px 12px;border-bottom:1px solid #edf2f7;text-align:center;vertical-align:middle;color:var(--adm-text)}
        .adm-review-management .adm-table th:nth-child(1){width:70px}
        .adm-review-management .adm-table th:nth-child(2){width:310px}
        .adm-review-management .adm-table th:nth-child(3){width:210px}
        .adm-review-management .adm-table th:nth-child(4){width:110px}
        .adm-review-management .adm-table th:nth-child(5){width:300px}
        .adm-review-management .adm-table th:nth-child(6){width:120px}
        .adm-review-management .adm-table th:nth-child(7){width:135px}
        .adm-review-management .adm-table th:nth-child(8){width:150px}
        .adm-review-management .adm-product-cell{display:flex;align-items:center;justify-content:flex-start;gap:10px;text-align:left;min-width:0}
        .adm-review-management .adm-product-thumb{width:42px;height:42px;border-radius:8px;background:#f1f5f9;object-fit:cover;flex:none}
        .adm-review-management .adm-user-cell{display:flex;align-items:center;justify-content:center;gap:10px;text-align:center}
        .adm-review-management .u-avatar{width:34px;height:34px;border-radius:50%;background:#edf2f7;color:#475569;display:grid;place-items:center;font-weight:800;font-size:12px;flex:none}
        .adm-review-management .u-name,.adm-review-management .p-name{display:block;font-weight:800;color:var(--adm-text)}
        .adm-review-management .u-note,.adm-review-management .p-note{display:block;margin-top:3px;font-size:11px;color:var(--adm-muted)}
        .adm-review-management .p-name{line-height:1.3}
        .adm-review-management .p-note{line-height:1.25}
        .adm-review-management .adm-stars{white-space:nowrap;color:#f59e0b}
        .adm-review-management .adm-stars .empty{color:#d7dee8}
        .adm-review-management .adm-review-content{text-align:left;color:#475569;line-height:1.45;max-width:280px;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;word-break:normal;overflow-wrap:anywhere}
        .adm-review-management .adm-status{display:inline-flex;align-items:center;justify-content:center;min-width:86px;height:26px;border-radius:999px;font-size:12px;font-weight:700}
        .adm-review-management .adm-status.green{background:#dcfce7;color:#16a34a}
        .adm-review-management .adm-status.amber{background:#fef3c7;color:#d97706}
        .adm-review-management .adm-status.red{background:#fee2e2;color:#dc2626}
        .adm-review-management .adm-review-action{display:flex;gap:8px;align-items:center;justify-content:center}
        .adm-review-management .adm-review-action select{height:34px;width:110px}
        .adm-review-management .adm-review-action button{height:34px;min-width:38px;padding:0;border-radius:6px}
        @media (max-width:1300px){.adm-review-management .adm-stats{grid-template-columns:repeat(3,1fr)}.adm-review-management .adm-toolbar-grid{grid-template-columns:repeat(2,1fr)}}
    </style>
@endonce

<div class="adm-page adm-review-management">
    <div class="adm-page-title">
        <div class="adm-title-icon"><i class="fa fa-star"></i></div>
        <div>
            <h1>Quản lý đánh giá sản phẩm</h1>
            <div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> / Khách hàng / Đánh giá</div>
            <div class="adm-page-desc">Xem, duyệt và quản lý đánh giá sản phẩm từ khách hàng.</div>
        </div>
    </div>

    @if (session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="adm-stats">
        <div class="adm-stat" data-filter-field="status" data-filter-value="all"><div class="icon blue"><i class="fa fa-comments"></i></div><div><div class="num">{{ number_format($stats['total']) }}</div><div class="lbl">Tổng đánh giá</div></div></div>
        <div class="adm-stat" data-filter-field="status" data-filter-value="pending"><div class="icon amber"><i class="fa fa-clock-o"></i></div><div><div class="num">{{ number_format($stats['pending']) }}</div><div class="lbl">Chờ duyệt</div></div></div>
        <div class="adm-stat" data-filter-field="status" data-filter-value="approved"><div class="icon green"><i class="fa fa-check-circle"></i></div><div><div class="num">{{ number_format($stats['approved']) }}</div><div class="lbl">Đã duyệt</div></div></div>
        <div class="adm-stat" data-filter-field="status" data-filter-value="rejected"><div class="icon red"><i class="fa fa-ban"></i></div><div><div class="num">{{ number_format($stats['rejected']) }}</div><div class="lbl">Từ chối</div></div></div>
        <div class="adm-stat"><div class="icon teal"><i class="fa fa-star"></i></div><div><div class="num">{{ number_format($stats['average_rating'], 1) }}/5</div><div class="lbl">Điểm trung bình</div></div></div>
    </div>

    <form method="GET" action="{{ route('admin.reviews') }}" class="adm-toolbar">
        <div class="adm-toolbar-grid">
            <div>
                <label class="adm-filter-label">Từ khóa</label>
                <div class="adm-search"><i class="fa fa-search"></i><input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Tìm theo sản phẩm, khách hàng, nội dung..."></div>
            </div>
            <div>
                <label class="adm-filter-label" for="review-status">Trạng thái</label>
                <select id="review-status" name="status" class="adm-select">
                    <option value="all" @selected($filters['status'] === 'all')>Tất cả</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ $statusLabels[$status]['label'] ?? 'Không rõ' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="adm-filter-label" for="review-rating">Số sao</label>
                <select id="review-rating" name="rating" class="adm-select">
                    <option value="all" @selected($filters['rating'] === 'all')>Tất cả</option>
                    @for ($rating = 5; $rating >= 1; $rating--)
                        <option value="{{ $rating }}" @selected((string) $rating === $filters['rating'])>{{ $rating }} sao</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="adm-filter-label" for="review-date-from">Từ ngày</label>
                <input id="review-date-from" type="date" name="date_from" value="{{ $filters['date_from'] }}" class="adm-select">
            </div>
            <div>
                <label class="adm-filter-label" for="review-date-to">Đến ngày</label>
                <input id="review-date-to" type="date" name="date_to" value="{{ $filters['date_to'] }}" class="adm-select">
            </div>

        </div>
        <div class="adm-filter-actions">
            <button class="adm-btn primary" type="submit"><i class="fa fa-filter"></i> Lọc</button>
            <a class="adm-btn outline" href="{{ route('admin.reviews') }}"><i class="fa fa-refresh"></i> Xóa lọc</a>
        </div>
    </form>

    <div class="adm-table-wrap">
        <div class="adm-table-head">
            <h2>Danh sách đánh giá</h2>
            <span class="adm-badge blue">{{ number_format($reviews->total()) }} kết quả</span>
        </div>
        <table class="adm-table">
            <thead>
                <tr>
                    <th style="width:70px">Mã ĐG</th>
                    <th>Sản phẩm</th>
                    <th>Khách hàng</th>
                    <th style="width:110px">@include('admin.partials.sort_header', ['label' => 'Điểm đánh giá', 'asc' => 'rating_asc', 'desc' => 'rating_desc', 'filters' => $filters])</th>
                    <th>Nội dung</th>
                    <th style="width:120px">Trạng thái</th>
                    <th style="width:130px">@include('admin.partials.sort_header', ['label' => 'Ngày tạo', 'asc' => 'oldest', 'desc' => 'newest', 'filters' => $filters])</th>
                    <th style="width:150px">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reviews as $review)
                    @php
                        $status = $statusLabels[$review->status] ?? ['label' => 'Không rõ', 'class' => 'gray'];
                        $customerName = $review->customer?->name ?? $review->author_name ?? 'Khách ẩn danh';
                        $customerEmail = $review->customer?->email ?? $review->author_email;
                        $thumb = $review->product?->thumbnail_path ? asset($review->product->thumbnail_path) : null;
                    @endphp
                    <tr>
                        <td><strong>DG{{ str_pad((string) $review->id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                        <td>
                            <div class="adm-product-cell">
                                @if ($thumb)
                                    <img class="adm-product-thumb" src="{{ $thumb }}" alt="{{ $review->product?->name }}">
                                @endif
                                <div>
                                    <span class="p-name">{{ $review->product?->name ?? 'Sản phẩm đã xóa' }}</span>
                                    @if ($review->product?->sku)
                                        <span class="p-note">Mã tồn kho: {{ $review->product->sku }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="text-align:center;vertical-align:middle">
                            <strong>{{ $customerName }}</strong>
                            @if ($customerEmail)
                                <small style="display:block;color:var(--adm-muted);margin-top:4px">{{ $customerEmail }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="adm-stars">
                                @for ($star = 1; $star <= 5; $star++)
                                    <i class="fa fa-star {{ $star <= (int) $review->rating ? '' : 'empty' }}"></i>
                                @endfor
                            </div>
                        </td>
                        <td><div class="adm-review-content">{{ \Illuminate\Support\Str::limit((string) (($review->title ? $review->title . ' - ' : '') . ($review->content ?? '')), 120) }}</div></td>
                        <td><span class="adm-status {{ $status['class'] }}">{{ $status['label'] }}</span></td>
                        <td>{{ $review->created_at?->format('d/m/Y H:i') }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.reviews.status', array_merge(['review' => $review], request()->query())) }}" class="adm-review-action">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="adm-select">
                                    @foreach ($statuses as $nextStatus)
                                        <option value="{{ $nextStatus }}" @selected($review->status === $nextStatus)>{{ $statusLabels[$nextStatus]['label'] ?? 'Không rõ' }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="adm-btn primary"><i class="fa fa-check"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="padding:36px;color:var(--adm-muted)">Chưa có đánh giá phù hợp với bộ lọc hiện tại.</td></tr>
                @endforelse
            </tbody>
        </table>
        @include('admin.partials.pagination', ['paginator' => $reviews, 'label' => 'đánh giá'])
    </div>
</div>
@endsection
