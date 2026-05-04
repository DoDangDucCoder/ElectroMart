<section class="tech-section tech-flash-section tech-sale-section" id="flash-sale">
    <div class="container">
        <div class="tech-flash-shell">
            <div class="tech-flash-head">
                <div>
                    <span class="tech-eyebrow tech-eyebrow-light">Ưu đãi giới hạn</span>
                    <h2>Khuyến mãi chớp nhoáng</h2>
                    <p>Danh sách sản phẩm giảm giá chạy ngang, tối ưu cho laptop và điện thoại đang có mức giá tốt.</p>
                </div>

                <div class="tech-countdown" data-tech-countdown aria-label="Đồng hồ đếm ngược khuyến mãi">
                    <span><strong data-countdown-unit="hours">06</strong><small>Giờ</small></span>
                    <span><strong data-countdown-unit="minutes">24</strong><small>Phút</small></span>
                    <span><strong data-countdown-unit="seconds">12</strong><small>Giây</small></span>
                </div>
            </div>

            <div class="tech-sale-viewport" data-tech-sale-viewport>
                <button type="button"
                    class="tech-sale-nav tech-sale-prev"
                    data-tech-sale-prev
                    aria-label="Quay lại sản phẩm khuyến mãi">
                    <i class="fa fa-angle-left"></i>
                </button>

                <div class="tech-sale-track" data-tech-sale-track aria-label="Danh sách sản phẩm khuyến mãi">
                    @foreach ($products as $product)
                        <div class="tech-sale-item">
                            @include('partials.home.product-card', [
                                'product' => $product,
                                'compact' => true,
                            ])
                        </div>
                    @endforeach
                </div>

                <button type="button"
                    class="tech-sale-nav tech-sale-next"
                    data-tech-sale-next
                    aria-label="Xem thêm sản phẩm khuyến mãi">
                    <i class="fa fa-angle-right"></i>
                </button>
            </div>

            <div class="tech-sale-dots" data-tech-sale-dots aria-hidden="true">
                <span class="tech-sale-dot is-active"></span>
                <span class="tech-sale-dot"></span>
                <span class="tech-sale-dot"></span>
                <span class="tech-sale-dot"></span>
            </div>
        </div>
    </div>
</section>
