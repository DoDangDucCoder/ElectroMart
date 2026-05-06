@extends('layouts.home-tech')
@section('body_class','policy-page dieu-khoan-page')
@php
$meta_desc=$meta_desc??'Điều khoản sử dụng website DangDuc.vn - Quy định quyền và nghĩa vụ khi sử dụng dịch vụ';
$meta_keywords=$meta_keywords??'điều khoản sử dụng, terms of service, DangDuc';
$url_canonical=$url_canonical??url('/dieu-khoan-su-dung');
$meta_title=$meta_title??'Điều khoản sử dụng - DangDuc';
@endphp
@push('styles')<link href="{{ asset('frontend/css/policy-page.css') }}" rel="stylesheet">@endpush
@section('content')
<div class="pp-wrap">
    @include('partials.storefront.breadcrumb', ['class' => 'pp-breadcrumb', 'separator' => '›', 'items' => [['label' => 'Điều khoản sử dụng', 'current' => true]]])

    <section class="pp-hero">
        <div class="pp-hero-copy">
            <h1>Điều khoản sử dụng</h1>
            <p>Điều khoản này quy định quyền và nghĩa vụ của bạn khi truy cập và sử dụng website DangDuc.vn. Chúng tôi cam kết mang đến môi trường mua sắm minh bạch, an toàn và tuân thủ pháp luật.</p>
            <div class="pp-update"><i class="fa fa-refresh"></i> Cập nhật lần cuối: 01/2025</div>
        </div>
        <div class="pp-hero-art">
            <div class="pp-icon-box" style="background:rgba(13,148,136,.15);color:#14b8a6"><i class="fa fa-file-text-o"></i></div>
            <div style="display:flex;flex-direction:column;gap:10px">
                <div class="pp-icon-sm" style="background:rgba(59,130,246,.15);color:#3b82f6"><i class="fa fa-gavel"></i></div>
                <div class="pp-icon-sm" style="background:rgba(16,185,129,.15);color:#10b981"><i class="fa fa-shield"></i></div>
            </div>
        </div>
    </section>

    <div class="pp-highlights">
        <div class="pp-highlight"><i class="fa fa-eye"></i><strong>Minh bạch</strong><p>Thông tin về quyền, nghĩa vụ và Công khai trên website.</p></div>
        <div class="pp-highlight"><i class="fa fa-heart"></i><strong>Bảo vệ quyền lợi</strong><p>Cam kết bảo vệ quyền lợi hợp pháp của khách hàng.</p></div>
        <div class="pp-highlight"><i class="fa fa-lock"></i><strong>Giao dịch an toàn</strong><p>Hệ thống bảo mật hiện đại, đảm bảo an toàn.</p></div>
        <div class="pp-highlight"><i class="fa fa-headphones"></i><strong>Hỗ trợ 24/7</strong><p>Đội ngũ chuyên viên luôn sẵn sàng giải đáp mọi thắc mắc.</p></div>
    </div>

    {{-- Sections 1-10 as cards grid --}}
    <div class="pp-terms-grid">
        {{-- 1. Phạm vi áp dụng --}}
        <div class="pp-terms-card">
            <h4><span class="pp-num">1</span> Phạm vi áp dụng</h4>
            <ul>
                <li><i class="fa fa-check"></i> Điều khoản này áp dụng cho mọi người dùng khi truy cập và sử dụng website DangDuc.vn.</li>
                <li><i class="fa fa-check"></i> Khi sử dụng website, bạn đồng ý tuân thủ tất cả các điều khoản được quy định tại đây.</li>
            </ul>
        </div>

        {{-- 2. Tài khoản --}}
        <div class="pp-terms-card">
            <h4><span class="pp-num">2</span> Tài khoản và thông tin khách hàng</h4>
            <ul>
                <li><i class="fa fa-check"></i> Khách hàng cung cấp thông tin cá nhân xác, đầy đủ, và cập nhật khi đăng ký tài khoản.</li>
                <li><i class="fa fa-check"></i> Bạn chịu trách nhiệm bảo mật thông tin đăng nhập và mọi hoạt động phát sinh trên tài khoản.</li>
            </ul>
        </div>

        {{-- 3. Quy định mua hàng --}}
        <div class="pp-terms-card">
            <h4><span class="pp-num">3</span> Quy định mua hàng</h4>
            <ul>
                <li><i class="fa fa-check"></i> Sản phẩm được hiển thị với thông tin về tên, giá cả và trang sản hàng.</li>
                <li><i class="fa fa-check"></i> DangDuc có quyền từ chối hoặc huỷ đơn hàng nếu có dấu hiệu gian lận, thông tin không hợp lệ - phương thức thanh toán.</li>
            </ul>
        </div>

        {{-- 4. Giá bán & thanh toán --}}
        <div class="pp-terms-card">
            <h4><span class="pp-num">4</span> Giá bán và thanh toán</h4>
            <ul>
                <li><i class="fa fa-check"></i> Giá sản phẩm được niêm yết bằng VNĐ và đã bao gồm thuế VAT (nếu có).</li>
                <li><i class="fa fa-check"></i> Khách hàng có thể thanh toán bằng các phương thức được hỗ trợ trên website.</li>
            </ul>
        </div>

        {{-- 5. Giao hàng, đổi trả --}}
        <div class="pp-terms-card">
            <h4><span class="pp-num">5</span> Giao hàng, đổi trả và bảo hành</h4>
            <ul>
                <li><i class="fa fa-check"></i> Giao hàng theo chính sách vận chuyển và phạm vi phục vụ tại đây, bao gồm, phần mở rộng.</li>
                <li><i class="fa fa-check"></i> Khách hàng vui lòng kiểm tra hàng và liên hệ ngay nếu có vấn đề phát sinh.</li>
            </ul>
        </div>

        {{-- 6. Bảo mật thông tin --}}
        <div class="pp-terms-card">
            <h4><span class="pp-num">6</span> Bảo mật thông tin</h4>
            <ul>
                <li><i class="fa fa-check"></i> DangDuc cam kết bảo mật thông tin cá nhân theo Chính sách bảo mật.</li>
                <li><i class="fa fa-check"></i> Thông tin chỉ được sử dụng cho mục đích xử lý đơn hàng và chăm sóc khách hàng.</li>
            </ul>
        </div>

        {{-- 7. Quyền sở hữu trí tuệ --}}
        <div class="pp-terms-card">
            <h4><span class="pp-num">7</span> Quyền sở hữu trí tuệ</h4>
            <ul>
                <li><i class="fa fa-check"></i> Toàn bộ nội dung trên website (hình ảnh, logo, thương hiệu...) thuộc quyền sở hữu của DangDuc.</li>
                <li><i class="fa fa-check"></i> Không được sao chép, sử dụng hoặc phân phối khi chưa được sự đồng ý.</li>
            </ul>
        </div>

        {{-- 8. Hành vi không được phép --}}
        <div class="pp-terms-card">
            <h4><span class="pp-num">8</span> Hành vi không được phép</h4>
            <ul>
                <li><i class="fa fa-times" style="color:#dc2626"></i> Không sử dụng website cho mục đích bất hợp pháp hoặc gây ảnh hưởng đến hệ thống.</li>
                <li><i class="fa fa-times" style="color:#dc2626"></i> Không đăng tải nội dung vi phạm pháp luật phần mềm có hại (virus).</li>
            </ul>
        </div>

        {{-- 9. Giới hạn trách nhiệm --}}
        <div class="pp-terms-card">
            <h4><span class="pp-num">9</span> Giới hạn trách nhiệm</h4>
            <ul>
                <li><i class="fa fa-check"></i> DangDuc không chịu trách nhiệm cho các thiệt hại phát sinh do sự cố Khách quan, ngoài tầm kiểm soát.</li>
                <li><i class="fa fa-check"></i> Website có thể gián đoạn, tạm nghỉ bảo trì hoặc thay đổi tính năng mà không cần thông báo trước.</li>
            </ul>
        </div>
    </div>

    {{-- 10. Thay đổi điều khoản --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">10</span> Thay đổi điều khoản</h2>
        <div class="pp-bullet-card">
            <ul>
                <li><i class="fa fa-check"></i> DangDuc có quyền cập nhật, chỉnh sửa điều khoản bất kỳ lúc nào.</li>
                <li><i class="fa fa-check"></i> Điều khoản mới sẽ có hiệu lực ngay khi được đăng tải trên website. Việc bạn tiếp tục sử dụng website đồng nghĩa với việc chấp nhận các thay đổi.</li>
            </ul>
        </div>
    </div>

    {{-- FAQ --}}
    <div class="pp-section">
        <h2 class="pp-section-title" style="margin-top:8px">Câu hỏi thường gặp</h2>
        <div class="pp-faq-list">
            @foreach(['Điều khoản sử dụng có áp dụng cho khách mua hàng tại cửa hàng không?','Tôi có thể thay đổi hoặc hủy đơn hàng sau khi đặt không?','DangDuc có quyền thay đổi điều khoản khi nào?','Tôi cần làm gì nếu phát hiện vi phạm điều khoản?','Làm thế nào để liên hệ khi có thắc mắc về điều khoản?'] as $q)
            <div class="pp-faq-item"><a href="#"><i class="fa fa-chevron-right"></i> {{ $q }}</a></div>
            @endforeach
        </div>
    </div>

    {{-- CTA --}}
    <div class="pp-cta">
        <h3>Cần hỗ trợ về điều khoản sử dụng?</h3>
        <p>Đội ngũ DangDuc luôn sẵn sàng hỗ trợ bạn nhanh chóng và tận tâm.</p>
        <div class="pp-cta-buttons">
            <a href="#" class="pp-cta-btn primary"><i class="fa fa-phone"></i> Hotline 24/7<br><small>1900 1234</small><br><small>8:00 - 22:00 (T2 - CN)</small></a>
            <a href="#" class="pp-cta-btn"><i class="fa fa-comments"></i> Trung tâm hỗ trợ<br><small>Hỗ trợ trực tuyến</small><br><small>Phản hồi trong 1 - 2 giờ</small></a>
            <a href="#" class="pp-cta-btn"><i class="fa fa-envelope"></i> Liên hệ tư vấn<br><small>support@dangduc.vn</small><br><small>Trả lời trong giờ làm việc</small></a>
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
