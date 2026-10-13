@extends('admin_layout')

@section('admin_content')
@php
    $commentStats = $comment_stats ?? [
        'total' => method_exists($comment, 'total') ? $comment->total() : $comment->count(),
        'replies' => $comment_rep->count(),
        'approved' => collect($comment)->where('comment_status', 0)->count(),
        'pending' => collect($comment)->where('comment_status', 1)->count(),
    ];
@endphp

@once
    <style>
        .adm-comment-management{padding:24px 34px 30px;background:#f4f7fb;min-height:calc(100vh - 72px)}
        .adm-comment-management .adm-page-title{display:flex;gap:16px;align-items:center;margin:4px 0 24px}
        .adm-comment-management .adm-title-icon{width:58px;height:58px;border-radius:12px;background:#fff;border:1px solid var(--adm-border);box-shadow:0 6px 16px rgba(15,23,42,.08);display:grid;place-items:center;color:var(--adm-primary);font-size:25px}
        .adm-comment-management .adm-page-title h1{margin:0;font-size:28px;font-weight:800;color:var(--adm-text)}
        .adm-comment-management .adm-page-title .crumb{margin-top:6px;color:var(--adm-muted);font-size:14px}
        .adm-comment-management .adm-page-desc{margin-top:8px;color:var(--adm-muted)}
        .adm-comment-management .adm-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:18px;margin-bottom:20px}
        .adm-comment-management .adm-stat{height:96px;background:#fff;border:1px solid var(--adm-border);border-radius:10px;box-shadow:0 6px 16px rgba(15,23,42,.06);display:flex;flex-direction:row;align-items:center;justify-content:flex-start;gap:18px;padding:0 24px}
        .adm-comment-management .adm-stat .icon{width:58px;height:58px;border-radius:16px;display:grid;place-items:center;font-size:24px}
        .adm-comment-management .adm-stat .num{font-size:28px;font-weight:800;line-height:1;color:var(--adm-text)}
        .adm-comment-management .adm-stat .lbl{margin-top:6px;color:var(--adm-muted);font-size:14px}
        .adm-comment-management .adm-toolbar{background:#fff;border:1px solid var(--adm-border);border-radius:10px;box-shadow:0 6px 16px rgba(15,23,42,.05);padding:18px;margin-bottom:18px;display:grid;grid-template-columns:1.4fr .65fr auto auto;gap:18px;align-items:end}
        .adm-comment-management .adm-table-wrap{background:#fff;border:1px solid var(--adm-border);border-radius:10px;box-shadow:0 6px 16px rgba(15,23,42,.05);overflow:hidden}
        .adm-comment-management .adm-table-head{display:flex;justify-content:space-between;align-items:center;padding:18px 20px;border-bottom:1px solid var(--adm-border)}
        .adm-comment-management .adm-table-head h2{margin:0;font-size:18px;font-weight:800;color:var(--adm-text)}
        .adm-comment-management .adm-table{width:100%;table-layout:fixed;border-collapse:collapse}
        .adm-comment-management .adm-table th{height:48px;background:#f8fafc;color:var(--adm-muted);font-size:12px;text-transform:uppercase;letter-spacing:.03em;text-align:center;border-bottom:1px solid var(--adm-border)}
        .adm-comment-management .adm-table td{height:64px;padding:10px 12px;border-bottom:1px solid #edf2f7;text-align:center;vertical-align:middle;color:var(--adm-text)}
        .adm-comment-management .adm-sender{display:flex;align-items:center;justify-content:center;gap:10px;text-align:left}
        .adm-comment-management .u-avatar{width:34px;height:34px;border-radius:50%;background:#e8efff;color:var(--adm-primary);display:grid;place-items:center;font-weight:800;font-size:12px;flex:none}
        .adm-comment-management .adm-comment-body{text-align:left;line-height:1.45;color:#334155}
        .adm-comment-management .adm-comment-replies{margin:8px 0 0;padding:0;list-style:none;color:var(--adm-muted);font-size:12px}
        .adm-comment-management .adm-reply-box{margin-top:10px;display:flex;gap:8px}
        .adm-comment-management .adm-reply-box textarea{min-height:38px;resize:vertical}
        .adm-comment-management .adm-status{display:inline-flex;align-items:center;justify-content:center;min-width:86px;height:26px;border-radius:999px;font-size:12px;font-weight:700}
        .adm-comment-management .adm-status.green{background:#dcfce7;color:#16a34a}
        .adm-comment-management .adm-status.amber{background:#fef3c7;color:#d97706}
        .adm-comment-management .adm-action-btn{height:32px;border-radius:6px;padding:0 12px;border:1px solid var(--adm-border);background:#fff;font-weight:700}
        .adm-comment-management .adm-action-btn.primary{background:var(--adm-primary);border-color:var(--adm-primary);color:#fff}
        .adm-comment-management .adm-action-btn.danger{background:#fff1f2;border-color:#fecdd3;color:#dc2626}
        @media (max-width:1200px){.adm-comment-management .adm-stats{grid-template-columns:repeat(2,1fr)}.adm-comment-management .adm-toolbar{grid-template-columns:1fr 1fr}}
    </style>
@endonce

<div class="adm-page adm-comment-management">
    <div class="adm-page-title">
        <div class="adm-title-icon"><i class="fa fa-comments"></i></div>
        <div>
            <h1>Quản lý bình luận</h1>
            <div class="crumb"><a href="{{ url('/dashboard') }}">Trang chủ</a> / Khách hàng / Bình luận</div>
            <div class="adm-page-desc">Duyệt, ẩn và trả lời trực tiếp các bình luận của khách hàng.</div>
        </div>
    </div>

    <div id="notify_comment"></div>
    @if (session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="adm-stats">
        <div class="adm-stat" data-filter-field="status" data-filter-value="all"><div class="icon blue"><i class="fa fa-comment"></i></div><div><div class="num">{{ number_format($commentStats['total']) }}</div><div class="lbl">Bình luận gốc</div></div></div>
        <div class="adm-stat"><div class="icon purple"><i class="fa fa-reply"></i></div><div><div class="num">{{ number_format($commentStats['replies']) }}</div><div class="lbl">Phản hồi hiện có</div></div></div>
        <div class="adm-stat" data-filter-field="status" data-filter-value="approved"><div class="icon green"><i class="fa fa-check-circle"></i></div><div><div class="num">{{ number_format($commentStats['approved']) }}</div><div class="lbl">Đã duyệt</div></div></div>
        <div class="adm-stat" data-filter-field="status" data-filter-value="pending"><div class="icon amber"><i class="fa fa-clock-o"></i></div><div><div class="num">{{ number_format($commentStats['pending']) }}</div><div class="lbl">Chờ duyệt</div></div></div>
    </div>

    <form method="GET" action="{{ url('/comment') }}" class="adm-toolbar">
        <div>
            <label class="adm-filter-label">Tìm kiếm bình luận</label>
            <div class="adm-search"><i class="fa fa-search"></i><input type="text" name="q" value="{{ $filters['q'] ?? request('q') }}" placeholder="Tìm theo tên người gửi, nội dung bình luận..."></div>
        </div>
        <div>
            <label class="adm-filter-label">Trạng thái</label>
            <select name="status" class="adm-select">
                <option value="">Tất cả</option>
                <option value="approved" @selected(($filters['status'] ?? request('status')) === 'approved')>Đã duyệt</option>
                <option value="pending" @selected(($filters['status'] ?? request('status')) === 'pending')>Chờ duyệt</option>
            </select>
        </div>

        <button type="submit" class="adm-btn primary"><i class="fa fa-filter"></i> Lọc</button>
        <a href="{{ url('/comment') }}" class="adm-btn outline"><i class="fa fa-refresh"></i> Xóa lọc</a>
    </form>

    <div class="adm-table-wrap">
        <div class="adm-table-head">
            <h2>Danh sách bình luận</h2>
            <span class="adm-badge blue">{{ number_format(method_exists($comment, 'total') ? $comment->total() : $comment->count()) }} kết quả</span>
        </div>
        <table class="adm-table">
            <thead>
                <tr>
                    <th style="width:140px">Người gửi</th>
                    <th>Bình luận</th>
                    <th style="width:140px">@include('admin.partials.sort_header', ['label' => 'Ngày gửi', 'asc' => 'oldest', 'desc' => 'newest', 'filters' => $filters])</th>
                    <th>Sản phẩm</th>
                    <th style="width:120px">@include('admin.partials.sort_header', ['label' => 'Trạng thái', 'asc' => 'pending_first', 'desc' => 'newest', 'filters' => $filters])</th>
                    <th style="width:150px">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($comment as $comm)
                    <tr>
                        <td style="text-align:center;vertical-align:middle">
                            <strong>{{ $comm->comment_name }}</strong>
                        </td>
                        <td>
                            <div class="adm-comment-body">
                                {{ $comm->comment }}
                                <ul class="adm-comment-replies">
                                    @foreach ($comment_rep as $comm_reply)
                                        @if ($comm_reply->comment_parent_comment == $comm->comment_id)
                                            <li><i class="fa fa-reply"></i> {{ $comm_reply->comment }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                                @if ($comm->comment_status == 0)
                                    <div class="adm-reply-box">
                                        <textarea class="form-control reply_comment_{{ $comm->comment_id }}" rows="1" placeholder="Trả lời bình luận này"></textarea>
                                        <button class="adm-action-btn primary btn-reply-comment" type="button" data-product_id="{{ $comm->comment_product_id }}" data-comment_id="{{ $comm->comment_id }}">Trả lời</button>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td>{{ $comm->comment_date }}</td>
                        <td>
                            @if (! empty($comm->product?->product_slug))
                                <a href="{{ url('/chi-tiet/' . $comm->product->product_slug) }}" target="_blank">{{ $comm->product->product_name }}</a>
                            @else
                                <span class="text-muted">Không còn sản phẩm</span>
                            @endif
                        </td>
                        <td>
                            <span class="adm-status {{ $comm->comment_status == 0 ? 'green' : 'amber' }}">{{ $comm->comment_status == 0 ? 'Đã duyệt' : 'Chờ duyệt' }}</span>
                        </td>
                        <td>
                            @if ($comm->comment_status == 1)
                                <button type="button" data-comment_status="0" data-comment_id="{{ $comm->comment_id }}" id="{{ $comm->comment_product_id }}" class="adm-action-btn primary comment_duyet_btn">Duyệt</button>
                            @else
                                <button type="button" data-comment_status="1" data-comment_id="{{ $comm->comment_id }}" id="{{ $comm->comment_product_id }}" class="adm-action-btn danger comment_duyet_btn">Bỏ duyệt</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="padding:36px;color:var(--adm-muted)">Chưa có bình luận nào để xử lý.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if (method_exists($comment, 'links'))
            @include('admin.partials.pagination', ['paginator' => $comment, 'label' => 'bình luận'])
        @endif
    </div>
</div>
@endsection
