@extends('pages.support.content-page')
@section('support_page_class', 'bao-mat-page')
@section('support_title', 'Chính sách bảo mật')
@section('support_meta_desc', 'Chính sách bảo mật thông tin khách hàng tại DangDuc.vn')
@section('support_meta_keywords', 'bảo mật, chính sách bảo mật, DangDuc, privacy')
@section('support_canonical', '/chinh-sach-bao-mat')
@section('support_content')

    <section class="pp-hero">
        <div class="pp-hero-copy">
            <h1>Chính sách bảo mật</h1>
            <p>DangDuc cam kết bảo vệ thông tin cá nhân và dữ liệu giao dịch của khách hàng. Chúng tôi áp dụng các biện pháp kỹ thuật và quản lý để đảm bảo thông tin luôn được bảo mật, an toàn.</p>
        </div>
        <div class="pp-hero-art">
            <div class="pp-icon-box" style="background:rgba(13,148,136,.15);color:#14b8a6"><i class="fa fa-lock"></i></div>
            <div style="display:flex;flex-direction:column;gap:10px">
                <div class="pp-icon-sm" style="background:rgba(59,130,246,.15);color:#3b82f6"><i class="fa fa-laptop"></i></div>
                <div class="pp-icon-sm" style="background:rgba(16,185,129,.15);color:#10b981"><i class="fa fa-shield"></i></div>
            </div>
        </div>
    </section>

    <div class="pp-highlights">
        <div class="pp-highlight"><i class="fa fa-eye"></i><strong>Thu thập minh bạch</strong><p>Thu thập thông tin cần thiết một cách minh bạch, rõ ràng.</p></div>
        <div class="pp-highlight"><i class="fa fa-lock"></i><strong>Bảo mật thông tin</strong><p>Bảo vệ thông tin bằng công nghệ hiện đại và quy trình chặt chẽ.</p></div>
        <div class="pp-highlight"><i class="fa fa-bullseye"></i><strong>Sử dụng đúng mục đích</strong><p>Chỉ sử dụng thông tin cho các mục đích đã được thông báo.</p></div>
        <div class="pp-highlight"><i class="fa fa-headphones"></i><strong>Hỗ trợ 24/7</strong><p>Đội ngũ chuyên viên luôn sẵn sàng hỗ trợ mọi thắc mắc.</p></div>
    </div>

    {{-- 1. Thông tin thu thập --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">1</span> Thông tin chúng tôi thu thập</h2>
        <div class="pp-icon-list">
            @foreach([['i'=>'fa-user','t'=>'Họ tên'],['i'=>'fa-map-marker','t'=>'Địa chỉ giao hàng'],['i'=>'fa-phone','t'=>'Số điện thoại'],['i'=>'fa-file-text-o','t'=>'Thông tin đơn hàng'],['i'=>'fa-envelope','t'=>'Email'],['i'=>'fa-history','t'=>'Lịch sử mua hàng']] as $it)
            <div class="pp-icon-item"><i class="fa {{ $it['i'] }}"></i><span>{{ $it['t'] }}</span></div>
            @endforeach
        </div>
    </div>

    {{-- 2. Mục đích sử dụng --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">2</span> Mục đích sử dụng thông tin</h2>
        <div class="pp-bullet-card">
            <ul>
                <li><i class="fa fa-check"></i> Xử lý đơn hàng và xác nhận giao dịch.</li>
                <li><i class="fa fa-check"></i> Giao hàng và theo dõi đơn hàng.</li>
                <li><i class="fa fa-check"></i> Hỗ trợ khách hàng và giải quyết khiếu nại.</li>
                <li><i class="fa fa-check"></i> Gửi thông tin khuyến mãi nếu được khách hàng đồng ý.</li>
                <li><i class="fa fa-check"></i> Cải thiện trải nghiệm mua sắm và dịch vụ.</li>
            </ul>
        </div>
    </div>

    {{-- 3. Phạm vi sử dụng --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">3</span> Phạm vi sử dụng và chia sẻ thông tin</h2>
        <div class="pp-bullet-card">
            <ul>
                <li><i class="fa fa-check"></i> Sử dụng thông tin trong nội bộ DangDuc để phục vụ khách hàng.</li>
                <li><i class="fa fa-check"></i> Chia sẻ với đối tác vận chuyển, thanh toán khi cần thiết để hoàn tất đơn hàng.</li>
                <li><i class="fa fa-check"></i> Tuân thủ quy định pháp luật khi cơ quan chức năng yêu cầu.</li>
                <li><i class="fa fa-check"></i> Không bán, cho thuê hoặc trao đổi thông tin cá nhân cho bên thứ ba.</li>
            </ul>
        </div>
    </div>

    {{-- 4. Thời gian lưu trữ --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">4</span> Thời gian lưu trữ thông tin</h2>
        <table class="pp-table">
            <thead><tr><th>Loại thông tin</th><th>Thời gian lưu trữ</th></tr></thead>
            <tbody>
                <tr><td>Thông tin tài khoản</td><td>Trong suốt thời gian hoạt động</td></tr>
                <tr><td>Thông tin đơn hàng</td><td>Tối thiểu 5 năm</td></tr>
                <tr><td>Lịch sử giao dịch</td><td>Tối thiểu 5 năm</td></tr>
                <tr><td>Thông tin hỗ trợ</td><td>Tối thiểu 2 năm</td></tr>
            </tbody>
        </table>
    </div>

    {{-- 5. Biện pháp bảo mật --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">5</span> Biện pháp bảo mật</h2>
        <div class="pp-measures">
            <div class="pp-measure"><i class="fa fa-lock"></i><strong>Mã hóa dữ liệu</strong><span>Mã hóa SSL/TLS bảo vệ dữ liệu truyền tải.</span></div>
            <div class="pp-measure"><i class="fa fa-key"></i><strong>Kiểm soát truy cập</strong><span>Hạn chế quyền truy cập, chỉ người có thẩm quyền.</span></div>
            <div class="pp-measure"><i class="fa fa-credit-card"></i><strong>Thanh toán an toàn</strong><span>Tích hợp cổng thanh toán uy tín, chuẩn bảo mật PCI-DSS.</span></div>
            <div class="pp-measure"><i class="fa fa-mobile"></i><strong>Xác thực tài khoản</strong><span>Yêu cầu xác thực khi đăng nhập trên thiết bị mới.</span></div>
        </div>
    </div>

    {{-- 6. Quyền khách hàng --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">6</span> Quyền của khách hàng</h2>
        <div class="pp-bullet-card">
            <ul>
                <li><i class="fa fa-check"></i> Xem thông tin cá nhân mà chúng tôi lưu trữ.</li>
                <li><i class="fa fa-check"></i> Cập nhật, chỉnh sửa thông tin khi có thay đổi.</li>
                <li><i class="fa fa-check"></i> Yêu cầu xóa thông tin cá nhân (trong trường hợp phù hợp pháp luật).</li>
                <li><i class="fa fa-check"></i> Từ chối nhận thông tin quảng bá, khuyến mãi từ DangDuc.</li>
                <li><i class="fa fa-check"></i> Khiếu nại, phản ánh về việc xử lý thông tin cá nhân.</li>
            </ul>
        </div>
    </div>

    {{-- 7. Cookie --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">7</span> Cookie và công nghệ theo dõi</h2>
        <div class="pp-bullet-card">
            <ul>
                <li><i class="fa fa-check"></i> DangDuc sử dụng cookie và các công nghệ tương tự để:</li>
                <li><i class="fa fa-check"></i> Ghi nhớ tùy chọn và cài đặt của bạn.</li>
                <li><i class="fa fa-check"></i> Phân tích lưu lượng truy cập và hành vi người dùng.</li>
                <li><i class="fa fa-check"></i> Cá nhân hóa nội dung và quảng cáo (nếu có).</li>
                <li><i class="fa fa-check"></i> Bạn có thể quản lý hoặc tắt cookie trong trình duyệt bất kỳ lúc nào.</li>
            </ul>
        </div>
    </div>

    {{-- 8. Liên hệ --}}
    <div class="pp-section">
        <h2 class="pp-section-title"><span class="pp-num">8</span> Liên hệ & khiếu nại</h2>
        <div class="pp-bullet-card">
            <ul>
                <li><i class="fa fa-phone"></i> <strong>Hotline:</strong>&nbsp; 1900 1234</li>
                <li><i class="fa fa-envelope"></i> <strong>Email:</strong>&nbsp; support@dangduc.vn</li>
                <li><i class="fa fa-map-marker"></i> <strong>Địa chỉ:</strong>&nbsp; 128 Nguyễn Trãi, Thanh Xuân, Hà Nội</li>
            </ul>
            <p style="font-size:12px;color:#64748b;margin:8px 0 0">Chúng tôi sẽ phản hồi yêu cầu và khiếu nại trong thời gian sớm nhất.</p>
        </div>
    </div>

    {{-- FAQ --}}
    <div class="pp-section">
        <h2 class="pp-section-title" style="margin-top:8px">Câu hỏi thường gặp</h2>
        <div class="pp-faq-list">
            @foreach(['DangDuc có chia sẻ thông tin cá nhân của tôi cho bên thứ ba không?','Tôi có thể yêu cầu xóa thông tin cá nhân của mình không?','Thông tin thanh toán của tôi được bảo vệ như thế nào?','Cookie có ảnh hưởng gì đến trải nghiệm mua sắm?','Làm thế nào để tôi từ chối nhận email khuyến mãi?'] as $q)
            <div class="pp-faq-item"><a href="#"><i class="fa fa-chevron-right"></i> {{ $q }}</a></div>
            @endforeach
        </div>
    </div>

    {{-- CTA --}}
    <div class="pp-cta">
        <h3>Cần hỗ trợ về bảo mật?</h3>
        <p>Đội ngũ DangDuc luôn sẵn sàng hỗ trợ bạn nhanh chóng và tận tâm.</p>
        <div class="pp-cta-buttons">
            <a href="#" class="pp-cta-btn primary"><i class="fa fa-phone"></i> Liên hệ hỗ trợ<br><small>Hotline 1900 1234</small></a>
            <a href="#" class="pp-cta-btn"><i class="fa fa-clock-o"></i> Gọi 1900 1234<br><small>8:00 - 22:00 (T2 - CN)</small></a>
            <a href="#" class="pp-cta-btn"><i class="fa fa-envelope"></i> Gửi email<br><small>support@dangduc.vn</small></a>
        </div>
    </div>

    {{-- Policy strip --}}
    <div class="pp-policies">
        <div class="pp-pol"><i class="fa fa-truck"></i><div><strong>Miễn phí giao hàng</strong><span>Nội thành đơn từ 300K</span></div></div>
        <div class="pp-pol"><i class="fa fa-refresh"></i><div><strong>Đổi trả dễ dàng</strong><span>Đổi trả trong 7 ngày nếu sản phẩm lỗi</span></div></div>
        <div class="pp-pol"><i class="fa fa-shield"></i><div><strong>Bảo hành chính hãng</strong><span>Cam kết bảo hành chính hãng</span></div></div>
        <div class="pp-pol"><i class="fa fa-lock"></i><div><strong>Thanh toán an toàn</strong><span>Nhiều phương thức thanh toán</span></div></div>
        <div class="pp-pol"><i class="fa fa-headphones"></i><div><strong>Hỗ trợ tận tâm 24/7</strong><span>Đội ngũ tư vấn luôn sẵn sàng</span></div></div>
    </div>
@endsection
