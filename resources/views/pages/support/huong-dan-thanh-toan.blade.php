@extends('layouts.home-tech')
@section('body_class','policy-page guide-page')
@php
$meta_desc=$meta_desc??'Hướng dẫn thanh toán tại DangDuc.vn - Thanh toán nhanh chóng, an toàn và tiện lợi';
$meta_keywords=$meta_keywords??'hướng dẫn thanh toán, cách thanh toán, DangDuc';
$url_canonical=$url_canonical??url('/huong-dan-thanh-toan');
$meta_title=$meta_title??'Hướng dẫn thanh toán - DangDuc';
@endphp
@push('styles')<link href="{{ asset('frontend/css/policy-page.css') }}" rel="stylesheet">@endpush
@section('content')
<div class="pp-wrap">
    @include('partials.storefront.breadcrumb', ['class' => 'pp-breadcrumb', 'separator' => '›', 'items' => [['label' => 'Hướng dẫn thanh toán', 'current' => true]]])

    <section class="pp-hero">
        <div class="pp-hero-copy">
            <h1>Hướng dẫn thanh toán</h1>
            <p>Trang hướng dẫn giúp bạn lựa chọn và thực hiện thanh toán trên DangDuc.vn một cách nhanh chóng, an toàn và thuận tiện với nhiều phương thức linh hoạt.</p>
            <div class="pp-update"><i class="fa fa-refresh"></i> Cập nhật lần cuối: 01/2025</div>
        </div>
        <div class="pp-hero-art">
            <div class="pp-icon-box" style="background:rgba(13,148,136,.15);color:#14b8a6"><i class="fa fa-credit-card"></i></div>
            <div style="display:flex;flex-direction:column;gap:10px">
                <div class="pp-icon-sm" style="background:rgba(59,130,246,.15);color:#3b82f6"><i class="fa fa-qrcode"></i></div>
                <div class="pp-icon-sm" style="background:rgba(16,185,129,.15);color:#10b981"><i class="fa fa-check-circle"></i></div>
            </div>
        </div>
    </section>

    <div class="pp-highlights">
        <div class="pp-highlight"><i class="fa fa-money"></i><strong>Thanh toán linh hoạt</strong><p>Nhiều phương thức phù hợp với mọi nhu cầu mua sắm.</p></div>
        <div class="pp-highlight"><i class="fa fa-lock"></i><strong>Bảo mật an toàn</strong><p>Thông tin giao dịch được bảo vệ và tuân thủ tiêu chuẩn.</p></div>
        <div class="pp-highlight"><i class="fa fa-check-circle"></i><strong>Xác nhận nhanh</strong><p>Đơn hàng được xử lý nhanh và phản hồi kịp thời.</p></div>
        <div class="pp-highlight"><i class="fa fa-headphones"></i><strong>Hỗ trợ tận tâm</strong><p>Đội ngũ CSKH luôn sẵn sàng hỗ trợ 24/7.</p></div>
    </div>

    {{-- Quy trình thanh toán --}}
    <div class="pp-section" style="text-align:center;margin-bottom:24px">
        <h2 style="font-size:20px;font-weight:700;color:#0f172a">Quy trình thanh toán tại DangDuc.vn</h2>
    </div>

    <div class="pp-terms-grid" style="margin-bottom:24px">
        <div class="pp-terms-card">
            <h4><span class="pp-num">1</span> Chọn sản phẩm và đặt hàng</h4>
            <ul>
                <li><i class="fa fa-check"></i> Thêm sản phẩm vào giỏ hàng.</li>
                <li><i class="fa fa-check"></i> Kiểm tra số lượng, phiên bản và phụ kiện.</li>
                <li><i class="fa fa-check"></i> Nhấn "Tiến hành thanh toán".</li>
            </ul>
        </div>
        <div class="pp-terms-card">
            <h4><span class="pp-num">2</span> Kiểm tra giỏ hàng</h4>
            <ul>
                <li><i class="fa fa-check"></i> Xem lại danh sách sản phẩm.</li>
                <li><i class="fa fa-check"></i> Nhập mã giảm giá nếu có.</li>
                <li><i class="fa fa-check"></i> Kiểm tra tổng tiền và phí dự kiến.</li>
            </ul>
        </div>
        <div class="pp-terms-card">
            <h4><span class="pp-num">3</span> Nhập thông tin nhận hàng</h4>
            <ul>
                <li><i class="fa fa-check"></i> Nhập họ tên, số điện thoại.</li>
                <li><i class="fa fa-check"></i> Nhập địa chỉ giao hàng chính xác.</li>
                <li><i class="fa fa-check"></i> Thêm ghi chú cho đơn hàng (nếu cần).</li>
            </ul>
        </div>
        <div class="pp-terms-card">
            <h4><span class="pp-num">4</span> Chọn phương thức thanh toán</h4>
            <ul>
                <li><i class="fa fa-check"></i> Chọn COD, chuyển khoản, thẻ hoặc ví điện tử.</li>
                <li><i class="fa fa-check"></i> Kiểm tra điều kiện áp dụng của từng phương thức.</li>
            </ul>
        </div>
        <div class="pp-terms-card">
            <h4><span class="pp-num">5</span> Thanh toán khi nhận hàng (COD)</h4>
            <ul>
                <li><i class="fa fa-check"></i> Thanh toán trực tiếp cho nhân viên giao hàng.</li>
                <li><i class="fa fa-check"></i> Kiểm tra đơn hàng trước khi thanh toán.</li>
            </ul>
        </div>
        <div class="pp-terms-card">
            <h4><span class="pp-num">6</span> Chuyển khoản ngân hàng</h4>
            <ul>
                <li><i class="fa fa-check"></i> Chuyển khoản theo thông tin tài khoản DangDuc.</li>
                <li><i class="fa fa-check"></i> Ghi rõ mã đơn hàng hoặc số điện thoại.</li>
                <li><i class="fa fa-check"></i> Đơn sẽ được xác nhận sau khi nhận tiền.</li>
            </ul>
        </div>
        <div class="pp-terms-card">
            <h4><span class="pp-num">7</span> Thanh toán online</h4>
            <ul>
                <li><i class="fa fa-check"></i> Thực hiện thanh toán qua cổng thanh toán hỗ trợ.</li>
                <li><i class="fa fa-check"></i> Hỗ trợ thẻ ATM, thẻ quốc tế và ví điện tử.</li>
                <li><i class="fa fa-check"></i> Kiểm tra tình trạng giao dịch thành công.</li>
            </ul>
        </div>
        <div class="pp-terms-card">
            <h4><span class="pp-num">8</span> Xác nhận giao dịch</h4>
            <ul>
                <li><i class="fa fa-check"></i> Hệ thống gửi thông báo xác nhận thanh toán.</li>
                <li><i class="fa fa-check"></i> Kiểm tra email, tin nhắn hoặc trạng thái đơn hàng.</li>
            </ul>
        </div>
        <div class="pp-terms-card">
            <h4><span class="pp-num">9</span> Xuất hóa đơn VAT</h4>
            <ul>
                <li><i class="fa fa-check"></i> Cung cấp đầy đủ thông tin xuất hóa đơn nếu cần.</li>
                <li><i class="fa fa-check"></i> Kiểm tra mã số thuế, tên công ty và địa chỉ.</li>
            </ul>
        </div>
    </div>

    {{-- Step 10 --}}
    <div class="pp-section">
        <div class="pp-terms-card" style="max-width:100%">
            <h4><span class="pp-num">10</span> Hỗ trợ sau thanh toán</h4>
            <ul>
                <li><i class="fa fa-check"></i> Liên hệ CSKH nếu có lỗi giao dịch hoặc cần hỗ trợ.</li>
                <li><i class="fa fa-check"></i> Hỗ trợ tra soát, hoàn tiền nếu thanh toán nhầm.</li>
            </ul>
        </div>
    </div>

    {{-- Phương thức thanh toán hỗ trợ --}}
    <div class="pp-section" style="text-align:center;margin-bottom:24px">
        <h2 style="font-size:18px;font-weight:700;color:#0f172a">Phương thức thanh toán hỗ trợ</h2>
    </div>
    <div class="pp-scope" style="margin-bottom:32px">
        @foreach([['i'=>'fa-money','t'=>'COD<br>Thanh toán khi nhận hàng'],['i'=>'fa-university','t'=>'Chuyển khoản<br>ngân hàng'],['i'=>'fa-credit-card','t'=>'Thẻ ATM /<br>Internet Banking'],['i'=>'fa-cc-visa','t'=>'Thẻ quốc tế'],['i'=>'fa-mobile','t'=>'Ví điện tử']] as $s)
        <div class="pp-scope-item"><i class="fa {{ $s['i'] }}"></i><strong>{!! $s['t'] !!}</strong></div>
        @endforeach
    </div>

    {{-- Lưu ý & FAQ --}}
    <div class="pp-conditions" style="margin-bottom:32px">
        <div class="pp-section">
            <h2 class="pp-section-title">Lưu ý khi thanh toán</h2>
            <div class="pp-cond-card">
                <ul>
                    <li><i class="fa fa-check"></i> Kiểm tra đúng số tiền và nội dung thanh toán.</li>
                    <li><i class="fa fa-check"></i> Không chia sẻ OTP và các thông tin nhạy cảm trên thẻ.</li>
                    <li><i class="fa fa-check"></i> Giữ lại biên lai hoặc ảnh giao dịch để đối chiếu khi cần.</li>
                    <li><i class="fa fa-check"></i> Liên hệ ngay CSKH nếu phát hiện dấu hiệu bất thường.</li>
                </ul>
            </div>
        </div>
        <div class="pp-section">
            <h2 class="pp-section-title">Câu hỏi thường gặp</h2>
            <div class="pp-faq-list" style="grid-template-columns:1fr">
                @foreach(['Tôi có thể thanh toán bằng nhiều phương thức một?','Sau khi chuyển khoản bao lâu đơn hàng được xác nhận?','Thanh toán online có an toàn không?','Tôi có thể yêu cầu xuất hóa đơn VAT thế nào?','Nếu thanh toán lỗi hoặc trừ tiền nhưng chưa ghi nhận thì làm sao?'] as $q)
                <div class="pp-faq-item"><a href="#"><i class="fa fa-chevron-right"></i> {{ $q }}</a></div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="pp-cta">
        <h3>Cần hỗ trợ thanh toán?</h3>
        <p>Đội ngũ DangDuc luôn sẵn sàng hỗ trợ bạn nhanh chóng và tận tâm.</p>
        <div class="pp-cta-buttons">
            <a href="#" class="pp-cta-btn primary"><i class="fa fa-phone"></i> Hotline 24/7<br><small>1900 1234</small><br><small>08:00 - 21:00 (T2 - CN)</small></a>
            <a href="#" class="pp-cta-btn"><i class="fa fa-comments"></i> Hỗ trợ trực tuyến<br><small>Chat ngay với chúng tôi</small><br><small>Phản hồi trong 1 - 2 giờ</small></a>
            <a href="#" class="pp-cta-btn"><i class="fa fa-envelope"></i> Liên hệ tư vấn<br><small>support@dangduc.vn</small><br><small>Trả lời trong giờ làm việc</small></a>
        </div>
    </div>

    <div class="pp-policies">
        <div class="pp-pol"><i class="fa fa-truck"></i><div><strong>Miễn phí giao hàng</strong><span>Nội thành đơn từ 300K</span></div></div>
        <div class="pp-pol"><i class="fa fa-refresh"></i><div><strong>Đổi trả dễ dàng</strong><span>Đổi trả trong 7 ngày nếu sản phẩm lỗi</span></div></div>
        <div class="pp-pol"><i class="fa fa-shield"></i><div><strong>Bảo hành chính hãng</strong><span>Cam kết bảo hành chính hãng</span></div></div>
        <div class="pp-pol"><i class="fa fa-lock"></i><div><strong>Thanh toán an toàn</strong><span>Nhiều phương thức thanh toán</span></div></div>
        <div class="pp-pol"><i class="fa fa-headphones"></i><div><strong>Hỗ trợ tận tâm 24/7</strong><span>Đội ngũ tư vấn luôn sẵn sàng</span></div></div>
    </div>
</div>
@endsection
