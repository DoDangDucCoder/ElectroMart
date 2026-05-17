@extends('admin_layout')

@section('admin_content')

@php
    $statusLabels = [
        'pending' => ['label' => 'Chờ duyệt', 'class' => 'amber'],
        'approved' => ['label' => 'Đã duyệt', 'class' => 'green'],
        'rejected' => ['label' => 'Từ chối', 'class' => 'red'],
    ];
@endphp

<div class="adm-page">
    <div class="adm-page-title">
        <h1><i class="fa fa-star" style="color:var(--adm-amber);margin-right:6px"></i>Quản lý đánh giá sản phẩm</h1>
        <div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> / Sản phẩm / Đánh giá</div>
    </div>

    @if (session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="adm-stats">
        <div class="adm-stat"><div class="icon blue"><i class="fa fa-comments"></i></div><div><div class="num">{{ number_format($stats['total']) }}</div><div class="lbl">Tổng đánh giá</div></div></div>
        <div class="adm-stat"><div class="icon amber"><i class="fa fa-clock-o"></i></div><div><div class="num">{{ number_format($stats['pending']) }}</div><div class="lbl">Chờ duyệt</div></div></div>
        <div class="adm-stat"><div class="icon green"><i class="fa fa-check-circle"></i></div><div><div class="num">{{ number_format($stats['approved']) }}</div><div class="lbl">Đã duyệt</div></div></div>
        <div class="adm-stat"><div class="icon red"><i class="fa fa-ban"></i></div><div><div class="num">{{ number_format($stats['rejected']) }}</div><div class="lbl">Từ chối</div></div></div>
        <div class="adm-stat"><div class="icon teal"><i class="fa fa-star"></i></div><div><div class="num">{{ number_format($stats['average_rating'], 1) }}/5</div><div class="lbl">Điểm trung bình</div></div></div>
    </div>

    <form method="GET" action="{{ route('admin.reviews') }}" class="adm-toolbar" style="align-items:flex-end;flex-wrap:wrap">
        <div class="adm-search" style="min-width:280px">
            <i class="fa fa-search"></i>
            <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Tìm sản phẩm, SKU, khách hàng, nội dung...">
        </div>
        <div>
            <label class="adm-filter-label" for="review-status">Trạng thái</label>
            <select id="review-status" name="status" class="adm-select">
                <option value="all" @selected($filters['status'] === 'all')>Tất cả</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ $statusLabels[$status]['label'] ?? $status }}</option>
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
        <div>
            <label class="adm-filter-label" for="review-per-page">Mỗi trang</label>
            <select id="review-per-page" name="per_page" class="adm-select">
                @foreach ([15, 25, 50] as $size)
                    <option value="{{ $size }}" @selected((int) $filters['per_page'] === $size)>{{ $size }}</option>
                @endforeach
            </select>
        </div>
        <button class="adm-btn primary" type="submit"><i class="fa fa-filter"></i> Lọc</button>
        <a class="adm-btn outline" href="{{ route('admin.reviews') }}"><i class="fa fa-refresh"></i> Xóa lọc</a>
    </form>

    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Mã</th>
                    <th>Sản phẩm</th>
                    <th>Khách hàng</th>
                    <th>Điểm</th>
                    <th>Nội dung</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reviews as $review)
                    @php
                        $status = $statusLabels[$review->status] ?? ['label' => $review->status, 'class' => 'gray'];
                        $customerName = $review->customer?->name ?? $review->author_name ?? 'Khách ẩn danh';
                        $customerEmail = $review->customer?->email ?? $review->author_email;
                    @endphp
                    <tr>
                        <td class="id">#{{ $review->id }}</td>
                        <td>
                            <div class="adm-product-cell">
                                <span class="p-name">{{ $review->product?->name ?? 'Sản phẩm đã xóa' }}</span>
                                @if ($review->product?->sku)
                                    <small style="display:block;color:var(--adm-muted)">SKU: {{ $review->product->sku }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="adm-user-cell">
                                <div class="u-avatar">{{ mb_substr($customerName, 0, 1) }}</div>
                                <span class="u-name">{{ $customerName }}</span>
                            </div>
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
                        <td style="min-width:260px;max-width:420px">
                            @if ($review->title)
                                <strong style="display:block;margin-bottom:4px">{{ $review->title }}</strong>
                            @endif
                            <span style="color:var(--adm-muted)">{{ \Illuminate\Support\Str::limit((string) ($review->content ?? ''), 180) }}</span>
                        </td>
                        <td><span class="adm-badge {{ $status['class'] }}">{{ $status['label'] }}</span></td>
                        <td style="white-space:nowrap">{{ $review->created_at?->format('d/m/Y H:i') }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.reviews.status', array_merge(['review' => $review], request()->query())) }}" style="display:flex;gap:6px;align-items:center">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="adm-select" style="height:32px;width:120px">
                                    @foreach ($statuses as $nextStatus)
                                        <option value="{{ $nextStatus }}" @selected($review->status === $nextStatus)>{{ $statusLabels[$nextStatus]['label'] ?? $nextStatus }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="adm-btn outline" style="height:32px;padding:0 10px"><i class="fa fa-check"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:36px;color:var(--adm-muted)">
                            <i class="fa fa-comments-o" style="font-size:28px;display:block;margin-bottom:8px"></i>
                            Chưa có đánh giá phù hợp với bộ lọc hiện tại.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="adm-pagination">
            <span class="info">
                Hiển thị {{ $reviews->firstItem() ?? 0 }} đến {{ $reviews->lastItem() ?? 0 }} của {{ number_format($reviews->total()) }} đánh giá
            </span>
            <div class="pages">{{ $reviews->links() }}</div>
        </div>
    </div>
</div>
@endsection
