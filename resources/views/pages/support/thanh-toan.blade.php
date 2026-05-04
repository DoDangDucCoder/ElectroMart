@extends('layouts.home-tech')
@section('body_class','policy-page thanh-toan-page')
@php
$meta_desc=$meta_desc??'Chính sách thanh toán tại DangDuc.vn - Đa dạng phương thức, bảo mật an toàn';
$meta_keywords=$meta_keywords??'thanh toán, chính sách thanh toán, DangDuc, payment';
$url_canonical=$url_canonical??url('/chinh-sach-thanh-toan');
$meta_title=$meta_title??'Chính sách thanh toán - DangDuc';
@endphp
@push('styles')<link href="{{ asset('frontend/css/policy-page.css') }}" rel="stylesheet">@endpush
@section('content')
<div class="pp-wrap">
    <nav class="pp-breadcrumb"><a href="{{ url('/') }}">Trang chủ</a> <i class="fa fa-angle-right"></i> Chính sách thanh toán</nav>

    <section class="pp-hero">
        <div class="pp-hero-copy">
            <h1>Chính sách thanh toán</h1>
            <p>DangDuc hỗ trợ đa dạng phương thức thanh toán, đảm bảo giao dịch của bạn luôn an toàn, nhanh chóng và minh bạch. Chọn phương thức phù hợp với nhu cầu để trải nghiệm mua sắm dễ dàng và tiện lợi nhất.</p>
        </div>
        <div class="pp-hero-art">
            <div class="pp-icon-box" style="background:rgba(13,148,136,.15);color:#14b8a6"><i class="fa fa-credit-card"></i></div>
            <div style="display:flex;flex-direction:column;gap:10px">
                <div class="pp-icon-sm" style="background:rgba(59,130,246,.15);color:#3b82f6"><i class="fa fa-qrcode"></i></div>
                <div class="pp-icon-sm" style="background:rgba(245,158,11,.15);color:#f59e0b"><i class="fa fa-money"></i></div>
            </div>
        </div>
    </section>

    <div class="pp-highlights">
        <div class="pp-highlight"><i class="fa fa-money"></i><strong>Thanh toán linh hoạt</strong><p>Nhiều phương thức thanh toán phù hợp với mọi nhu cầu.</p></div>
        <div class="pp-highlight"><i class="fa fa-lock"></i><strong>Bảo mật an toàn</strong><p>Thông tin giao dịch được bảo vệ tuân thủ tiêu chuẩn bảo mật.</p></div>
        <div class="pp-highlight"><i class="fa fa-check-circle"></i><strong>Xác nhận nhanh</strong><p>Giao dịch được xác nhận nhanh chóng, tiết kiệm thời gian.</p></div>
        <div class="pp-highlight"><i class="fa fa-headphones"></i><strong>Hỗ trợ 24/7</strong><p>Đội ngũ CSKH luôn sẵn sàng hỗ trợ mọi thắc mắc.</p></div>
    </div>

    {{-- 1. Phương thức thanh toán --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">1</span> Phương thức thanh toán</h2>
        <div class="pp-scope">
            @foreach([['i'=>'fa-money','t'=>'COD<br>Thanh toán khi nhận hàng'],['i'=>'fa-university','t'=>'Thẻ ATM /<br>Internet Banking'],['i'=>'fa-credit-card','t'=>'Thẻ tín dụng /<br>ghi nợ'],['i'=>'fa-mobile','t'=>'Ví điện tử'],['i'=>'fa-dollar','t'=>'Trả góp 0%']] as $s)
            <div class="pp-scope-item"><i class="fa {{ $s['i'] }}"></i><strong>{!! $s['t'] !!}</strong></div>
            @endforeach
        </div>
    </div>

    {{-- 2. Hướng dẫn thanh toán --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">2</span> Hướng dẫn thanh toán</h2>
        <div class="pp-steps">
            @foreach([['n'=>'01','t'=>'Thêm sản phẩm','d'=>'Thêm sản phẩm vào giỏ hàng.'],['n'=>'02','t'=>'Xác nhận đơn hàng','d'=>'Kiểm tra thông tin và xác nhận đơn hàng.'],['n'=>'03','t'=>'Chọn phương thức','d'=>'Chọn phương thức thanh toán phù hợp.'],['n'=>'04','t'=>'Hoàn tất thanh toán','d'=>'Thanh toán và xác nhận đơn hàng.']] as $i=>$st)
            <div class="pp-step">
                <div class="pp-step-num">{{ $st['n'] }}</div>
                <strong>{{ $st['t'] }}</strong>
                <p>{{ $st['d'] }}</p>
                @if($i<3)<span class="pp-step-arrow"><i class="fa fa-long-arrow-right"></i></span>@endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- 3. Quy định thanh toán --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">3</span> Quy định thanh toán</h2>
        <div class="pp-bullet-card">
            <ul>
                <li><i class="fa fa-check"></i> Khách hàng kiểm tra đúng thông tin đơn hàng trước khi thanh toán.</li>
                <li><i class="fa fa-check"></i> Thanh toán đủ giá trị đơn hàng theo thông tin hiển thị.</li>
                <li><i class="fa fa-check"></i> Đơn hàng chỉ được xử lý sau khi xác nhận thanh toán thành công.</li>
                <li><i class="fa fa-check"></i> Đối với COD, thanh toán khi nhận hàng và kiểm tra sản phẩm.</li>
                <li><i class="fa fa-check"></i> Chuyển khoản: chờ nội dung/mã đơn hàng để được xác nhận.</li>
            </ul>
        </div>
    </div>

    {{-- 4. Lưu ý theo từng phương thức --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">4</span> Lưu ý theo từng phương thức</h2>
        <div class="pp-bullet-card">
            <ul>
                <li><i class="fa fa-exclamation-triangle" style="color:#f59e0b"></i> <strong>COD:</strong> Chuẩn bị đủ tiền mặt, kiểm tra hàng trước khi thanh toán.</li>
                <li><i class="fa fa-exclamation-triangle" style="color:#f59e0b"></i> <strong>Chuyển khoản:</strong> Ghi đúng mã đơn hàng để được xác nhận nhanh.</li>
                <li><i class="fa fa-exclamation-triangle" style="color:#f59e0b"></i> <strong>Ví điện tử:</strong> Kiểm tra số dư, liên kết tài khoản hợp lệ trước khi thanh toán.</li>
                <li><i class="fa fa-exclamation-triangle" style="color:#f59e0b"></i> <strong>Thẻ:</strong> Hỗ trợ thẻ nội địa & quốc tế, kiểm tra giới hạn giao dịch.</li>
                <li><i class="fa fa-exclamation-triangle" style="color:#f59e0b"></i> <strong>Trả góp 0%:</strong> Áp dụng với thẻ tín dụng ngân hàng đối tác, kỳ hạn 3 - 12 tháng.</li>
            </ul>
        </div>
    </div>

    {{-- 5. Bảng thông tin phương thức --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">5</span> Bảng thông tin phương thức</h2>
        <table class="pp-table">
            <thead><tr><th>Phương thức</th><th>Thời gian xử lý</th><th>Thời gian xác nhận</th><th>Phí giao dịch</th><th>Ghi chú</th></tr></thead>
            <tbody>
                <tr><td>Thanh toán khi nhận hàng</td><td>Ngay khi đặt hàng</td><td>Khi nhận hàng</td><td>Miễn phí</td><td></td></tr>
                <tr><td>Thẻ ATM / Internet Banking</td><td>1 - 2 giờ</td><td>5 - 30 phút</td><td>Vài giây</td><td></td></tr>
                <tr><td>Thẻ tín dụng / ghi nợ</td><td>Tức thì</td><td>5 - 30 phút</td><td>Miễn phí</td><td>Hỗ trợ Visa, Master, JCB</td></tr>
                <tr><td>Ví điện tử (Momo, ZaloPay...)</td><td>Tức thì</td><td>5 - 30 phút</td><td>Miễn phí</td><td>Tùy vào khuyến mãi</td></tr>
                <tr><td>Trả góp 0% bằng thẻ</td><td>1 - 2 giờ</td><td>Tức thì</td><td>Miễn phí</td><td>Kỳ hạn 3 - 12 tháng</td></tr>
            </tbody>
        </table>
    </div>

    {{-- 6. Thanh toán không thành công --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">6</span> Trường hợp thanh toán không thành công</h2>
        <div class="pp-bullet-card">
            <ul>
                <li><i class="fa fa-times" style="color:#dc2626"></i> Nội dung chuyển khoản sai hoặc không đúng mã đơn hàng.</li>
                <li><i class="fa fa-times" style="color:#dc2626"></i> Giao dịch bị ngân hàng hoặc ví điện tử từ chối.</li>
                <li><i class="fa fa-times" style="color:#dc2626"></i> Ví điện tử không đủ số dư hoặc tài khoản chưa liên kết.</li>
                <li><i class="fa fa-times" style="color:#dc2626"></i> Lỗi kết nối mạng trong quá trình thanh toán.</li>
                <li><i class="fa fa-times" style="color:#dc2626"></i> Quá thời gian thanh toán theo quy định của đơn hàng.</li>
            </ul>
        </div>
    </div>

    {{-- 7. FAQ --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">7</span> Câu hỏi thường gặp</h2>
        <div class="pp-faq-list">
            @foreach(['Thanh toán COD có mất thêm phí không?','Sau bao lâu đơn hàng được xác nhận khi chuyển khoản?','Tôi có thể thanh toán bằng mấy thẻ tín dụng?','Làm gì nếu chuyển khoản nhưng chưa được xác nhận?','DangDuc có hỗ trợ trả góp 0% không?'] as $q)
            <div class="pp-faq-item"><a href="#"><i class="fa fa-chevron-right"></i> {{ $q }}</a></div>
            @endforeach
        </div>
    </div>

    {{-- CTA --}}
    <div class="pp-cta">
        <h3>Cần hỗ trợ thanh toán?</h3>
        <p>Đội ngũ DangDuc luôn sẵn sàng hỗ trợ bạn nhanh chóng và tận tâm.</p>
        <div class="pp-cta-buttons">
            <a href="#" class="pp-cta-btn primary"><i class="fa fa-phone"></i> Liên hệ hỗ trợ<br><small>Hotline 1900 1234</small></a>
            <a href="#" class="pp-cta-btn"><i class="fa fa-search"></i> Kiểm tra đơn hàng<br><small>Nhập mã đơn hàng</small></a>
            <a href="#" class="pp-cta-btn"><i class="fa fa-phone"></i> Gọi ngay cho chúng tôi<br><small>1900 1234</small></a>
        </div>
    </div>

    <div class="pp-policies">
        <div class="pp-pol"><i class="fa fa-truck"></i><div><strong>Miễn phí giao hàng</strong><span>Nội thành đơn từ 300K</span></div></div>
        <div class="pp-pol"><i class="fa fa-refresh"></i><div><strong>Đổi trả dễ dàng</strong><span>Đổi trả trong 7 ngày nếu sản phẩm lỗi</span></div></div>
        <div class="pp-pol"><i class="fa fa-shield"></i><div><strong>Bảo hành chính hãng</strong><span>Cam kết bảo hành minh bạch</span></div></div>
        <div class="pp-pol"><i class="fa fa-lock"></i><div><strong>Thanh toán an toàn</strong><span>Nhiều phương thức thanh toán</span></div></div>
        <div class="pp-pol"><i class="fa fa-headphones"></i><div><strong>Hỗ trợ tận tâm 24/7</strong><span>Đội ngũ tư vấn luôn sẵn sàng</span></div></div>
    </div>
</div>
@endsection
