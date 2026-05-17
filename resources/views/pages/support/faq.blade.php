@extends('layouts.home-tech')

@php
    $meta_desc = $meta_desc ?? 'Câu hỏi thường gặp về mua hàng, thanh toán, vận chuyển, bảo hành tại DangDuc.vn';
    $meta_keywords = $meta_keywords ?? 'FAQ, câu hỏi thường gặp, DangDuc, hỗ trợ khách hàng';
    $meta_title = $meta_title ?? 'Câu hỏi thường gặp - DangDuc';
    $url_canonical = $url_canonical ?? url('/cau-hoi-thuong-gap');
@endphp

@section('body_class', 'policy-page faq-page')

@push('styles')
<style>
.faq-wrap{max-width:1240px;margin:0 auto;padding:0 16px}
.faq-breadcrumb{padding:14px 0;font-size:13px;color:#64748b}
.faq-breadcrumb a{color:#64748b;text-decoration:none}.faq-breadcrumb a:hover{color:#0d9488}

/* Hero */
.faq-hero{display:flex;align-items:center;justify-content:space-between;padding:40px 48px;border-radius:16px;background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);margin-bottom:32px;position:relative;overflow:hidden}
.faq-hero::after{content:'';position:absolute;right:-40px;top:-40px;width:300px;height:300px;border-radius:50%;background:rgba(13,148,136,.12)}
.faq-hero-copy{max-width:560px;position:relative;z-index:1}
.faq-hero h1{color:#fff;font-size:32px;font-weight:800;margin:0 0 12px;line-height:1.2}
.faq-hero p{color:#94a3b8;font-size:15px;margin:0 0 16px;line-height:1.6}
.faq-hero .last-update{display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.08);color:#94a3b8;padding:6px 14px;border-radius:20px;font-size:12px;margin-bottom:16px}
.faq-search{display:flex;align-items:center;gap:8px;background:#fff;border-radius:12px;padding:4px 4px 4px 18px;max-width:440px}
.faq-search input{flex:1;border:none;outline:none;font-size:14px;padding:10px 0;color:#111827}
.faq-search button{background:#0d9488;color:#fff;border:none;padding:10px 20px;border-radius:8px;font-weight:600;font-size:13px;cursor:pointer}
.faq-hero-art{position:relative;z-index:1;width:240px;display:flex;align-items:center;justify-content:center}
.faq-hero-art img{width:100%;max-width:200px;height:auto}
.faq-hero-art .faq-icon-group{display:flex;flex-direction:column;align-items:center;gap:16px}
.faq-icon-badge{width:80px;height:80px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:900}

/* Highlights */
.faq-highlights{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:36px}
.faq-highlight{display:flex;align-items:flex-start;gap:12px;padding:18px 16px;border:1px solid #e5e7eb;border-radius:12px;background:#fff}
.faq-highlight i{color:#0d9488;font-size:22px;margin-top:2px;flex-shrink:0}
.faq-highlight strong{font-size:13px;color:#111827;display:block;margin-bottom:2px}
.faq-highlight p{font-size:12px;color:#64748b;margin:0;line-height:1.4}

/* Category section */
.faq-section-title{text-align:center;font-size:22px;font-weight:800;color:#111827;margin:0 0 24px}
.faq-categories{display:grid;grid-template-columns:repeat(6,1fr);gap:14px;margin-bottom:40px}
.faq-cat{text-align:center;padding:20px 12px;border:1px solid #e5e7eb;border-radius:12px;background:#fff;text-decoration:none;transition:all .2s}
.faq-cat:hover{border-color:#0d9488;box-shadow:0 4px 16px rgba(13,148,136,.1);transform:translateY(-2px)}
.faq-cat i{font-size:28px;color:#0f172a;display:block;margin-bottom:10px}
.faq-cat strong{font-size:13px;color:#111827;display:block;margin-bottom:4px}
.faq-cat p{font-size:11px;color:#64748b;margin:0;line-height:1.3}

/* FAQ content sections */
.faq-content{margin-bottom:40px}
.faq-content h2{font-size:18px;font-weight:700;color:#0f172a;margin:0 0 16px;padding-bottom:10px;border-bottom:2px solid #e5e7eb}
.faq-grid{display:grid;grid-template-columns:1fr 1fr;gap:0}
.faq-item{border-bottom:1px solid #f1f5f9}
.faq-item a{display:flex;align-items:center;gap:8px;padding:12px 16px;color:#374151;text-decoration:none;font-size:13.5px;transition:color .15s}
.faq-item a:hover{color:#0d9488}
.faq-item a i{color:#0d9488;font-size:10px;flex-shrink:0}

/* Quick tips */
.faq-tips{background:#f0fdfa;border-radius:14px;padding:28px 32px;margin-bottom:36px}
.faq-tips h3{font-size:16px;font-weight:700;color:#0f172a;margin:0 0 14px}
.faq-tips-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
.faq-tips-grid li{display:flex;align-items:flex-start;gap:8px;font-size:12.5px;color:#374151;line-height:1.4}
.faq-tips-grid li i{color:#0d9488;margin-top:2px;flex-shrink:0}

/* Still need help */
.faq-help-cta{text-align:center;padding:32px;margin-bottom:36px}
.faq-help-cta h3{font-size:18px;font-weight:700;color:#0f172a;margin:0 0 6px}
.faq-help-cta p{color:#64748b;font-size:14px;margin:0 0 16px}
.faq-help-contacts{display:flex;justify-content:center;gap:32px;margin-bottom:16px}
.faq-contact-item{text-align:center}
.faq-contact-item i{font-size:22px;color:#0d9488;display:block;margin-bottom:4px}
.faq-contact-item strong{font-size:13px;color:#111827;display:block}
.faq-contact-item span{font-size:11.5px;color:#64748b}

/* Bottom CTA */
.faq-bottom-cta{background:linear-gradient(135deg,#0f172a,#1e3a5f);border-radius:14px;padding:28px 36px;text-align:center;margin-bottom:36px;color:#fff}
.faq-bottom-cta h3{font-size:18px;font-weight:700;margin:0 0 6px}
.faq-bottom-cta p{color:#94a3b8;font-size:13px;margin:0 0 16px}
.faq-bottom-cta .cta-buttons{display:flex;justify-content:center;gap:12px}
.faq-bottom-cta .cta-buttons a{padding:10px 24px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;border:1px solid rgba(255,255,255,.2);color:#fff;transition:all .2s}
.faq-bottom-cta .cta-buttons a.primary{background:#0d9488;border-color:#0d9488}
.faq-bottom-cta .cta-buttons a:hover{opacity:.9}

/* Policy strip */
.faq-policies{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:36px}
.faq-policy{display:flex;align-items:center;gap:10px;padding:14px;border:1px solid #e5e7eb;border-radius:10px;background:#fff}
.faq-policy i{font-size:20px;color:#0d9488;flex-shrink:0}
.faq-policy strong{font-size:12px;color:#111827;display:block}
.faq-policy span{font-size:11px;color:#64748b}

@media(max-width:900px){.faq-hero{flex-direction:column;text-align:center;padding:24px}.faq-hero-art{display:none}.faq-categories{grid-template-columns:repeat(3,1fr)}.faq-grid{grid-template-columns:1fr}.faq-highlights,.faq-tips-grid,.faq-policies{grid-template-columns:repeat(2,1fr)}}
@media(max-width:600px){.faq-categories{grid-template-columns:repeat(2,1fr)}.faq-highlights,.faq-tips-grid,.faq-policies{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
<div class="pp-wrap">
    @include('partials.storefront.breadcrumb', [
        'class' => 'pp-breadcrumb',
        'separator' => '›',
        'items' => [
            [
                'label' => 'Câu hỏi thường gặp',
                'current' => true,
            ],
        ],
    ])

    {{-- Hero --}}
    <section class="faq-hero">
        <div class="faq-hero-copy">
            <h1>Câu hỏi thường gặp</h1>
            <p>Tổng hợp những câu hỏi phổ biến về mua hàng, thanh toán, vận chuyển, bảo hành và dịch vụ tại DangDuc.vn.<br>Tìm câu trả lời nhanh chóng, rõ ràng và dễ hiểu.</p>
            <div class="last-update"><i class="fa fa-clock-o"></i> Cập nhật lần cuối: 01/2025</div>
            <div class="faq-search">
                <input type="text" placeholder="Nhập câu hỏi bạn cần tìm...">
                <button><i class="fa fa-search"></i> Tìm kiếm</button>
            </div>
        </div>
        <div class="faq-hero-art">
            <div class="faq-icon-group">
                <div class="faq-icon-badge" style="background:rgba(13,148,136,.15);color:#14b8a6">?</div>
                <div style="display:flex;gap:10px">
                    <div style="width:50px;height:50px;border-radius:12px;background:rgba(59,130,246,.15);display:flex;align-items:center;justify-content:center;color:#3b82f6;font-size:20px"><i class="fa fa-comments"></i></div>
                    <div style="width:50px;height:50px;border-radius:12px;background:rgba(16,185,129,.15);display:flex;align-items:center;justify-content:center;color:#10b981;font-size:20px"><i class="fa fa-check-circle"></i></div>
                </div>
            </div>
        </div>
    </section>

    {{-- Highlights --}}
    <div class="faq-highlights">
        <div class="faq-highlight"><i class="fa fa-search"></i><div><strong>Tìm kiếm nhanh</strong><p>Tra cứu câu trả lời chỉ trong vài giây.</p></div></div>
        <div class="faq-highlight"><i class="fa fa-file-text-o"></i><div><strong>Nội dung rõ ràng</strong><p>Thông tin được soạn bằng ngôn gữ ngắn gọn, dễ hiểu.</p></div></div>
        <div class="faq-highlight"><i class="fa fa-refresh"></i><div><strong>Cập nhật thường xuyên</strong><p>Các câu hỏi được bổ sung theo nhu cầu thực tế.</p></div></div>
        <div class="faq-highlight"><i class="fa fa-headphones"></i><div><strong>Hỗ trợ tận tâm</strong><p>Đội ngũ CSKH luôn sẵn sàng hỗ trợ 24/7.</p></div></div>
    </div>

    {{-- Category icons --}}
    <h2 class="faq-section-title">Danh mục câu hỏi</h2>
    <div class="faq-categories">
        @foreach([['i'=>'fa-shopping-cart','t'=>'Đặt hàng','d'=>'Câu hỏi về cách đặt hàng và đơn hàng.'],['i'=>'fa-credit-card','t'=>'Thanh toán','d'=>'Hình thức thanh toán và sao chỉnh.'],['i'=>'fa-truck','t'=>'Vận chuyển','d'=>'Thời gian, giá cả, và trạng thái giao hàng.'],['i'=>'fa-shield','t'=>'Bảo hành & đổi trả','d'=>'Chính sách bảo hành và điều kiện đổi trả.'],['i'=>'fa-user','t'=>'Tài khoản & bảo mật','d'=>'Tài khoản, thông tin cá nhân và bảo mật.'],['i'=>'fa-file-text-o','t'=>'Hóa đơn VAT','d'=>'Hóa đơn, chứng từ và xuất VAT.']] as $c)
        <a href="#" class="faq-cat"><i class="fa {{ $c['i'] }}"></i><strong>{{ $c['t'] }}</strong><p>{{ $c['d'] }}</p></a>
        @endforeach
    </div>

    {{-- FAQ sections --}}
    @php
    $sections = [
        ['title'=>'Đặt hàng','items'=>['Tôi có cần tài khoản để đặt hàng không?','Tôi có thể đặt hàng qua điện thoại không?','Sau khi đặt hàng tôi có chỉnh sửa thông tin không?','Làm sao để kiểm tra trạng thái đơn hàng?']],
        ['title'=>'Thanh toán','items'=>['DangDuc hỗ trợ những hình thức thanh toán nào?','Chuyển khoản bao lâu thì được xác nhận?','Thanh toán online có an toàn không?','Nếu thanh toán lỗi hoặc bị trừ tiền thì sao?']],
        ['title'=>'Vận chuyển','items'=>['Thời gian giao hàng mất bao lâu?','Phí giao hàng được tính như thế nào?','Tôi có được kiểm tra hàng khi nhận không?','Làm sao theo dõi đơn đang giao?']],
        ['title'=>'Bảo hành & đổi trả','items'=>['Sản phẩm được đổi trả trong bao lâu?','Quy trình đổi trả như thế nào?','Điều kiện bảo hành là gì?','Tôi có cần giữ hộp để gửi bảo hành không?']],
        ['title'=>'Tài khoản & bảo mật','items'=>['Quên mật khẩu phải làm sao?','Thông tin cá nhân có được bảo mật?','Làm sao thay đổi số điện thoại hoặc email?','Tài khoản bị khoá thì phải làm gì?']],
        ['title'=>'Hóa đơn VAT','items'=>['Làm sao để yêu cầu xuất hóa đơn VAT?','Tôi có thể sửa thông tin công ty sau khi đặt hàng không?','Bao lâu thì nhận được hóa đơn?','Thông tin trên hóa đơn bị sai xử lý thế nào?']],
    ];
    @endphp

    @foreach($sections as $sec)
    <div class="faq-content">
        <h2>{{ $sec['title'] }}</h2>
        <div class="faq-grid">
            @foreach($sec['items'] as $item)
            <div class="faq-item"><a href="#"><i class="fa fa-chevron-right"></i> {{ $item }}</a></div>
            @endforeach
        </div>
    </div>
    @endforeach

    {{-- Quick tips --}}
    <div class="faq-tips">
        <h3>Mẹo tra cứu nhanh</h3>
        <ul class="faq-tips-grid" style="list-style:none;padding:0;margin:0">
            <li><i class="fa fa-check"></i> Sử dụng từ khoá ngắn gọn</li>
            <li><i class="fa fa-check"></i> Chọn đúng danh mục câu hỏi</li>
            <li><i class="fa fa-check"></i> Kiểm tra phần thanh toán và vận chuyển</li>
            <li><i class="fa fa-check"></i> Liên hệ CSKH nếu chưa tìm thấy nội dung phù hợp</li>
        </ul>
    </div>

    {{-- Still need help --}}
    <div class="faq-help-cta">
        <h3>Bạn vẫn chưa tìm thấy câu trả lời?</h3>
        <p>Đội ngũ DangDuc luôn sẵn sàng hỗ trợ bạn nhanh chóng và tận tâm.</p>
        <div class="faq-help-contacts">
            <div class="faq-contact-item"><i class="fa fa-phone"></i><strong>Hotline</strong><span>1900 1234</span><br><span>08:00 – 21:00 (T2 – CN)</span></div>
            <div class="faq-contact-item"><i class="fa fa-comments"></i><strong>Hỗ trợ trực tuyến</strong><span>Phản hồi trong 1 – 2 giờ</span></div>
            <div class="faq-contact-item"><i class="fa fa-envelope"></i><strong>Email</strong><span>support@dangduc.vn</span></div>
        </div>
    </div>

    {{-- Bottom CTA --}}
    <div class="faq-bottom-cta">
        <h3>Cần hỗ trợ thêm?</h3>
        <p>Đội ngũ DangDuc luôn sẵn sàng giải đáp nhanh chóng và tận tâm.</p>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;max-width:700px;margin:0 auto 16px">
            <div style="text-align:center"><div style="font-weight:700;font-size:14px">Hotline 24/7</div><div style="color:#14b8a6;font-weight:800;font-size:16px">1900 1234</div><div style="font-size:11px;color:#94a3b8">08:00 – 21:00 (T2 – CN)</div></div>
            <div style="text-align:center"><div style="font-weight:700;font-size:14px">Trung tâm hỗ trợ</div><div style="color:#14b8a6;font-size:13px">Hỗ trợ trực tuyến</div><div style="font-size:11px;color:#94a3b8">Phản hồi trong 1 – 2 giờ</div></div>
            <div style="text-align:center"><div style="font-weight:700;font-size:14px">Liên hệ tư vấn</div><div style="color:#14b8a6;font-size:13px">support@dangduc.vn</div><div style="font-size:11px;color:#94a3b8">Trả lời trong giờ làm việc</div></div>
        </div>
    </div>

    {{-- Policy strip --}}
    <div class="faq-policies">
        <div class="faq-policy"><i class="fa fa-truck"></i><div><strong>Miễn phí giao hàng</strong><span>Nội thành đơn từ 300k</span></div></div>
        <div class="faq-policy"><i class="fa fa-refresh"></i><div><strong>Đổi trả dễ dàng</strong><span>Đổi trả trong 7 ngày nếu sản phẩm lỗi</span></div></div>
        <div class="faq-policy"><i class="fa fa-shield"></i><div><strong>Bảo hành chính hãng</strong><span>Cam kết bảo hành chính hãng</span></div></div>
        <div class="faq-policy"><i class="fa fa-lock"></i><div><strong>Thanh toán an toàn</strong><span>Nhiều phương thức thanh toán</span></div></div>
        <div class="faq-policy"><i class="fa fa-headphones"></i><div><strong>Hỗ trợ tận tâm 24/7</strong><span>Đội ngũ tư vấn luôn sẵn sàng</span></div></div>
    </div>
</div>
@endsection
