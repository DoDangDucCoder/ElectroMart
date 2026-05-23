<footer class="tech-footer shopw-footer">
    <div class="container tech-footer-inner shopw-footer-inner">
        <div class="row tech-footer-main">
            <div class="col-xs-12 col-md-3">
                <div class="tech-footer-brand shopw-footer-brand-card">
                    <a href="{{ url('/') }}" aria-label="DangDuc">
                        <img src="{{ asset('uploads/brand/dangduc-logo-dark-width-600.svg') }}" alt="DangDuc">
                    </a>
                    <p class="tech-footer-brand-description">Chuyên cung cấp laptop, linh kiện PC và phụ kiện công nghệ chính hãng. Cam kết giá tốt, bảo hành và hỗ trợ tận tâm.</p>
                    <div class="tech-footer-social">
                        <a href="{{ url('/lien-he') }}" aria-label="Facebook DangDuc"><i class="fa fa-facebook"></i></a>
                        <a href="{{ url('/lien-he') }}" aria-label="Instagram DangDuc"><i class="fa fa-instagram"></i></a>
                        <a href="{{ url('/bai-viet') }}" aria-label="YouTube DangDuc"><i class="fa fa-youtube-play"></i></a>
                        <a href="{{ url('/lien-he') }}" aria-label="LinkedIn DangDuc"><i class="fa fa-linkedin"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-4 col-md-3">
                <h3>VỀ CHÚNG TÔI</h3>
                <nav class="tech-footer-nav">
                    <a href="{{ url('/cham-soc-khach-hang/gioi-thieu') }}">Giới thiệu</a>
                    <a href="{{ url('/bai-viet') }}">Tin tức</a>
                    <a href="{{ url('/lien-he') }}">Tuyển dụng</a>
                    <a href="{{ url('/lien-he') }}">Liên hệ</a>
                </nav>
            </div>

            <div class="col-xs-12 col-sm-4 col-md-3">
                <h3>CHÍNH SÁCH</h3>
                <nav class="tech-footer-nav">
                    <a href="{{ url('/cham-soc-khach-hang#topics') }}">Chính sách bảo hành</a>
                    <a href="{{ url('/cham-soc-khach-hang#topics') }}">Chính sách đổi trả</a>
                    <a href="{{ url('/cham-soc-khach-hang/thanh-toan') }}">Chính sách thanh toán</a>
                    <a href="{{ url('/cham-soc-khach-hang/van-chuyen') }}">Chính sách vận chuyển</a>
                    <a href="{{ url('/cham-soc-khach-hang/bao-mat') }}">Chính sách bảo mật</a>
                </nav>
            </div>

            <div class="col-xs-12 col-sm-4 col-md-3">
                <div class="shopw-footer-newsletter-card">
                    <h3>HỖ TRỢ KHÁCH HÀNG</h3>
                    <nav class="tech-footer-nav">
                        <a href="{{ url('/cham-soc-khach-hang') }}">Trung tâm hỗ trợ</a>
                        <a href="{{ url('/cham-soc-khach-hang/huong-dan-mua-hang') }}">Hướng dẫn mua hàng</a>
                        <a href="{{ url('/cham-soc-khach-hang/huong-dan-thanh-toan') }}">Hướng dẫn thanh toán</a>
                        <a href="{{ url('/kiem-tra-don-hang') }}">Kiểm tra đơn hàng</a>
                        <a href="{{ url('/cham-soc-khach-hang#topics') }}">Câu hỏi thường gặp</a>
                    </nav>
                </div>
                <div class="tech-footer-contact">
                    <span><i class="fa fa-phone"></i> Hotline: 1900 1234</span>
                    <span><i class="fa fa-envelope"></i> Email: support@dangduc.vn</span>
                    <span><i class="fa fa-map-marker"></i> Địa chỉ: 123 Nguyễn Trãi, Thanh Xuân, Hà Nội</span>
                </div>
            </div>
        </div>
    </div>

    <div class="tech-footer-bottom">
        <div class="container tech-footer-inner shopw-footer-inner">
            <div class="tech-footer-bottom-flex">
                <p>&copy; {{ date('Y') }} DangDuc. All rights reserved.</p>
                <div class="tech-payment-list">
                    @foreach ($paymentItems ?? [] as $payment)
                        <span class="sr-only">{{ $payment }}</span>
                    @endforeach
                    <img src="{{ asset('uploads/brand/dangduc-logo-icon-256.svg') }}" alt="Verified" class="tech-verified-badge">
                    <i class="fa fa-credit-card"></i>
                    <i class="fa fa-credit-card"></i>
                    <i class="fa fa-credit-card"></i>
                    <i class="fa fa-money"></i>
                </div>
            </div>
        </div>
    </div>
</footer>
