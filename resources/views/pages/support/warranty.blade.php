@extends('pages.support.content-page')

@section('support_page_class', 'warranty-page')
@section('support_title', 'Chính sách bảo hành')
@section('support_meta_desc', 'Chính sách bảo hành sản phẩm tại DangDuc.vn - Cam kết bảo hành chính hãng, minh bạch, nhanh chóng')
@section('support_meta_keywords', 'bảo hành, chính sách bảo hành, DangDuc, warranty')
@section('support_canonical', '/chinh-sach-bao-hanh')

@section('support_content')
<style>
.wr-wrap{max-width:1240px;margin:0 auto;padding:0 16px}
.wr-breadcrumb{padding:14px 0;font-size:13px;color:#64748b}
.wr-breadcrumb a{color:#64748b;text-decoration:none}.wr-breadcrumb a:hover{color:#0d9488}

/* Hero */
.wr-hero{display:flex;align-items:center;justify-content:space-between;padding:36px 48px;border-radius:16px;background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);margin-bottom:32px;position:relative;overflow:hidden}
.wr-hero::after{content:'';position:absolute;right:-40px;top:-40px;width:300px;height:300px;border-radius:50%;background:rgba(13,148,136,.12)}
.wr-hero-copy{max-width:560px;position:relative;z-index:1}
.wr-hero h1{color:#fff;font-size:30px;font-weight:800;margin:0 0 12px}
.wr-hero p{color:#94a3b8;font-size:14px;margin:0;line-height:1.6}
.wr-hero-art{position:relative;z-index:1;display:flex;align-items:center;gap:16px}
.wr-hero-art .icon-box{width:90px;height:90px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:36px}

/* Policy cards */
.wr-policy-cards{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:36px}
.wr-policy-card{padding:20px;border:1px solid #e5e7eb;border-radius:12px;background:#fff;text-align:center}
.wr-policy-card i{font-size:28px;color:#0d9488;display:block;margin-bottom:10px}
.wr-policy-card strong{font-size:14px;color:#111827;display:block;margin-bottom:6px}
.wr-policy-card p{font-size:12px;color:#64748b;margin:0;line-height:1.4}

/* Numbered sections */
.wr-section{margin-bottom:32px}
.wr-section-title{font-size:18px;font-weight:700;color:#0f172a;margin:0 0 16px;display:flex;align-items:center;gap:8px}
.wr-section-num{width:28px;height:28px;border-radius:6px;background:#0f172a;color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0}

/* Scope grid */
.wr-scope-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:16px}
.wr-scope-item{text-align:center;padding:16px 8px;border:1px solid #e5e7eb;border-radius:10px;background:#fff}
.wr-scope-item i{font-size:24px;color:#0f172a;display:block;margin-bottom:6px}
.wr-scope-item strong{font-size:12px;color:#111827;display:block}

/* Duration table */
.wr-duration-table{width:100%;border-collapse:collapse;font-size:13px;margin-bottom:8px;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden}
.wr-duration-table th{background:#f8fafc;padding:12px 16px;text-align:left;font-weight:700;color:#0f172a;border-bottom:1px solid #e5e7eb}
.wr-duration-table td{padding:10px 16px;border-bottom:1px solid #f1f5f9;color:#374151}
.wr-duration-table tr:last-child td{border-bottom:none}

/* Conditions */
.wr-conditions{display:grid;grid-template-columns:1fr 1fr;gap:20px}
.wr-cond-card{padding:20px;border:1px solid #e5e7eb;border-radius:12px;background:#fff}
.wr-cond-card h4{font-size:14px;font-weight:700;color:#0f172a;margin:0 0 12px}
.wr-cond-card ul{list-style:none;padding:0;margin:0}
.wr-cond-card li{padding:6px 0;font-size:12.5px;color:#374151;display:flex;align-items:flex-start;gap:8px;line-height:1.4}
.wr-cond-card li i{color:#0d9488;margin-top:3px;flex-shrink:0;font-size:12px}
.wr-cond-card li i.fa-times{color:#dc2626}

/* Process steps */
.wr-process{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:16px}
.wr-step{padding:20px 16px;border:1px solid #e5e7eb;border-radius:12px;background:#fff;position:relative}
.wr-step-num{width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#0f172a,#1e3a5f);color:#fff;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;margin-bottom:10px}
.wr-step strong{font-size:13px;color:#111827;display:block;margin-bottom:4px}
.wr-step p{font-size:11.5px;color:#64748b;margin:0;line-height:1.4}
.wr-step-arrow{position:absolute;right:-14px;top:50%;transform:translateY(-50%);color:#cbd5e1;font-size:18px}

/* Contact cards */
.wr-contacts{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:16px}
.wr-contact-card{padding:18px;border:1px solid #e5e7eb;border-radius:12px;background:#fff;text-align:center}
.wr-contact-card i{font-size:24px;color:#0d9488;display:block;margin-bottom:8px}
.wr-contact-card strong{font-size:13px;color:#111827;display:block;margin-bottom:4px}
.wr-contact-card span{font-size:11.5px;color:#64748b}

/* FAQ accordion */
.wr-faq{margin-bottom:16px}
.wr-faq details{border:1px solid #e5e7eb;border-radius:10px;margin-bottom:8px;overflow:hidden}
.wr-faq summary{padding:14px 18px;font-size:13.5px;font-weight:600;color:#0f172a;cursor:pointer;display:flex;align-items:center;justify-content:space-between;list-style:none}
.wr-faq summary::after{content:'\f078';font-family:FontAwesome;font-size:12px;color:#94a3b8;transition:transform .2s}
.wr-faq details[open] summary::after{transform:rotate(180deg)}
.wr-faq summary::-webkit-details-marker{display:none}
.wr-faq details p{padding:0 18px 14px;font-size:13px;color:#64748b;margin:0;line-height:1.6}

/* CTA bottom */
.wr-bottom-cta{background:linear-gradient(135deg,#0f172a,#1e3a5f);border-radius:14px;padding:28px 36px;text-align:center;margin-bottom:36px;color:#fff}
.wr-bottom-cta h3{font-size:18px;font-weight:700;margin:0 0 6px}
.wr-bottom-cta p{color:#94a3b8;font-size:13px;margin:0 0 16px}
.wr-cta-buttons{display:flex;justify-content:center;gap:12px}
.wr-cta-buttons a{padding:10px 24px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;border:1px solid rgba(255,255,255,.2);color:#fff;transition:all .2s}
.wr-cta-buttons a.primary{background:#0d9488;border-color:#0d9488}

/* Policy strip */
.wr-policies{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:36px}
.wr-pol{display:flex;align-items:center;gap:10px;padding:14px;border:1px solid #e5e7eb;border-radius:10px;background:#fff}
.wr-pol i{font-size:20px;color:#0d9488;flex-shrink:0}
.wr-pol strong{font-size:12px;color:#111827;display:block}
.wr-pol span{font-size:11px;color:#64748b}

@media(max-width:900px){.wr-hero{flex-direction:column;padding:24px}.wr-hero-art{display:none}.wr-scope-grid{grid-template-columns:repeat(3,1fr)}.wr-process,.wr-contacts{grid-template-columns:repeat(2,1fr)}.wr-conditions{grid-template-columns:1fr}.wr-policy-cards,.wr-policies{grid-template-columns:repeat(2,1fr)}}
@media(max-width:600px){.wr-scope-grid,.wr-policy-cards,.wr-policies{grid-template-columns:1fr}}
</style>

    {{-- Hero --}}
    <section class="wr-hero">
        <div class="wr-hero-copy">
            <h1>Chính sách bảo hành</h1>
            <p>DangDuc cam kết mang đến sản phẩm chính hãng, chất lượng cao cùng dịch vụ bảo hành minh bạch, nhanh chóng và tận tâm. Sự hài lòng của khách hàng là ưu tiên hàng đầu của chúng tôi.</p>
        </div>
        <div class="wr-hero-art">
            <div class="icon-box" style="background:rgba(13,148,136,.15);color:#14b8a6"><i class="fa fa-shield"></i></div>
            <div style="display:flex;flex-direction:column;gap:10px">
                <div class="icon-box" style="width:60px;height:60px;font-size:24px;background:rgba(59,130,246,.15);color:#3b82f6;border-radius:12px"><i class="fa fa-laptop"></i></div>
                <div class="icon-box" style="width:60px;height:60px;font-size:24px;background:rgba(16,185,129,.15);color:#10b981;border-radius:12px"><i class="fa fa-check-circle"></i></div>
            </div>
        </div>
    </section>

    {{-- 4 Policy highlight cards --}}
    <div class="wr-policy-cards">
        <div class="wr-policy-card"><i class="fa fa-shield"></i><strong>Bảo hành chính hãng</strong><p>100% sản phẩm chính hãng, bảo hành theo chính sách của nhà sản xuất.</p></div>
        <div class="wr-policy-card"><i class="fa fa-refresh"></i><strong>Đổi mới nếu lỗi NSX</strong><p>Đổi mới trong thời gian quy định nếu sản phẩm lỗi do nhà sản xuất (áp dụng điều kiện).</p></div>
        <div class="wr-policy-card"><i class="fa fa-headphones"></i><strong>Hỗ trợ nhanh</strong><p>Tiếp nhận & xử lý yêu cầu nhanh chóng qua nhiều kênh: Hotline, Chat, Email, Trung tâm.</p></div>
        <div class="wr-policy-card"><i class="fa fa-building-o"></i><strong>Trung tâm bảo hành</strong><p>Hệ thống trung tâm & đối tác bảo hành rộng khắp, thuận tiện cho khách hàng.</p></div>
    </div>

    {{-- 1. Phạm vi --}}
    <div class="wr-section">
        <h2 class="wr-section-title"><span class="wr-section-num">1</span> Phạm vi bảo hành</h2>
        <div class="wr-scope-grid">
            @foreach([['i'=>'fa-laptop','t'=>'Laptop &\nWorkstation'],['i'=>'fa-desktop','t'=>'PC & Máy bộ'],['i'=>'fa-television','t'=>'Màn hình'],['i'=>'fa-microchip','t'=>'Linh kiện'],['i'=>'fa-keyboard-o','t'=>'Phụ kiện'],['i'=>'fa-windows','t'=>'Phần mềm\n& hỗ trợ']] as $s)
            <div class="wr-scope-item"><i class="fa {{ $s['i'] }}"></i><strong>{!! nl2br(e($s['t'])) !!}</strong></div>
            @endforeach
        </div>
    </div>

    {{-- 2. Thời gian --}}
    <div class="wr-section">
        <h2 class="wr-section-title"><span class="wr-section-num">2</span> Thời gian bảo hành</h2>
        <table class="wr-duration-table">
            <thead><tr><th>Nhóm sản phẩm</th><th>Thời gian bảo hành</th></tr></thead>
            <tbody>
                <tr><td>Laptop & Workstation</td><td>12 – 24 tháng</td></tr>
                <tr><td>PC & Máy bộ</td><td>12 – 36 tháng</td></tr>
                <tr><td>Màn hình</td><td>12 tháng</td></tr>
                <tr><td>SSD, RAM, Ổ cứng, VGA, Mainboard, PSU</td><td>36 tháng</td></tr>
                <tr><td>Phụ kiện (Chuột, bàn phím, Tai nghe, Tản nhiệt...)</td><td>6 – 12 tháng</td></tr>
                <tr><td>Phần mềm (Windows, Office...)</td><td>Theo chính sách của nhà sản xuất</td></tr>
            </tbody>
        </table>
    </div>

    {{-- 3 & 4. Conditions --}}
    <div class="wr-conditions" style="margin-bottom:32px">
        <div class="wr-section">
            <h2 class="wr-section-title"><span class="wr-section-num">3</span> Điều kiện được bảo hành</h2>
            <div class="wr-cond-card">
                <ul>
                    <li><i class="fa fa-check"></i> Sản phẩm còn trong thời hạn bảo hành của DangDuc.</li>
                    <li><i class="fa fa-check"></i> Sản phẩm có tem, phiếu bảo hành hoặc hóa đơn mua hàng.</li>
                    <li><i class="fa fa-check"></i> Số serial/IMEI còn nguyên vẹn, không rách, không tẩy xóa.</li>
                    <li><i class="fa fa-check"></i> Lỗi kỹ thuật do nhà sản xuất.</li>
                    <li><i class="fa fa-check"></i> Sản phẩm không có dấu hiệu can thiệp, sửa chữa bên thứ ba.</li>
                </ul>
            </div>
        </div>
        <div class="wr-section">
            <h2 class="wr-section-title"><span class="wr-section-num">4</span> Các trường hợp không được bảo hành</h2>
            <div class="wr-cond-card">
                <ul>
                    <li><i class="fa fa-times"></i> Hết thời hạn bảo hành.</li>
                    <li><i class="fa fa-times"></i> Tem bảo hành, serial bị rách, mất, tẩy xóa.</li>
                    <li><i class="fa fa-times"></i> Hư hỏng do rơi vỡ, va đập, nước, cháy nổ, thiên tai.</li>
                    <li><i class="fa fa-times"></i> Sử dụng sai mục đích, tự ý tháo lắp, sửa chữa.</li>
                    <li><i class="fa fa-times"></i> Hao mòn tự nhiên (quạt, pin, bề mặt, cáp...).</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- 5. Quy trình --}}
    <div class="wr-section">
        <h2 class="wr-section-title"><span class="wr-section-num">5</span> Quy trình bảo hành</h2>
        <div class="wr-process">
            @foreach([['n'=>'01','t'=>'Tiếp nhận','d'=>'Khách hàng yêu cầu bảo hành qua hotline, chat, email hoặc trực tiếp tại trung tâm.'],['n'=>'02','t'=>'Kiểm tra','d'=>'Kỹ thuật viên kiểm tra sản phẩm, xác định lỗi và thông báo.'],['n'=>'03','t'=>'Xử lý','d'=>'Tiến hành bảo hành, sửa chữa hoặc đổi mới theo quy định của nhà sản xuất.'],['n'=>'04','t'=>'Bàn giao','d'=>'Bàn giao sản phẩm cho khách hàng và hướng dẫn bảo quản.']] as $i => $step)
            <div class="wr-step">
                <div class="wr-step-num">{{ $step['n'] }}</div>
                <strong>{{ $step['t'] }}</strong>
                <p>{{ $step['d'] }}</p>
                @if($i < 3)<span class="wr-step-arrow"><i class="fa fa-long-arrow-right"></i></span>@endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- 6. Trung tâm hỗ trợ --}}
    <div class="wr-section">
        <h2 class="wr-section-title"><span class="wr-section-num">6</span> Trung tâm & kênh hỗ trợ</h2>
        <div class="wr-contacts">
            <div class="wr-contact-card"><i class="fa fa-phone"></i><strong>Hotline</strong><span>1900 1234</span><br><span>8:00 – 22:00 (Thứ 2 – Chủ nhật)</span></div>
            <div class="wr-contact-card"><i class="fa fa-envelope"></i><strong>Email</strong><span>support@dangduc.vn</span><br><span>Phản hồi trong 1 – 2 giờ</span></div>
            <div class="wr-contact-card"><i class="fa fa-map-marker"></i><strong>Trung tâm bảo hành</strong><span>128 Nguyễn Trãi,</span><br><span>Thanh Xuân, Hà Nội</span></div>
            <div class="wr-contact-card"><i class="fa fa-comments"></i><strong>Chat trực tuyến</strong><span>Hỗ trợ ngay trên website</span><br><span>Xem bản đồ</span></div>
        </div>
    </div>

    {{-- 7. FAQ --}}
    <div class="wr-section">
        <h2 class="wr-section-title"><span class="wr-section-num">7</span> Câu hỏi thường gặp</h2>
        <div class="wr-faq">
            @foreach([['q'=>'Thời gian bảo hành được tính từ khi nào?','a'=>'Thời gian bảo hành được tính từ ngày mua hàng ghi trên hóa đơn hoặc phiếu bảo hành.'],['q'=>'Tôi có cần mang hóa đơn khi đi bảo hành không?','a'=>'Có, bạn nên mang theo hóa đơn mua hàng hoặc phiếu bảo hành để xác nhận thời hạn bảo hành.'],['q'=>'Sản phẩm được bảo hành tại đâu?','a'=>'Bạn có thể gửi sản phẩm bảo hành tại trung tâm DangDuc hoặc gửi qua đường bưu điện.'],['q'=>'Thời gian xử lý bảo hành mất bao lâu?','a'=>'Tùy tình trạng sản phẩm, thông thường từ 3-7 ngày làm việc.'],['q'=>'Tôi có thể theo dõi trạng thái bảo hành ở đâu?','a'=>'Bạn có thể kiểm tra trạng thái bảo hành qua trang tài khoản hoặc liên hệ hotline.']] as $faq)
            <details><summary>{{ $faq['q'] }}</summary><p>{{ $faq['a'] }}</p></details>
            @endforeach
        </div>
    </div>

    {{-- Bottom CTA --}}
    <div class="wr-bottom-cta">
        <h3>Cần hỗ trợ bảo hành?</h3>
        <p>Đội ngũ DangDuc luôn sẵn sàng hỗ trợ bạn nhanh chóng và tận tâm.</p>
        <div class="wr-cta-buttons">
            <a href="#" class="primary"><i class="fa fa-pencil-square-o"></i> Yêu cầu bảo hành</a>
            <a href="#"><i class="fa fa-phone"></i> Liên hệ hỗ trợ<br><small>Hotline 1900 1234</small></a>
            <a href="#"><i class="fa fa-search"></i> Kiểm tra đơn hàng<br><small>Nhập mã đơn hàng</small></a>
        </div>
    </div>

    {{-- Policy strip --}}
    <div class="wr-policies">
        <div class="wr-pol"><i class="fa fa-truck"></i><div><strong>Miễn phí giao hàng</strong><span>Nội thành đơn từ 300k</span></div></div>
        <div class="wr-pol"><i class="fa fa-refresh"></i><div><strong>Đổi trả dễ dàng</strong><span>Đổi trả trong 7 ngày nếu sản phẩm lỗi</span></div></div>
        <div class="wr-pol"><i class="fa fa-shield"></i><div><strong>Bảo hành chính hãng</strong><span>Cam kết bảo hành minh bạch</span></div></div>
        <div class="wr-pol"><i class="fa fa-lock"></i><div><strong>Thanh toán an toàn</strong><span>Nhiều phương thức thanh toán</span></div></div>
        <div class="wr-pol"><i class="fa fa-headphones"></i><div><strong>Hỗ trợ tận tâm 24/7</strong><span>Đội ngũ tư vấn luôn sẵn sàng giải đáp</span></div></div>
    </div>
@endsection
