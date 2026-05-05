<div class="shopw-chatbot" data-chatbot-widget>
    <section id="shopw-chatbot-panel" class="shopw-chatbot-panel" data-chatbot-panel hidden>
        <header class="shopw-chatbot-header">
            <div>
                <p class="shopw-chatbot-kicker">{{ config('services.storefront_brand.name', 'DangDuc') }}</p>
                <h3>{{ config('storefront.labels.chat_assistant', 'Trợ lý bán hàng') }}</h3>
                <span class="shopw-chatbot-subtitle">Chat mua hàng trực tuyến từ 8h - 24h</span>
            </div>
            <button type="button" class="shopw-chatbot-close" data-chatbot-close aria-label="Đóng hộp chat">
                <span aria-hidden="true">&times;</span>
            </button>
        </header>

        <div class="shopw-chatbot-body" data-chatbot-messages>
            <article class="shopw-chatbot-message shopw-chatbot-message-bot">
                <div class="shopw-chatbot-bubble">
                    <p>Xin chào! Tôi là trợ lý bán hàng. Bạn cần tư vấn sản phẩm, giá cả hay kiểm tra đơn hàng?</p>
                </div>
            </article>
        </div>

        <div class="shopw-chatbot-quick-actions" data-chatbot-quick-actions>
            <button type="button" data-chatbot-prompt="Tư vấn giúp tôi sản phẩm phù hợp">Tư vấn sản phẩm</button>
            <button type="button" data-chatbot-prompt="Gợi ý cho tôi một sản phẩm phù hợp">Gợi ý sản phẩm</button>
            <button type="button" data-chatbot-prompt="Chính sách bảo hành thế nào?">Bảo hành</button>
        </div>

        <form class="shopw-chatbot-form" data-chatbot-form>
            <input type="text" name="message" data-chatbot-input placeholder="Nhập tin nhắn..." maxlength="1000"
                autocomplete="off">
            <button type="submit">
                <i class="fa fa-paper-plane"></i>
                <span>Gửi</span>
            </button>
        </form>
    </section>

    <button type="button" class="shopw-chatbot-toggle" data-chatbot-toggle aria-controls="shopw-chatbot-panel"
        aria-expanded="false" aria-label="Mở trợ lý bán hàng">
        <span class="shopw-chatbot-toggle-main">
            <span class="shopw-chatbot-toggle-icon" aria-hidden="true">
                <i class="fa fa-comments-o"></i>
            </span>
            <span class="shopw-chatbot-toggle-copy">
                <strong>{{ config('storefront.labels.chat_assistant', 'Trợ lý bán hàng') }}</strong>
                <span>Chat mua hàng trực tuyến từ 8h - 24h</span>
            </span>
        </span>
        <span class="shopw-chatbot-toggle-tail" aria-hidden="true">
            <i class="fa fa-angle-up"></i>
        </span>
    </button>
</div>

