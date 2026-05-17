@extends('pages.guide.content-page')

@section('support_page_class', 'guide-page')
@section('support_title', 'Hướng dẫn mua hàng')
@section('support_meta_desc', 'Hướng dẫn mua hàng tại DangDuc.vn - Đặt hàng dễ dàng, nhanh chóng và chính xác')
@section('support_meta_keywords', 'hướng dẫn mua hàng, cách đặt hàng, DangDuc')
@section('support_canonical', '/huong-dan-mua-hang')

@section('support_content')

    <section class="pp-hero">
        <div class="pp-hero-copy">
            <h1>Hướng dẫn mua hàng</h1>
            <p>Trang hướng dẫn giúp bạn dễ dàng đặt hàng trên DangDuc.vn với các bước đơn giản, nhanh chóng và chính xác.</p>
            <div class="pp-update"><i class="fa fa-refresh"></i> Cập nhật lần cuối: 01/2025</div>
        </div>
        <div class="pp-hero-art">
            <div class="pp-icon-box" style="background:rgba(13,148,136,.15);color:#14b8a6"><i class="fa fa-shopping-cart"></i></div>
            <div style="display:flex;flex-direction:column;gap:10px">
                <div class="pp-icon-sm" style="background:rgba(59,130,246,.15);color:#3b82f6"><i class="fa fa-laptop"></i></div>
                <div class="pp-icon-sm" style="background:rgba(16,185,129,.15);color:#10b981"><i class="fa fa-check-circle"></i></div>
            </div>
        </div>
    </section>

    <div class="pp-highlights">
        <div class="pp-highlight"><i class="fa fa-shopping-cart"></i><strong>Mua hàng nhanh chóng</strong><p>Quy trình đơn giản, tiết kiệm thời gian.</p></div>
        <div class="pp-highlight"><i class="fa fa-credit-card"></i><strong>Thanh toán linh hoạt</strong><p>Đa dạng phương thức, an toàn và tiện lợi.</p></div>
        <div class="pp-highlight"><i class="fa fa-map-marker"></i><strong>Theo dõi đơn dễ dàng</strong><p>Tra cứu trạng thái đơn hàng mọi lúc, mọi nơi.</p></div>
        <div class="pp-highlight"><i class="fa fa-headphones"></i><strong>Hỗ trợ tận tâm</strong><p>Đội ngũ CSKH luôn sẵn sàng hỗ trợ 24/7.</p></div>
    </div>

    {{-- Quy trình mua hàng --}}
    <div class="pp-section" style="text-align:center;margin-bottom:24px">
        <h2 style="font-size:20px;font-weight:700;color:#0f172a">Quy trình mua hàng tại DangDuc.vn</h2>
    </div>

    <div class="pp-terms-grid" style="margin-bottom:24px">
        <div class="pp-terms-card">
            <h4><span class="pp-num">1</span> Tìm kiếm sản phẩm</h4>
            <ul>
                <li><i class="fa fa-check"></i> Sử dụng menu, danh mục hoặc thanh tìm kiếm.</li>
                <li><i class="fa fa-check"></i> Lọc theo thương hiệu, giá, cấu hình...</li>
                <li><i class="fa fa-check"></i> Xem nhanh thông tin sản phẩm để chọn sản phẩm phù hợp.</li>
            </ul>
        </div>
        <div class="pp-terms-card">
            <h4><span class="pp-num">2</span> Xem thông tin chi tiết</h4>
            <ul>
                <li><i class="fa fa-check"></i> Kiểm tra cấu hình, thông số kỹ thuật.</li>
                <li><i class="fa fa-check"></i> Xem giá bán, khuyến mãi hiện có.</li>
                <li><i class="fa fa-check"></i> Kiểm tra tình trạng còn hàng và các chính sách liên quan.</li>
            </ul>
        </div>
        <div class="pp-terms-card">
            <h4><span class="pp-num">3</span> Thêm vào giỏ hàng</h4>
            <ul>
                <li><i class="fa fa-check"></i> Chọn phiên bản, màu sắc, phụ kiện (nếu có).</li>
                <li><i class="fa fa-check"></i> Chọn số lượng phù hợp.</li>
                <li><i class="fa fa-check"></i> Nhấn "Thêm vào giỏ hàng".</li>
            </ul>
        </div>
        <div class="pp-terms-card">
            <h4><span class="pp-num">4</span> Kiểm tra giỏ hàng</h4>
            <ul>
                <li><i class="fa fa-check"></i> Kiểm tra danh sách sản phẩm, số lượng và tùy chọn.</li>
                <li><i class="fa fa-check"></i> Nhập mã giảm giá (nếu có).</li>
                <li><i class="fa fa-check"></i> Kiểm tra tổng tiền và phí vận chuyển dự kiến.</li>
            </ul>
        </div>
        <div class="pp-terms-card">
            <h4><span class="pp-num">5</span> Nhập thông tin nhận hàng</h4>
            <ul>
                <li><i class="fa fa-check"></i> Nhập họ tên, số điện thoại.</li>
                <li><i class="fa fa-check"></i> Nhập địa chỉ giao hàng chính xác.</li>
                <li><i class="fa fa-check"></i> Thêm ghi chú cho đơn hàng (nếu cần).</li>
            </ul>
        </div>
        <div class="pp-terms-card">
            <h4><span class="pp-num">6</span> Chọn phương thức thanh toán</h4>
            <ul>
                <li><i class="fa fa-check"></i> Chọn thanh toán khi nhận hàng (COD).</li>
                <li><i class="fa fa-check"></i> Hoặc chuyển khoản ngân hàng.</li>
                <li><i class="fa fa-check"></i> Hoặc thanh toán online qua thẻ/ví.</li>
            </ul>
        </div>
        <div class="pp-terms-card">
            <h4><span class="pp-num">7</span> Xác nhận đặt hàng</h4>
            <ul>
                <li><i class="fa fa-check"></i> Kiểm tra lại toàn bộ thông tin đơn hàng.</li>
                <li><i class="fa fa-check"></i> Đồng ý với điều kiện mua hàng.</li>
                <li><i class="fa fa-check"></i> Nhấn "Đặt hàng" để hoàn tất.</li>
            </ul>
        </div>
        <div class="pp-terms-card">
            <h4><span class="pp-num">8</span> Theo dõi đơn hàng</h4>
            <ul>
                <li><i class="fa fa-check"></i> Sử dụng mã đơn hàng hoặc số điện thoại để tra cứu.</li>
                <li><i class="fa fa-check"></i> Theo dõi trạng thái: Đang xử lý, Đang giao, Hoàn tất...</li>
            </ul>
        </div>
        <div class="pp-terms-card">
            <h4><span class="pp-num">9</span> Nhận hàng và kiểm tra</h4>
            <ul>
                <li><i class="fa fa-check"></i> Kiểm tra bên trong trạng thái hàng.</li>
                <li><i class="fa fa-check"></i> Kiểm tra sản phẩm, phụ kiện, hóa đơn.</li>
                <li><i class="fa fa-check"></i> Liên hệ hỗ trợ nếu có vấn đề phát sinh.</li>
            </ul>
        </div>
    </div>

    {{-- Step 10 full width --}}
    <div class="pp-section">
        <div class="pp-terms-card" style="max-width:100%">
            <h4><span class="pp-num">10</span> Hỗ trợ sau mua</h4>
            <ul>
                <li><i class="fa fa-check"></i> Hỗ trợ bảo hành sản phẩm.</li>
                <li><i class="fa fa-check"></i> Xuất hóa đơn VAT, tư vấn kỹ thuật.</li>
                <li><i class="fa fa-check"></i> Hỗ trợ đổi trả theo chính sách.</li>
            </ul>
        </div>
    </div>

    {{-- Lưu ý & FAQ --}}
    <div class="pp-conditions" style="margin-bottom:32px">
        <div class="pp-section">
            <h2 class="pp-section-title">Lưu ý khi mua hàng</h2>
            <div class="pp-cond-card">
                <ul>
                    <li><i class="fa fa-check"></i> Kiểm tra kỹ thông tin sản phẩm và các chương trình khuyến mãi.</li>
                    <li><i class="fa fa-check"></i> Đảm bảo số điện thoại và địa chỉ nhận hàng chính xác.</li>
                    <li><i class="fa fa-check"></i> Không chia sẻ OTP hoặc thông tin thanh toán cho bên thứ 3.</li>
                    <li><i class="fa fa-check"></i> Chỉ thanh toán qua kênh chính thức của DangDuc.</li>
                </ul>
            </div>
        </div>
        <div class="pp-section">
            <h2 class="pp-section-title">Câu hỏi thường gặp</h2>
            <div class="pp-faq-list" style="grid-template-columns:1fr">
                @foreach(['Tôi có cần tài khoản để đặt hàng không?','Tôi có thể đặt hàng qua điện thoại không?','Sau khi đặt hàng tôi có thể thay đổi thông tin không?','Làm sao để kiểm tra tình trạng đơn hàng?','Khi nhận hàng tôi cần lưu ý những gì?'] as $q)
                <div class="pp-faq-item"><a href="#"><i class="fa fa-chevron-right"></i> {{ $q }}</a></div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- CTA --}}
    <div class="pp-cta">
        <h3>Cần hỗ trợ đặt hàng?</h3>
        <p>Đội ngũ DangDuc luôn sẵn sàng hỗ trợ bạn nhanh chóng và tận tâm.</p>
        <div class="pp-cta-buttons">
            <a href="#" class="pp-cta-btn primary"><i class="fa fa-phone"></i> Hotline 24/7<br><small>1900 1234</small><br><small>08:00 - 21:00 (T2 - CN)</small></a>
            <a href="#" class="pp-cta-btn"><i class="fa fa-comments"></i> Trung tâm hỗ trợ<br><small>Hỗ trợ trực tuyến</small><br><small>Chat ngay với chúng tôi</small></a>
            <a href="#" class="pp-cta-btn"><i class="fa fa-envelope"></i> Liên hệ tư vấn<br><small>support@dangduc.vn</small><br><small>Phản hồi trong 30 phút</small></a>
        </div>
    </div>

    <div class="pp-policies">
        <div class="pp-pol"><i class="fa fa-truck"></i><div><strong>Miễn phí giao hàng</strong><span>Nội thành đơn từ 300K</span></div></div>
        <div class="pp-pol"><i class="fa fa-refresh"></i><div><strong>Đổi trả dễ dàng</strong><span>Đổi trả trong 7 ngày nếu sản phẩm lỗi</span></div></div>
        <div class="pp-pol"><i class="fa fa-shield"></i><div><strong>Bảo hành chính hãng</strong><span>Cam kết bảo hành minh bạch</span></div></div>
        <div class="pp-pol"><i class="fa fa-lock"></i><div><strong>Thanh toán an toàn</strong><span>Nhiều phương thức thanh toán</span></div></div>
        <div class="pp-pol"><i class="fa fa-headphones"></i><div><strong>Hỗ trợ tận tâm 24/7</strong><span>Đội ngũ tư vấn luôn sẵn sàng</span></div></div>
    </div>
@endsection
