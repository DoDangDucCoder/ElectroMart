@extends('admin_layout')

@section('admin_content')
    @php
        $statusLabels = [
            'new' => 'Mới',
            'read' => 'Đã đọc',
            'replied' => 'Đã phản hồi',
            'archived' => 'Lưu trữ',
        ];

        $statusClasses = [
            'new' => 'info',
            'read' => 'warning',
            'replied' => 'success',
            'archived' => 'default',
        ];
    @endphp

    <div class="g-admin-page-shell">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Hệ thống / Liên hệ</span>
                <h1>Liên hệ khách hàng</h1>
                <p>Quản lý các tin nhắn khách hàng gửi từ form liên hệ trên website.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($stats['total']) }}</strong>
                    <span>Tổng liên hệ</span>
                </article>
                <article>
                    <strong>{{ number_format($stats['new']) }}</strong>
                    <span>Chưa xử lý</span>
                </article>
                <article>
                    <strong>{{ number_format($stats['replied']) }}</strong>
                    <span>Đã phản hồi</span>
                </article>
            </div>
        </section>

        @if (session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Bộ lọc liên hệ</strong>
                    <p>Tìm theo tên, email, số điện thoại, chủ đề hoặc nội dung tin nhắn.</p>
                </div>
            </header>
            <div class="g-admin-panel-body">
                <form method="get" action="{{ route('admin.contact-messages.index') }}" class="g-admin-contact-filter">
                    <div class="g-admin-contact-filter-keyword">
                        <label for="q">Từ khóa</label>
                        <input type="search" name="q" id="q" value="{{ $filters['q'] }}" class="form-control" placeholder="Tên, email, chủ đề, nội dung...">
                    </div>
                    <div>
                        <label for="status">Trạng thái</label>
                        <select name="status" id="status" class="form-control">
                            <option value="all" @selected($filters['status'] === 'all')>Tất cả</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ $statusLabels[$status] ?? 'Không rõ' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="date_from">Từ ngày</label>
                        <input type="date" name="date_from" id="date_from" value="{{ $filters['date_from'] }}" class="form-control">
                    </div>
                    <div>
                        <label for="date_to">Đến ngày</label>
                        <input type="date" name="date_to" id="date_to" value="{{ $filters['date_to'] }}" class="form-control">
                    </div>

                    <div class="g-admin-contact-filter-actions">
                        <button type="submit" class="btn btn-info">
                            <i class="fa fa-search"></i> Lọc liên hệ
                        </button>
                        <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-default">Xóa lọc</a>
                    </div>
                </form>
            </div>
        </section>

        <section class="g-admin-panel-card" style="margin-top:18px;">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Danh sách liên hệ</strong>
                    <p>Hiển thị mới nhất trước, dữ liệu lấy từ bảng contact_messages.</p>
                </div>
                <span class="g-admin-status-chip">{{ number_format($messages->total()) }} kết quả</span>
            </header>
            <div class="g-admin-panel-body">
                @if ($messages->isEmpty())
                    <div class="g-admin-empty-state">
                        <h3>Chưa có tin nhắn liên hệ</h3>
                        <p>Khi khách hàng gửi form liên hệ ngoài website, dữ liệu sẽ xuất hiện tại đây.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width:90px;">Mã liên hệ</th>
                                    <th>Người gửi</th>
                                    <th>Chủ đề</th>
                                    <th>Nội dung</th>
                                    <th style="width:130px;">Trạng thái</th>
                                    <th style="width:150px;">Ngày gửi</th>
                                    <th style="width:190px;">Cập nhật</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($messages as $message)
                                    @php
                                        $status = $message->status ?: 'new';
                                        $createdAt = $message->created_at;
                                    @endphp
                                    <tr>
                                        <td>LH{{ str_pad((string) $message->id, 4, '0', STR_PAD_LEFT) }}</td>
                                        <td>
                                            <strong>{{ $message->name }}</strong><br>
                                            <a href="mailto:{{ $message->email }}">{{ $message->email }}</a>
                                            @if ($message->phone)
                                                <br><span>{{ $message->phone }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $message->subject ?: 'Không có chủ đề' }}</td>
                                        <td>
                                            <div style="max-width:360px;">
                                                {{ \Illuminate\Support\Str::limit((string) ($message->message ?? ''), 140) }}
                                            </div>
                                            <details style="margin-top:6px;">
                                                <summary>Xem đầy đủ</summary>
                                                <div style="white-space:pre-line;margin-top:6px;">{{ $message->message }}</div>
                                            </details>
                                        </td>
                                        <td>
                                            <span class="label label-{{ $statusClasses[$status] ?? 'default' }}">
                                                {{ $statusLabels[$status] ?? 'Không rõ' }}
                                            </span>
                                        </td>
                                        <td>{{ $createdAt ? $createdAt->format('d/m/Y H:i') : 'Không rõ' }}</td>
                                        <td>
                                            <form method="post" action="{{ route('admin.contact-messages.status', array_merge(['contactMessage' => $message], request()->query())) }}">
                                                @csrf
                                                @method('PATCH')
                                                <div style="display:flex;gap:6px;">
                                                    <select name="status" class="form-control input-sm" aria-label="Trạng thái liên hệ LH{{ str_pad((string) $message->id, 4, '0', STR_PAD_LEFT) }}">
                                                        @foreach ($statuses as $option)
                                                            <option value="{{ $option }}" @selected($status === $option)>{{ $statusLabels[$option] ?? $option }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @include('admin.partials.pagination', ['paginator' => $messages, 'label' => 'liên hệ'])
                @endif
            </div>
        </section>
    </div>
@endsection
