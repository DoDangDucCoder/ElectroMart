@extends('layouts.home-tech')
@section('body_class', 'g-checkout-redesign ors-page')

@php
    $hasResult = !empty($trackingResult);
    $result = $trackingResult ?? null;
@endphp

@section('content')
<style>
body.ors-page{background:#f3f5f7;font-family:'Inter',-apple-system,sans-serif}
.otr-wrap{max-width:1180px;margin:0 auto;padding:0 16px 40px}
.otr-crumb{font-size:13px;color:#6b7280;padding:14px 0;display:flex;align-items:center;gap:8px}
.otr-crumb a{color:#6b7280;text-decoration:none}.otr-crumb a:hover{color:#0d9488}
.otr-crumb .sep{color:#cbd5e1}.otr-crumb .current{color:#111827;font-weight:500}
.otr-title{margin-bottom:8px}
.otr-title h1{font-size:26px;font-weight:800;color:#0f172a;margin:0 0 6px}
.otr-title p{font-size:14px;color:#6b7280;margin:0}
.otr-search-card{background:#fff;border-radius:12px;border:1px solid #e5e7eb;padding:28px;margin:20px 0 28px;display:flex;align-items:flex-end;gap:16px}
.otr-search-card .field{flex:1}
.otr-search-card label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px}
.otr-search-card input{width:100%;height:44px;border:1px solid #e5e7eb;border-radius:8px;padding:0 14px;font-size:14px;color:#111827;background:#fff}
.otr-search-card input:focus{outline:none;border-color:#0d9488;box-shadow:0 0 0 3px rgba(13,148,136,.12)}
.otr-search-card .icon{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:14px;pointer-events:none}
.otr-search-card .field-inner{position:relative}
.otr-search-card .field-inner input{padding-left:38px}
.otr-search-btn{height:44px;padding:0 28px;background:#0d9488;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;white-space:nowrap;transition:background .2s}
.otr-search-btn:hover{background:#0f766e}
.otr-card{background:#fff;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,.04);padding:24px;margin-bottom:20px}
.otr-result-grid{display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:flex-start}
@media(max-width:991px){.otr-result-grid{grid-template-columns:1fr}}
.otr-result-head{font-size:18px;font-weight:700;color:#111827;margin:0 0 20px}
.otr-result-head span{color:#0d9488}
.otr-summary-row{display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:24px}
@media(max-width:768px){.otr-summary-row{grid-template-columns:repeat(2,1fr)}}
.otr-summary-item{padding:14px;border-radius:10px;background:#f8fafc;border:1px solid #e5e7eb}
.otr-summary-item .lbl{font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.3px;margin-bottom:4px;display:flex;align-items:center;gap:6px}
.otr-summary-item .lbl i{font-size:13px;color:#0d9488}
.otr-summary-item .val{font-size:13.5px;font-weight:700;color:#111827}
.otr-summary-item .val.teal{color:#0d9488}
.otr-summary-item .val.price{color:#dc2626;font-size:15px}
/* Timeline */
.otr-timeline{display:flex;align-items:flex-start;gap:0;margin-bottom:28px;overflow-x:auto;padding:8px 0}
.otr-tl-step{display:flex;flex-direction:column;align-items:center;min-width:130px;flex:1;position:relative}
.otr-tl-dot{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;background:#fff;border:2px solid #e5e7eb;color:#9ca3af;z-index:2;margin-bottom:8px}
.otr-tl-step.done .otr-tl-dot{background:#0d9488;border-color:#0d9488;color:#fff}
.otr-tl-step.active .otr-tl-dot{background:#0d9488;border-color:#0d9488;color:#fff;box-shadow:0 0 0 4px rgba(13,148,136,.15)}
.otr-tl-label{font-size:11.5px;font-weight:600;color:#9ca3af;text-align:center}
.otr-tl-step.done .otr-tl-label,.otr-tl-step.active .otr-tl-label{color:#0d9488}
.otr-tl-date{font-size:10px;color:#9ca3af;text-align:center;margin-top:2px}
.otr-tl-line{flex:1;height:3px;background:#e5e7eb;margin-top:15px;min-width:30px}
.otr-tl-line.done{background:#0d9488}
/* Info cards */
.otr-info-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px}
@media(max-width:768px){.otr-info-grid{grid-template-columns:1fr}}
.otr-info-card{border:1px solid #e5e7eb;border-radius:10px;padding:18px}
.otr-info-card h4{font-size:13px;font-weight:700;color:#111827;margin:0 0 12px;text-transform:uppercase;letter-spacing:.3px}
.otr-info-card p{font-size:13px;color:#374151;margin:0 0 4px;line-height:1.5}
.otr-info-card .highlight{background:#f8fafc;border:1px solid #e5e7eb;border-radius:8px;padding:12px;margin-top:10px}
.otr-info-card .grand-total{font-size:18px;font-weight:800;color:#dc2626}
/* Product table */
.otr-table{width:100%;border-collapse:collapse;font-size:13px}
.otr-table thead th{background:#f8fafc;padding:10px 12px;text-align:left;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb;font-size:12px;text-transform:uppercase;letter-spacing:.3px}
.otr-table tbody td{padding:10px 12px;border-bottom:1px solid #f3f4f6;color:#374151;vertical-align:middle}
.otr-table .prod-cell{display:flex;align-items:center;gap:10px}
.otr-table .prod-img{width:40px;height:40px;border-radius:6px;border:1px solid #e5e7eb;overflow:hidden;flex-shrink:0}
.otr-table .prod-img img{width:100%;height:100%;object-fit:cover}
/* Right sidebar */
.otr-aside-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;margin-bottom:16px}
.otr-aside-card h4{font-size:14px;font-weight:700;color:#111827;margin:0 0 12px}
.otr-aside-contact{list-style:none;padding:0;margin:0}
.otr-aside-contact li{display:flex;align-items:center;gap:10px;padding:8px 0;font-size:13px;color:#374151}
.otr-aside-contact li+li{border-top:1px solid #f3f4f6}
.otr-aside-contact li i{width:32px;height:32px;border-radius:8px;background:#f0fdfa;color:#0d9488;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0}
.otr-aside-contact li strong{color:#111827}
.otr-aside-contact li a{color:#0d9488;font-weight:600;text-decoration:none}
.otr-chat-btn{width:100%;height:42px;background:#0d9488;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;margin-top:12px;display:flex;align-items:center;justify-content:center;gap:8px;transition:background .2s}
.otr-chat-btn:hover{background:#0f766e}
.otr-faq{list-style:none;padding:0;margin:0}
.otr-faq li{padding:8px 0;font-size:13px;border-bottom:1px solid #f3f4f6}
.otr-faq li a{color:#374151;text-decoration:none;display:flex;align-items:center;gap:6px}
.otr-faq li a:hover{color:#0d9488}
.otr-faq li a i{color:#9ca3af;font-size:11px}
.otr-faq .see-all{color:#0d9488;font-weight:600;border-bottom:none}
/* Policy */
.otr-policy{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px 24px;display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:32px}
@media(max-width:768px){.otr-policy{grid-template-columns:repeat(2,1fr)}}
.otr-policy-item{display:flex;gap:12px;align-items:flex-start}
.otr-policy-icon{width:40px;height:40px;border-radius:10px;background:#f0fdfa;color:#0d9488;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.otr-policy-title{font-size:13px;font-weight:700;color:#111827}
.otr-policy-sub{font-size:11.5px;color:#6b7280;line-height:1.4;margin-top:2px}
</style>

<div class="otr-wrap">
    <div class="otr-crumb">
        <a href="{{ url('/') }}">Trang chủ</a><span class="sep">›</span>
        <span class="current">Kiểm tra đơn hàng</span>
    </div>

    <div class="otr-title">
        <h1>Kiểm tra đơn hàng</h1>
        <p>Nhập mã đơn hàng và số điện thoại hoặc email để xem trạng thái đơn hàng của bạn.</p>
    </div>

    <form class="otr-search-card" method="GET" action="{{ url('/kiem-tra-don-hang') }}">
        <div class="field">
            <label>Mã đơn hàng</label>
            <div class="field-inner">
                <span class="icon"><i class="fa fa-file-text-o"></i></span>
                <input type="text" name="code" placeholder="VD: DD250520001" value="{{ request('code') }}">
            </div>
        </div>
        <div class="field">
            <label>Số điện thoại hoặc email</label>
            <div class="field-inner">
                <span class="icon"><i class="fa fa-phone"></i></span>
                <input type="text" name="contact" placeholder="Nhập số điện thoại hoặc email" value="{{ request('contact') }}">
            </div>
        </div>
        <button type="submit" class="otr-search-btn"><i class="fa fa-search"></i> Kiểm tra đơn hàng</button>
    </form>

    @if ($hasResult)
    <div class="otr-result-grid">
        <div>
            <div class="otr-card">
                <h2 class="otr-result-head">Kết quả đơn hàng <span>#{{ $result['code'] }}</span></h2>

                <div class="otr-summary-row">
                    <div class="otr-summary-item"><div class="lbl"><i class="fa fa-info-circle"></i> Trạng thái hiện tại</div><div class="val teal">{{ $result['status_label'] }}</div></div>
                    <div class="otr-summary-item"><div class="lbl"><i class="fa fa-calendar"></i> Ngày đặt hàng</div><div class="val">{{ $result['date'] }}</div></div>
                    <div class="otr-summary-item"><div class="lbl"><i class="fa fa-credit-card"></i> Phương thức thanh toán</div><div class="val">{{ $result['payment_method'] }}</div></div>
                    <div class="otr-summary-item"><div class="lbl"><i class="fa fa-money"></i> Tổng thanh toán</div><div class="val price">{{ number_format($result['total'], 0, ',', '.') }}đ</div></div>
                    <div class="otr-summary-item"><div class="lbl"><i class="fa fa-truck"></i> Dự kiến giao hàng</div><div class="val">{{ $result['estimated_delivery'] }}</div></div>
                </div>

                {{-- Timeline --}}
                <div class="otr-timeline">
                    @foreach ($result['timeline'] as $i => $step)
                        @if ($i > 0)<div class="otr-tl-line {{ $step['done'] ? 'done' : '' }}"></div>@endif
                        <div class="otr-tl-step {{ $step['done'] ? ($step['active'] ?? false ? 'active' : 'done') : '' }}">
                            <div class="otr-tl-dot"><i class="fa {{ $step['icon'] }}"></i></div>
                            <div class="otr-tl-label">{{ $step['label'] }}</div>
                            @if (!empty($step['date']))<div class="otr-tl-date">{{ $step['date'] }}</div>@endif
                        </div>
                    @endforeach
                </div>

                {{-- Info cards --}}
                <div class="otr-info-grid">
                    <div class="otr-info-card">
                        <h4>Thông tin nhận hàng</h4>
                        <p><strong>{{ $result['shipping']['name'] }}</strong></p>
                        <p><i class="fa fa-phone"></i> {{ $result['shipping']['phone'] }}</p>
                        <p><i class="fa fa-map-marker"></i> {{ $result['shipping']['address'] }}</p>
                    </div>
                    <div class="otr-info-card">
                        <h4>Thông tin vận chuyển</h4>
                        <p><strong>{{ $result['carrier']['name'] }}</strong></p>
                        <p>Mã vận đơn: <strong>{{ $result['carrier']['tracking_code'] }}</strong></p>
                        <p>Cập nhật gần nhất: {{ $result['carrier']['last_update'] }}</p>
                        <a href="#" style="display:inline-block;margin-top:8px;padding:6px 14px;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;color:#374151;text-decoration:none;font-weight:600">Theo dõi chi tiết</a>
                    </div>
                    <div class="otr-info-card">
                        <h4>Tóm tắt thanh toán</h4>
                        <p>Tạm tính: <span style="float:right">{{ number_format($result['totals']['subtotal'], 0, ',', '.') }}đ</span></p>
                        <p>Phí vận chuyển: <span style="float:right">{{ number_format($result['totals']['shipping'], 0, ',', '.') }}đ</span></p>
                        @if ($result['totals']['discount'] > 0)
                        <p>Giảm giá: <span style="float:right;color:#16a34a">-{{ number_format($result['totals']['discount'], 0, ',', '.') }}đ</span></p>
                        @endif
                        <div class="highlight">
                            <div style="display:flex;justify-content:space-between;align-items:center">
                                <span style="font-weight:700">Tổng cộng</span>
                                <span class="grand-total">{{ number_format($result['totals']['total'], 0, ',', '.') }}đ</span>
                            </div>
                            <div style="font-size:11px;color:#9ca3af;text-align:right">(Đã bao gồm VAT)</div>
                        </div>
                    </div>
                </div>

                {{-- Products table --}}
                <h4 style="font-size:14px;font-weight:700;margin:0 0 12px">Sản phẩm trong đơn</h4>
                <table class="otr-table">
                    <thead><tr><th>Sản phẩm</th><th>Số lượng</th><th>Đơn giá</th><th>Thành tiền</th></tr></thead>
                    <tbody>
                        @foreach ($result['items'] as $item)
                        @php $img = !empty($item['image']) ? asset($item['image']) : asset('uploads/product/no-image.svg'); @endphp
                        <tr>
                            <td><div class="prod-cell"><div class="prod-img"><img src="{{ $img }}" alt=""></div><span>{{ $item['name'] }}</span></div></td>
                            <td>x {{ $item['quantity'] }}</td>
                            <td>{{ number_format($item['price'], 0, ',', '.') }}đ</td>
                            <td style="font-weight:600">{{ number_format($item['subtotal'], 0, ',', '.') }}đ</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- RIGHT sidebar --}}
        <aside>
            <div class="otr-aside-card">
                <h4>Bạn cần hỗ trợ?</h4>
                <ul class="otr-aside-contact">
                    <li><i class="fa fa-phone"></i><div><strong>Hotline</strong><br><span style="color:#0d9488;font-weight:700;font-size:16px">1900 1234</span><br><small style="color:#9ca3af">8:00 - 21:00 (Cả T7, CN)</small></div></li>
                    <li><i class="fa fa-envelope"></i><div><strong>Email</strong><br><a href="mailto:support@dangduc.vn">support@dangduc.vn</a><br><small style="color:#9ca3af">Phản hồi trong 30 phút</small></div></li>
                </ul>
                <button type="button" class="otr-chat-btn"><i class="fa fa-comments"></i> Chat ngay</button>
            </div>

            <div class="otr-aside-card">
                <h4>Câu hỏi thường gặp</h4>
                <ul class="otr-faq">
                    <li><a href="#"><i class="fa fa-angle-right"></i> Tôi không tìm thấy đơn hàng?</a></li>
                    <li><a href="#"><i class="fa fa-angle-right"></i> Bao lâu đơn hàng được cập nhật?</a></li>
                    <li><a href="#"><i class="fa fa-angle-right"></i> Tôi có thể hủy đơn không?</a></li>
                    <li class="see-all"><a href="#"><i class="fa fa-arrow-right"></i> Xem tất cả câu hỏi</a></li>
                </ul>
            </div>
        </aside>
    </div>
    @endif

    <div class="otr-policy">
        <div class="otr-policy-item"><span class="otr-policy-icon"><i class="fa fa-truck"></i></span><div><div class="otr-policy-title">Miễn phí giao hàng</div><div class="otr-policy-sub">Áp dụng cho đơn hàng công nghệ đủ điều kiện</div></div></div>
        <div class="otr-policy-item"><span class="otr-policy-icon"><i class="fa fa-refresh"></i></span><div><div class="otr-policy-title">Đổi trả dễ dàng</div><div class="otr-policy-sub">Hỗ trợ đổi trả trong 7 ngày nếu có lỗi từ nhà sản xuất</div></div></div>
        <div class="otr-policy-item"><span class="otr-policy-icon"><i class="fa fa-shield"></i></span><div><div class="otr-policy-title">Thanh toán an toàn</div><div class="otr-policy-sub">Bảo vệ thông tin thanh toán của bạn tuyệt đối</div></div></div>
        <div class="otr-policy-item"><span class="otr-policy-icon"><i class="fa fa-headphones"></i></span><div><div class="otr-policy-title">Hỗ trợ tư vấn 24/7</div><div class="otr-policy-sub">Tư vấn chọn laptop, điện thoại và phụ kiện phù hợp nhu cầu</div></div></div>
    </div>
</div>
@endsection
