@extends('pages.support.content-page')
@section('support_page_class', 'van-chuyen-page')
@section('support_title', 'Chính sách vận chuyển')
@section('support_meta_desc', 'Chính sách vận chuyển tại DangDuc.vn - Giao hàng nhanh, an toàn trên toàn quốc')
@section('support_meta_keywords', 'vận chuyển, chính sách vận chuyển, DangDuc, giao hàng')
@section('support_canonical', '/chinh-sach-van-chuyen')
@section('support_content')

    <section class="pp-hero">
        <div class="pp-hero-copy">
            <h1>Chính sách vận chuyển</h1>
            <p>DangDuc cam kết giao hàng nhanh chóng, an toàn và đúng hẹn đến mọi khách hàng trên toàn quốc. Chúng tôi hợp tác với các đơn vị vận chuyển uy tín để đảm bảo trải nghiệm mua sắm thuận tiện và tin cậy nhất.</p>
        </div>
        <div class="pp-hero-art">
            <div class="pp-icon-box" style="background:rgba(13,148,136,.15);color:#14b8a6"><i class="fa fa-truck"></i></div>
            <div style="display:flex;flex-direction:column;gap:10px">
                <div class="pp-icon-sm" style="background:rgba(59,130,246,.15);color:#3b82f6"><i class="fa fa-map-marker"></i></div>
                <div class="pp-icon-sm" style="background:rgba(16,185,129,.15);color:#10b981"><i class="fa fa-check-circle"></i></div>
            </div>
        </div>
    </section>

    <div class="pp-highlights">
        <div class="pp-highlight"><i class="fa fa-globe"></i><strong>Giao hàng toàn quốc</strong><p>Phục vụ 63 tỉnh thành, đến tận nơi nhanh chóng.</p></div>
        <div class="pp-highlight"><i class="fa fa-bolt"></i><strong>Nội thành giao nhanh</strong><p>Giao trong ngày tại nội thành Hà Nội & TP. Hồ Chí Minh.</p></div>
        <div class="pp-highlight"><i class="fa fa-map-marker"></i><strong>Theo dõi đơn hàng</strong><p>Cập nhật trạng thái đơn hàng mọi lúc, mọi nơi.</p></div>
        <div class="pp-highlight"><i class="fa fa-headphones"></i><strong>Hỗ trợ 24/7</strong><p>Đội ngũ hỗ trợ sẵn sàng giải đáp mọi thắc mắc.</p></div>
    </div>

    {{-- 1. Phạm vi vận chuyển --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">1</span> Phạm vi vận chuyển</h2>
        <div class="pp-scope pp-scope-5">
            @foreach([['i'=>'fa-building','t'=>'Nội thành<br>Hà Nội & HCM'],['i'=>'fa-building-o','t'=>'Ngoại thành<br>Hà Nội & HCM'],['i'=>'fa-map','t'=>'Các tỉnh<br>Miền Bắc'],['i'=>'fa-map-o','t'=>'Các tỉnh<br>Miền Trung'],['i'=>'fa-map-signs','t'=>'Huyện / Xã<br>vùng xa']] as $s)
            <div class="pp-scope-item"><i class="fa {{ $s['i'] }}"></i><strong>{!! nl2br(e($s['t'])) !!}</strong></div>
            @endforeach
        </div>
    </div>

    {{-- 2. Thời gian giao hàng --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">2</span> Thời gian giao hàng</h2>
        <table class="pp-table">
            <thead><tr><th>Khu vực</th><th>Thời gian dự kiến</th></tr></thead>
            <tbody>
                <tr><td>Nội thành Hà Nội & HCM</td><td>2 - 6 giờ</td></tr>
                <tr><td>Ngoại thành Hà Nội & HCM</td><td>1 - 2 ngày</td></tr>
                <tr><td>Các tỉnh Miền Bắc</td><td>1 - 3 ngày</td></tr>
                <tr><td>Các tỉnh Miền Trung</td><td>2 - 4 ngày</td></tr>
                <tr><td>Các tỉnh Miền Nam</td><td>2 - 4 ngày</td></tr>
                <tr><td>Huyện / Xã vùng xa</td><td>3 - 7 ngày</td></tr>
            </tbody>
        </table>
        <p style="font-size:11.5px;color:#94a3b8;margin:6px 0 0;font-style:italic">* Thời gian giao hàng có thể thay đổi tùy điều kiện thực tế.</p>
    </div>

    {{-- 3. Phí vận chuyển --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">3</span> Phí vận chuyển</h2>
        <div class="pp-bullet-card">
            <ul>
                <li><i class="fa fa-check"></i> <strong>Miễn phí giao hàng:</strong> Đơn hàng từ 300.000đ trở lên (áp dụng nội thành Hà Nội & TP. Hồ Chí Minh và một số khu vực).</li>
                <li><i class="fa fa-check"></i> <strong>Tính phí theo khu vực:</strong> Áp dụng cho đơn hàng dưới 300.000đ giao đến các tỉnh thành khác.</li>
                <li><i class="fa fa-check"></i> <strong>Phụ phí hàng cồng kềnh:</strong> Sản phẩm có kích thước/khối lượng lớn sẽ phụ thu theo quy định của đơn vị vận chuyển.</li>
            </ul>
            <p style="font-size:12px;margin:8px 0 0"><a href="#" style="color:#0d9488;text-decoration:none"><i class="fa fa-angle-right"></i> Xem chi tiết bảng phí vận chuyển tại đây</a></p>
        </div>
    </div>

    {{-- 4. Quy trình giao hàng --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">4</span> Quy trình giao hàng</h2>
        <div class="pp-steps">
            @foreach([['n'=>'01','t'=>'Xác nhận đơn','d'=>'Xác nhận thông tin đơn hàng với khách.'],['n'=>'02','t'=>'Đóng gói sản phẩm','d'=>'Đóng gói cẩn thận, đảm bảo an toàn và sạp nhà mã đơn.'],['n'=>'03','t'=>'Bàn giao vận chuyển','d'=>'Giao cho đơn vị vận chuyển và vận chuyển về sạp nhà mã đơn.'],['n'=>'04','t'=>'Giao hàng thành công','d'=>'Giao đến tay khách hàng và hoàn tất đơn.']] as $i=>$st)
            <div class="pp-step">
                <div class="pp-step-num">{{ $st['n'] }}</div>
                <strong>{{ $st['t'] }}</strong>
                <p>{{ $st['d'] }}</p>
                @if($i<3)<span class="pp-step-arrow"><i class="fa fa-long-arrow-right"></i></span>@endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- 5. Điều kiện giao hàng --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">5</span> Điều kiện giao hàng</h2>
        <div class="pp-bullet-card">
            <ul>
                <li><i class="fa fa-check"></i> Địa chỉ giao hàng rõ ràng, chính xác.</li>
                <li><i class="fa fa-check"></i> Người nhận có mặt tại địa chỉ trong giờ giao hàng.</li>
                <li><i class="fa fa-check"></i> Sản phẩm không thuộc danh mục cấm vận chuyển.</li>
                <li><i class="fa fa-check"></i> Thanh toán đầy đủ (đối với đơn COD).</li>
                <li><i class="fa fa-check"></i> Hỗ trợ giao hàng lên tầng (nếu không có thang máy).</li>
                <li style="font-size:12px;margin-top:4px"><a href="#" style="color:#0d9488;text-decoration:none"><i class="fa fa-angle-right"></i> Xem chi tiết điều kiện giao hàng</a></li>
            </ul>
        </div>
    </div>

    {{-- 6. Lưu ý khi nhận hàng --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">6</span> Lưu ý khi nhận hàng</h2>
        <div class="pp-bullet-card">
            <ul>
                <li><i class="fa fa-check"></i> Kiểm tra tình trạng bao bì trước khi nhận hàng.</li>
                <li><i class="fa fa-check"></i> Quay video khi mở hộp để đảm bảo quyền lợi.</li>
                <li><i class="fa fa-check"></i> Từ chối nhận hàng nếu bao bì sách, méo, ẩm ướt, hư hỏng.</li>
                <li><i class="fa fa-check"></i> Hẹn giao lại nếu không có mặt tại thời điểm giao hàng.</li>
                <li><i class="fa fa-check"></i> Cung cấp thông tin đầy đủ để tránh giao hàng chậm trễ.</li>
            </ul>
        </div>
    </div>

    {{-- 7. FAQ --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">7</span> Câu hỏi thường gặp</h2>
        <div class="pp-faq-list">
            @foreach(['Đơn hàng khi nào được giao?','Tôi có thể thay đổi địa chỉ giao hàng không?','Phí vận chuyển được tính như thế nào?','Đơn hàng có được giao vào cuối tuần & ngày lễ không?','Làm sao để theo dõi đơn hàng của tôi?'] as $q)
            <div class="pp-faq-item"><a href="#"><i class="fa fa-chevron-right"></i> {{ $q }}</a></div>
            @endforeach
        </div>
    </div>

    {{-- CTA --}}
    <div class="pp-cta">
        <h3>Cần hỗ trợ vận chuyển?</h3>
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
@endsection
