<footer class="tech-footer">
    <div class="container">
        <div class="row tech-footer-main">
            <div class="col-xs-12 col-md-4">
                <div class="tech-footer-brand">
                    <span class="tech-footer-logo">
                        <img src="{{ asset('uploads/contact/laptop-symbol-azy3.png') }}" alt="ShopW Tech">
                    </span>
                    <div>
                        <h2>ShopW Tech</h2>
                        <p>Trang mua sắm công nghệ tập trung vào laptop, điện thoại và phụ kiện chính hãng.</p>
                    </div>
                </div>
            </div>

            <div class="col-xs-6 col-md-2">
                <h3>Thông tin</h3>
                <a href="{{ url('/trang-chu') }}">Về cửa hàng</a>
                <a href="{{ url('/lien-he') }}">Liên hệ</a>
                <a href="#tech-news">Tin công nghệ</a>
            </div>

            <div class="col-xs-6 col-md-2">
                <h3>Hỗ trợ</h3>
                <a href="#">Chính sách bảo hành</a>
                <a href="#">Đổi trả hàng</a>
                <a href="#">Hướng dẫn thanh toán</a>
            </div>

            <div class="col-xs-12 col-md-4">
                <h3>Sản phẩm</h3>
                <div class="tech-footer-products">
                    <a href="{{ url('/tim-kiem?keywords_submit=laptop') }}">Laptop</a>
                    <a href="{{ url('/tim-kiem?keywords_submit=điện thoại') }}">Điện thoại</a>
                    <a href="{{ url('/tim-kiem?keywords_submit=phụ kiện') }}">Phụ kiện</a>
                    <a href="{{ url('/tim-kiem?keywords_submit=gaming') }}">Gaming gear</a>
                </div>
            </div>
        </div>
    </div>

    <div class="tech-payment-strip">
        <div class="container">
            <span>Thanh toán an toàn</span>
            <div class="tech-payment-list">
                @foreach ($paymentItems as $payment)
                    <strong>{{ $payment }}</strong>
                @endforeach
            </div>
        </div>
    </div>
</footer>
