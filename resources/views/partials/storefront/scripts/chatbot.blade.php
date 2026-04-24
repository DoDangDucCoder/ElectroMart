<script>
    (function () {
        const widget = document.querySelector('[data-chatbot-widget]');

        if (!widget) {
            return;
        }

        const panel = widget.querySelector('[data-chatbot-panel]');
        const toggle = widget.querySelector('[data-chatbot-toggle]');
        const closeButton = widget.querySelector('[data-chatbot-close]');
        const form = widget.querySelector('[data-chatbot-form]');
        const input = widget.querySelector('[data-chatbot-input]');
        const messagesContainer = widget.querySelector('[data-chatbot-messages]');
        const quickActions = widget.querySelectorAll('[data-chatbot-prompt]');
        const launchers = document.querySelectorAll('[data-chatbot-launch]');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        let loaded = false;
        let isSending = false;
        let conversationId = 0;
        let pollTimer = null;

        function setExpanded(isExpanded) {
            if (toggle) {
                toggle.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text || '';
            return div.innerHTML;
        }

        function renderProducts(products) {
            if (!Array.isArray(products) || products.length === 0) {
                return '';
            }

            return `
                <div class="shopw-chatbot-product-list">
                    ${products.map(product => `
                        <a class="shopw-chatbot-product" href="${escapeHtml(product.url)}">
                            <img src="${escapeHtml(product.image)}" alt="${escapeHtml(product.name)}">
                            <span class="shopw-chatbot-product-copy">
                                <strong>${escapeHtml(product.name)}</strong>
                                <span class="shopw-chatbot-product-price-row">
                                    ${product.original_price_formatted ? `<small>${escapeHtml(product.original_price_formatted)}</small>` : ''}
                                    <span>${escapeHtml(product.price_formatted || '')}</span>
                                </span>
                                ${product.sale_label ? `<em>${escapeHtml(product.sale_label)}</em>` : ''}
                            </span>
                        </a>
                    `).join('')}
                </div>
            `;
        }

        function renderActions(actions) {
            if (!Array.isArray(actions) || actions.length === 0) {
                return '';
            }

            return `
                <div class="shopw-chatbot-actions">
                    ${actions.map(action => `
                        <a href="${escapeHtml(action.url)}" class="shopw-chatbot-action-link">${escapeHtml(action.label)}</a>
                    `).join('')}
                </div>
            `;
        }

        function renderMessage(message) {
            const wrapper = document.createElement('article');
            wrapper.className = `shopw-chatbot-message shopw-chatbot-message-${message.sender}`;
            wrapper.setAttribute('data-message-id', String(message.id || ''));
            wrapper.innerHTML = `
                <div class="shopw-chatbot-bubble">
                    <p>${escapeHtml(message.message || '')}</p>
                    ${renderProducts(message.products)}
                    ${renderActions(message.actions)}
                </div>
            `;

            return wrapper;
        }

        function scrollToBottom() {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function setMessages(messages) {
            messagesContainer.innerHTML = '';

            messages.forEach((message) => {
                messagesContainer.appendChild(renderMessage(message));
            });

            scrollToBottom();
        }

        function appendMessages(messages) {
            if (!Array.isArray(messages) || messages.length === 0) {
                return;
            }

            messages.forEach((message) => {
                messagesContainer.appendChild(renderMessage(message));
            });

            scrollToBottom();
        }

        function getLastMessageId() {
            const lastMessage = messagesContainer.querySelector('[data-message-id]:last-child');

            return lastMessage ? Number(lastMessage.getAttribute('data-message-id')) : 0;
        }

        function showTypingState() {
            const typing = document.createElement('article');
            typing.className = 'shopw-chatbot-message shopw-chatbot-message-bot';
            typing.setAttribute('data-chatbot-typing', 'true');
            typing.innerHTML = `
                <div class="shopw-chatbot-bubble">
                    <p>Đang soạn phản hồi...</p>
                </div>
            `;

            messagesContainer.appendChild(typing);
            scrollToBottom();
        }

        function clearTypingState() {
            const typing = messagesContainer.querySelector('[data-chatbot-typing="true"]');

            if (typing) {
                typing.remove();
            }
        }

        async function bootstrapConversation() {
            if (loaded) {
                return;
            }

            const response = await fetch('/chatbot/conversation', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const payload = await response.json();
            conversationId = Number(payload.data.id || 0);
            setMessages(payload.data.messages || []);
            loaded = true;
        }

        async function pollConversation() {
            if (!loaded || !conversationId || panel.hasAttribute('hidden')) {
                return;
            }

            const lastMessageId = getLastMessageId();
            const response = await fetch(`/chatbot/conversation?after_id=${lastMessageId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const payload = await response.json();
            appendMessages(payload.data.messages || []);
        }

        function startPolling() {
            stopPolling();
            pollTimer = window.setInterval(pollConversation, 5000);
        }

        function stopPolling() {
            if (pollTimer) {
                window.clearInterval(pollTimer);
                pollTimer = null;
            }
        }

        async function sendMessage(message) {
            if (isSending || !message.trim()) {
                return;
            }

            isSending = true;
            showTypingState();

            try {
                const response = await fetch('/chatbot/messages', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        message: message.trim()
                    })
                });

                const payload = await response.json();
                clearTypingState();
                conversationId = Number(payload.data.conversation.id || conversationId);
                setMessages(payload.data.conversation.messages || []);
            } catch (error) {
                clearTypingState();
                messagesContainer.appendChild(renderMessage({
                    sender: 'bot',
                    message: 'Có lỗi khi gửi tin nhắn. Bạn vui lòng thử lại sau ít phút.',
                    products: [],
                    actions: []
                }));
                scrollToBottom();
            } finally {
                isSending = false;
            }
        }

        if (toggle) {
            toggle.addEventListener('click', async function () {
                const isOpen = !panel.hasAttribute('hidden');

                if (isOpen) {
                    panel.setAttribute('hidden', 'hidden');
                    setExpanded(false);
                    return;
                }

                panel.removeAttribute('hidden');
                setExpanded(true);
                await bootstrapConversation();
                startPolling();
                input.focus();
            });
        }

        launchers.forEach((launcher) => {
            launcher.addEventListener('click', async function (event) {
                event.preventDefault();

                if (panel.hasAttribute('hidden')) {
                    panel.removeAttribute('hidden');
                    setExpanded(true);
                    await bootstrapConversation();
                    startPolling();
                }

                input.focus();
            });
        });

        closeButton.addEventListener('click', function () {
            panel.setAttribute('hidden', 'hidden');
            setExpanded(false);
            stopPolling();
        });

        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            const value = input.value;

            if (!value.trim()) {
                return;
            }

            input.value = '';
            await sendMessage(value);
            input.focus();
        });

        quickActions.forEach((button) => {
            button.addEventListener('click', async function () {
                const prompt = button.getAttribute('data-chatbot-prompt') || '';

                if (!prompt) {
                    return;
                }

                if (panel.hasAttribute('hidden')) {
                    panel.removeAttribute('hidden');
                    setExpanded(true);
                    await bootstrapConversation();
                    startPolling();
                }

                await sendMessage(prompt);
            });
        });

        if (window.Echo && typeof window.Echo.channel === 'function') {
            window.Echo.channel('chatbot')
                .listen('.ChatMessageCreated', function (event) {
                    if (!event || Number(event.conversation_id || 0) !== conversationId) {
                        return;
                    }

                    appendMessages([event.message]);
                });
        }
    })();
</script>

