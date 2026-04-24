<div class="shopw-chatbot" data-chatbot-widget>
    <section id="shopw-chatbot-panel" class="shopw-chatbot-panel" data-chatbot-panel hidden>
        <header class="shopw-chatbot-header">
            <div>
                <p class="shopw-chatbot-kicker">{{ config('services.storefront_brand.name', 'ShopW') }}</p>
                <h3>{{ config('storefront.labels.chat_panel_title', 'Chat hỗ trợ sản phẩm') }}</h3>
            </div>
            <button type="button" class="shopw-chatbot-close" data-chatbot-close aria-label="Đóng hộp chat">
                <i class="fa fa-times"></i>
            </button>
        </header>

        <div class="shopw-chatbot-body" data-chatbot-messages>
            <div class="shopw-chatbot-loading">Đang tải hội thoại...</div>
        </div>

        <div class="shopw-chatbot-quick-actions" data-chatbot-quick-actions>
            <button type="button" data-chatbot-prompt="Tìm cho tôi điện thoại dưới 10 triệu">Điện thoại dưới 10 triệu</button>
            <button type="button" data-chatbot-prompt="Gợi ý cho tôi một sản phẩm phù hợp">Gợi ý sản phẩm</button>
            <button type="button" data-chatbot-prompt="Chính sách bảo hành thế nào?">Bảo hành</button>
        </div>

        <form class="shopw-chatbot-form" data-chatbot-form>
            <input type="text" name="message" data-chatbot-input placeholder="Nhập câu hỏi của bạn..." maxlength="1000"
                autocomplete="off">
            <button type="submit">
                <i class="fa fa-paper-plane"></i>
                <span>Gửi</span>
            </button>
        </form>
    </section>
</div>

