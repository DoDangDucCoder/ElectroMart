@extends('pages.support.content-page')
@section('support_page_class', 'doi-tra-page')
@section('support_title', 'Chính sách đổi trả')
@section('support_meta_desc', 'Chính sách đổi trả sản phẩm tại DangDuc.vn - Đổi trả dễ dàng, hoàn tiền minh bạch')
@section('support_meta_keywords', 'đổi trả, chính sách đổi trả, DangDuc, hoàn tiền')
@section('support_canonical', '/chinh-sach-doi-tra')
@section('support_content')

    {{-- Hero --}}
    <section class="pp-hero">
        <div class="pp-hero-copy">
            <h1>Chính sách đổi trả</h1>
            <p>DangDuc cam kết mang đến trải nghiệm mua sắm minh bạch và an tâm. Chính sách đổi trả rõ ràng giúp Quý khách yên tâm lựa chọn sản phẩm chính hãng, được hỗ trợ tận tâm và nhanh chóng khi cần.</p>
        </div>
        <div class="pp-hero-art">
            <div class="pp-icon-box" style="background:rgba(13,148,136,.15);color:#14b8a6"><i class="fa fa-refresh"></i></div>
            <div style="display:flex;flex-direction:column;gap:10px">
                <div class="pp-icon-sm" style="background:rgba(59,130,246,.15);color:#3b82f6"><i class="fa fa-laptop"></i></div>
                <div class="pp-icon-sm" style="background:rgba(16,185,129,.15);color:#10b981"><i class="fa fa-cube"></i></div>
            </div>
        </div>
    </section>

    {{-- Highlights --}}
    <div class="pp-highlights">
        <div class="pp-highlight"><i class="fa fa-refresh"></i><strong>Đổi trả dễ dàng</strong><p>Quy trình đơn giản, hỗ trợ nhanh chóng</p></div>
        <div class="pp-highlight"><i class="fa fa-money"></i><strong>Hoàn tiền minh bạch</strong><p>Hoàn tiền nhanh chóng, minh bạch, rõ ràng</p></div>
        <div class="pp-highlight"><i class="fa fa-headphones"></i><strong>Hỗ trợ nhanh</strong><p>Đội ngũ CSKH 24/7 sẵn sàng hỗ trợ bạn</p></div>
        <div class="pp-highlight"><i class="fa fa-search"></i><strong>Kiểm tra sản phẩm</strong><p>Kiểm tra kỹ lưỡng trước khi gửi đến khách hàng</p></div>
    </div>

    {{-- 1. Phạm vi đổi trả --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">1</span> Phạm vi đổi trả</h2>
        <div class="pp-scope">
            @foreach([['i'=>'fa-laptop','t'=>'Laptop &<br>Workstation'],['i'=>'fa-desktop','t'=>'PC &<br>Máy bộ'],['i'=>'fa-television','t'=>'Màn hình'],['i'=>'fa-microchip','t'=>'Linh kiện'],['i'=>'fa-keyboard-o','t'=>'Phụ kiện'],['i'=>'fa-windows','t'=>'Phần mềm<br>& hỗ trợ']] as $s)
            <div class="pp-scope-item"><i class="fa {{ $s['i'] }}"></i><strong>{!! nl2br(e($s['t'])) !!}</strong></div>
            @endforeach
        </div>
    </div>

    {{-- 2. Thời gian đổi trả --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">2</span> Thời gian đổi trả</h2>
        <table class="pp-table">
            <thead><tr><th>Nhóm sản phẩm</th><th>Thời gian đổi trả</th></tr></thead>
            <tbody>
                <tr><td>Laptop & Workstation</td><td>15 ngày</td></tr>
                <tr><td>PC & Máy bộ</td><td>15 ngày</td></tr>
                <tr><td>Màn hình</td><td>07 ngày</td></tr>
                <tr><td>Linh kiện (RAM, SSD, HDD, VGA, Mainboard...)</td><td>07 ngày</td></tr>
                <tr><td>Phụ kiện (Chuột, bàn phím, tai nghe, loa...)</td><td>07 ngày</td></tr>
                <tr><td>Phần mềm (Windows, Office...)</td><td>Không hỗ trợ đổi trả</td></tr>
            </tbody>
        </table>
    </div>

    {{-- 3 & 4: Conditions --}}
    <div class="pp-conditions">
        <div class="pp-section">
            <h2 class="pp-section-title"><span class="pp-num">3</span> Điều kiện được đổi trả</h2>
            <div class="pp-cond-card">
                <ul>
                    <li><i class="fa fa-check"></i> Sản phẩm còn trong thời hạn đổi trả.</li>
                    <li><i class="fa fa-check"></i> Sản phẩm còn nguyên vẹn, chưa qua sử dụng.</li>
                    <li><i class="fa fa-check"></i> Đầy đủ hộp, phụ kiện, tài liệu và hóa đơn mua hàng.</li>
                    <li><i class="fa fa-check"></i> Không có dấu hiệu rơi vỡ, trầy xước, cháy nổ, và đập hoặc tác động vật lý.</li>
                    <li><i class="fa fa-check"></i> Tem bảo hành, mã serial/IMEI nguyên vẹn, rõ ràng.</li>
                </ul>
            </div>
        </div>
        <div class="pp-section">
            <h2 class="pp-section-title"><span class="pp-num">4</span> Các trường hợp không áp dụng</h2>
            <div class="pp-cond-card">
                <ul>
                    <li><i class="fa fa-times"></i> Sản phẩm hết thời hạn đổi trả.</li>
                    <li><i class="fa fa-times"></i> Sản phẩm đã qua sử dụng hoặc kích hoạt.</li>
                    <li><i class="fa fa-times"></i> Thiếu phụ kiện, hộp, hóa đơn hoặc tem niêm phong.</li>
                    <li><i class="fa fa-times"></i> Hư hỏng do người dùng, lỗi kỹ thuật không do NSX.</li>
                    <li><i class="fa fa-times"></i> Sản phẩm thuộc chương trình khuyến mãi đặc biệt.</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- 5. Quy trình đổi trả --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">5</span> Quy trình đổi trả</h2>
        <div class="pp-steps">
            @foreach([['n'=>'01','t'=>'Gửi yêu cầu','d'=>'Liên hệ CSKH hoặc gửi yêu cầu đổi trả trên website.'],['n'=>'02','t'=>'Xác minh','d'=>'Chúng tôi kiểm tra thông tin và điều kiện đổi trả sản phẩm.'],['n'=>'03','t'=>'Gửi/nhận','d'=>'Gửi sản phẩm theo hướng dẫn hoặc nhân viên đến lấy hàng (nếu có).'],['n'=>'04','t'=>'Đổi mới / Hoàn tiền','d'=>'Đổi sản phẩm mới hoặc hoàn tiền theo chính sách.']] as $i=>$st)
            <div class="pp-step">
                <div class="pp-step-num">{{ $st['n'] }}</div>
                <strong>{{ $st['t'] }}</strong>
                <p>{{ $st['d'] }}</p>
                @if($i<3)<span class="pp-step-arrow"><i class="fa fa-long-arrow-right"></i></span>@endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- 6. Hoàn tiền & chi phí --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">6</span> Hoàn tiền & chi phí</h2>
        <div class="pp-info-grid">
            <div class="pp-info-card"><i class="fa fa-money"></i><strong>Hình thức hoàn tiền</strong><span>Chuyển khoản ngân hàng hoặc hoàn về ví thanh toán (nếu có).</span></div>
            <div class="pp-info-card"><i class="fa fa-clock-o"></i><strong>Thời gian xử lý</strong><span>Trong 3 – 7 ngày làm việc kể từ khi nhận được sản phẩm hợp lệ.</span></div>
            <div class="pp-info-card"><i class="fa fa-truck"></i><strong>Chi phí vận chuyển</strong><span>DangDuc hỗ trợ phí vận chuyển trong trường hợp lỗi do nhà bán hoặc nhà sản xuất.</span></div>
            <div class="pp-info-card"><i class="fa fa-life-ring"></i><strong>Kênh hỗ trợ</strong><span>Hotline, Email, Chat trực tuyến sẵn sàng hỗ trợ 24/7.</span></div>
        </div>
    </div>

    {{-- 7. FAQ --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">7</span> Câu hỏi thường gặp</h2>
        <div class="pp-faq-list">
            @foreach(['Bao lâu tôi nhận được tiền hoàn sau khi gửi trả sản phẩm?','Chi phí vận chuyển khi đổi trả được xử lý như thế nào?','Tôi có thể đổi sang sản phẩm khác thay vì hoàn tiền không?','Sản phẩm khuyến mãi có được đổi trả không?','Tôi gửi nhầm sản phẩm, DangDuc có hỗ trợ không?'] as $q)
            <div class="pp-faq-item"><a href="#"><i class="fa fa-chevron-right"></i> {{ $q }}</a></div>
            @endforeach
        </div>
    </div>

    {{-- CTA --}}
    <div class="pp-cta">
        <h3>Cần hỗ trợ đổi trả?</h3>
        <p>Đội ngũ DangDuc luôn sẵn sàng hỗ trợ bạn nhanh chóng và tận tâm.</p>
        <div class="pp-cta-buttons">
            <a href="#" class="pp-cta-btn primary"><i class="fa fa-pencil-square-o"></i> Yêu cầu đổi trả<br><small>Gửi yêu cầu ngay</small></a>
            <a href="#" class="pp-cta-btn"><i class="fa fa-phone"></i> Liên hệ hỗ trợ<br><small>Hotline 1900 1234</small></a>
            <a href="#" class="pp-cta-btn"><i class="fa fa-search"></i> Kiểm tra đơn hàng<br><small>Nhập mã đơn hàng</small></a>
        </div>
    </div>

    {{-- Policy strip --}}
    <div class="pp-policies">
        <div class="pp-pol"><i class="fa fa-truck"></i><div><strong>Miễn phí giao hàng</strong><span>Nội thành đơn từ 300K</span></div></div>
        <div class="pp-pol"><i class="fa fa-refresh"></i><div><strong>Đổi trả dễ dàng</strong><span>Đổi trả trong 7 ngày nếu sản phẩm lỗi</span></div></div>
        <div class="pp-pol"><i class="fa fa-shield"></i><div><strong>Bảo hành chính hãng</strong><span>Cam kết bảo hành minh bạch</span></div></div>
        <div class="pp-pol"><i class="fa fa-lock"></i><div><strong>Thanh toán an toàn</strong><span>Nhiều phương thức thanh toán</span></div></div>
        <div class="pp-pol"><i class="fa fa-headphones"></i><div><strong>Hỗ trợ tận tâm 24/7</strong><span>Đội ngũ tư vấn luôn sẵn sàng</span></div></div>
    </div>
@endsection
