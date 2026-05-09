<div class="shopw-chatbot" data-chatbot-widget>
    <section id="shopw-chatbot-panel" class="shopw-chatbot-panel" data-chatbot-panel hidden>
        <header class="shopw-chatbot-header">
            <div>
                <h3>{{ config('storefront.labels.chat_assistant', 'Trợ lý bán hàng') }}</h3>
                <span class="shopw-chatbot-subtitle">Tư vấn mua hàng trực tuyến</span>
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
            <input type="text" name="message" data-chatbot-input placeholder="Nhập tin nhắn..." maxlength="500"
                autocomplete="off">
            <button type="submit">
                <i class="fa fa-paper-plane"></i>
                <span>Gửi</span>
            </button>
        </form>
    </section>
</div>

