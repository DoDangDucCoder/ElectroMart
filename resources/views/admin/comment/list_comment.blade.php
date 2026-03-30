@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-comment-list">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Sản phẩm / Bình luận</span>
                <h1>Liệt kê bình luận</h1>
                <p>Theo dõi, duyệt và phản hồi bình luận của khách hàng ngay trên một màn hình.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ $comment->count() }}</strong>
                    <span>Bình luận gốc</span>
                </article>
                <article>
                    <strong>{{ $comment_rep->count() }}</strong>
                    <span>Reply hiện có</span>
                </article>
            </div>
        </section>

        <section class="g-admin-panel-card">
            <header class="g-admin-panel-head">
                <div>
                    <strong>Danh sách bình luận</strong>
                    <p>Duyệt, ẩn và trả lời trực tiếp từ danh sách này.</p>
                </div>
            </header>
            <div class="g-admin-panel-body">
                <div id="notify_comment"></div>

                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped b-t b-light g-admin-data-table" id="myTable">
                        <thead>
                            <tr>
                                <th>Duyệt</th>
                                <th>Tên người gửi</th>
                                <th>Bình luận</th>
                                <th>Ngày gửi</th>
                                <th>Sản phẩm</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($comment as $comm)
                                <tr>
                                    <td>
                                        @if ($comm->comment_status == 1)
                                            <input type="button" data-comment_status="0"
                                                data-comment_id="{{ $comm->comment_id }}" id="{{ $comm->comment_product_id }}"
                                                class="btn btn-primary btn-xs comment_duyet_btn" value="Duyệt">
                                        @else
                                            <input type="button" data-comment_status="1"
                                                data-comment_id="{{ $comm->comment_id }}" id="{{ $comm->comment_product_id }}"
                                                class="btn btn-danger btn-xs comment_duyet_btn" value="Bỏ duyệt">
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $comm->comment_name }}</strong>
                                    </td>
                                    <td>
                                        <div class="g-admin-comment-thread">
                                            <p class="g-admin-comment-body">{{ $comm->comment }}</p>

                                            <ul class="g-admin-comment-replies">
                                                @foreach ($comment_rep as $comm_reply)
                                                    @if ($comm_reply->comment_parent_comment == $comm->comment_id)
                                                        <li>{{ $comm_reply->comment }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>

                                            @if ($comm->comment_status == 0)
                                                <div class="g-admin-comment-reply-box">
                                                    <textarea class="form-control reply_comment_{{ $comm->comment_id }}" rows="5"
                                                        placeholder="Trả lời bình luận này"></textarea>
                                                    <button class="btn btn-default btn-xs btn-reply-comment"
                                                        data-product_id="{{ $comm->comment_product_id }}"
                                                        data-comment_id="{{ $comm->comment_id }}">Trả lời bình luận</button>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $comm->comment_date }}</td>
                                    <td>
                                        @if (! empty($comm->product?->product_slug))
                                            <a href="{{ url('/chi-tiet/' . $comm->product->product_slug) }}"
                                                target="_blank">{{ $comm->product->product_name }}</a>
                                        @else
                                            <span class="text-muted">Không còn sản phẩm</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($comm->comment_status == 0)
                                            <span class="label label-success">Đã duyệt</span>
                                        @else
                                            <span class="label label-warning">Chờ duyệt</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="g-admin-empty-state">Chưa có bình luận nào để xử lý.</div>
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
