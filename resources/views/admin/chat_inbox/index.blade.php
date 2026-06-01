@extends('admin_layout')

@section('admin_content')
    <div class="g-admin-page-shell g-admin-chat-inbox">
        <section class="g-admin-page-hero">
            <div>
                <span class="g-admin-page-eyebrow">Hỗ trợ khách hàng</span>
                <h1>Hộp thư chatbot</h1>
                <p>Theo dõi hội thoại giữa khách hàng và chatbot, xem lịch sử chat và gửi phản hồi thủ công từ admin khi cần.</p>
            </div>
            <div class="g-admin-page-stats">
                <article>
                    <strong>{{ number_format($summary['total_conversations']) }}</strong>
                    <span>Tổng hội thoại</span>
                </article>
                <article>
                    <strong>{{ number_format($summary['customer_conversations']) }}</strong>
                    <span>Khách đã đăng nhập</span>
                </article>
                <article>
                    <strong>{{ number_format($summary['guest_conversations']) }}</strong>
                    <span>Khách vãng lai</span>
                </article>
                <article>
                    <strong>{{ number_format($summary['unread_conversations']) }}</strong>
                    <span>Chưa đọc</span>
                </article>
                <article>
                    <strong>{{ number_format($summary['resolved_conversations']) }}</strong>
                    <span>Đã xử lý</span>
                </article>
                <article>
                    <strong>{{ number_format($summary['my_unread_conversations'] ?? 0) }}</strong>
                    <span>Chưa đọc của tôi</span>
                </article>
            </div>
        </section>

        @unless ($chat_tables_ready ?? false)
            <section class="g-admin-panel-card">
                <div class="g-admin-panel-body">
                    <div class="alert alert-warning" style="margin-bottom: 0;">
                        Bảng dữ liệu chat chưa sẵn sàng. Trang đang hiển thị ở chế độ an toàn cho đến khi
                        <code>chat_conversations</code> và <code>chat_messages</code> được tạo trong cơ sở dữ liệu.
                    </div>
                </div>
            </section>
        @endunless

        <section class="g-admin-panel-card">
            <div class="g-admin-panel-head">
                <div>
                    <div>Bộ lọc hội thoại</div>
                    <p>Tìm theo tên khách, email, số điện thoại hoặc nội dung tin nhắn gần nhất.</p>
                </div>
            </div>
            <div class="g-admin-panel-body">
                <form method="GET" action="{{ url('/chat-inbox') }}" class="g-admin-report-toolbar g-admin-chat-toolbar">
                    <div class="form-group">
                        <label for="keyword">Từ khóa</label>
                        <input type="text" id="keyword" name="keyword" class="form-control"
                            value="{{ $filters['keyword'] ?? '' }}" placeholder="Tên khách, email hoặc nội dung chat">
                    </div>
                    <div class="form-group">
                        <label for="source">Nguồn khách</label>
                        <select name="source" id="source" class="form-control">
                            <option value="all" @selected(($filters['source'] ?? 'all') === 'all')>Tất cả</option>
                            <option value="customer" @selected(($filters['source'] ?? 'all') === 'customer')>Khách đăng nhập</option>
                            <option value="guest" @selected(($filters['source'] ?? 'all') === 'guest')>Khách vãng lai</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Trạng thái</label>
                        <select name="status" id="status" class="form-control">
                            <option value="all" @selected(($filters['status'] ?? 'all') === 'all')>Tất cả</option>
                            <option value="unread" @selected(($filters['status'] ?? 'all') === 'unread')>Chưa đọc</option>
                            <option value="in_progress" @selected(($filters['status'] ?? 'all') === 'in_progress')>Đang xử lý</option>
                            <option value="resolved" @selected(($filters['status'] ?? 'all') === 'resolved')>Đã xử lý</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="scope">Phạm vi</label>
                        <select name="scope" id="scope" class="form-control">
                            <option value="all" @selected(($filters['scope'] ?? 'all') === 'all')>Tất cả hội thoại</option>
                            <option value="mine" @selected(($filters['scope'] ?? 'all') === 'mine')>Chỉ hội thoại của tôi</option>
                        </select>
                    </div>
                    <div class="form-group g-admin-chat-toolbar-actions">
                        <label class="sr-only">Thao tác lọc</label>
                        <div class="g-admin-inline-actions">
                            <button type="submit" class="btn btn-info">Lọc hội thoại</button>
                            <a href="{{ url('/chat-inbox') }}" class="btn btn-default">Xóa bộ lọc</a>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        <section class="g-admin-chat-layout" data-admin-chat-inbox
            data-selected-conversation="{{ $selected_conversation?->id ?? '' }}">
            <aside class="g-admin-panel-card g-admin-chat-sidebar">
                <div class="g-admin-panel-head">
                    <div>
                        <div>Danh sách hội thoại</div>
                        <p>
                            {{ number_format($conversations->total()) }} hội thoại trong bộ lọc hiện tại.
                            @if (($filters['scope'] ?? 'all') === 'mine')
                                Chỉ hiển thị các hội thoại đã gán cho bạn.
                            @endif
                        </p>
                    </div>
                </div>
                <div class="g-admin-panel-body g-admin-chat-list" data-admin-chat-list>
                    @forelse ($conversations as $conversation)
                        @php
                            $customer = $conversation->customer;
                            $latestMessage = $conversation->latestMessage;
                        @endphp
                        <button type="button"
                            class="g-admin-chat-list-item {{ $selected_conversation && $selected_conversation->id === $conversation->id ? 'is-active' : '' }}"
                            data-conversation-id="{{ $conversation->id }}">
                            <strong>
                                {{ $customer?->name ?: 'Khách vãng lai' }}
                                <span class="g-admin-chat-status status-{{ $conversation->status }}">{{ $conversation->status === 'unread' ? 'Chưa đọc' : ($conversation->status === 'resolved' ? 'Đã xử lý' : 'Đang xử lý') }}</span>
                            </strong>
                            <span>{{ $customer?->email ?: ($conversation->session_token ? 'Phiên: ' . \Illuminate\Support\Str::limit($conversation->session_token, 18) : 'Chưa có email') }}</span>
                            <span>{{ $conversation->assignee?->name ? 'Phụ trách: ' . $conversation->assignee->name : 'Chưa gán phụ trách' }}</span>
                            <small>
                                {{ $latestMessage?->message ? \Illuminate\Support\Str::limit($latestMessage->message, 70) : 'Chưa có tin nhắn' }}
                            </small>
                            <em>
                                {{ $conversation->last_message_at ? $conversation->last_message_at->format('d/m/Y H:i') : 'N/A' }}
                            </em>
                        </button>
                    @empty
                        <div class="g-admin-empty-state">Chưa có hội thoại nào phù hợp với bộ lọc hiện tại.</div>
                    @endforelse
                </div>
                <div class="g-admin-panel-body">
                    @include('admin.partials.pagination', ['paginator' => $conversations, 'label' => 'hội thoại'])
                </div>
            </aside>

            <section class="g-admin-panel-card g-admin-chat-thread-card">
                <div class="g-admin-panel-head">
                    <div>
                        <div data-admin-chat-title>
                            {{ $selected_conversation?->customer?->name ?: 'Chưa chọn hội thoại' }}
                        </div>
                        <p data-admin-chat-meta>
                            @if ($selected_conversation?->customer)
                                {{ $selected_conversation->customer->email }}{{ $selected_conversation->customer->phone ? ' • ' . $selected_conversation->customer->phone : '' }}
                            @elseif ($selected_conversation)
                                Khách vãng lai • {{ $selected_conversation->session_token }}
                            @else
                                Chọn một hội thoại ở cột bên trái để xem chi tiết.
                            @endif
                        </p>
                        <div class="g-admin-chat-thread-actions" data-admin-chat-thread-actions @if (!$selected_conversation) hidden @endif>
                            <span class="g-admin-chat-status status-{{ $selected_conversation->status ?? 'unread' }}" data-admin-chat-status>
                                {{ ($selected_conversation->status ?? 'unread') === 'unread' ? 'Chưa đọc' : (($selected_conversation->status ?? 'unread') === 'resolved' ? 'Đã xử lý' : 'Đang xử lý') }}
                            </span>
                            <span class="g-admin-cell-note" data-admin-chat-assignee>
                                {{ $selected_conversation?->assignee?->name ? 'Phụ trách: ' . $selected_conversation->assignee->name : 'Chưa gán phụ trách' }}
                            </span>
                            <div class="g-admin-inline-actions">
                                <button type="button" class="btn btn-default" data-admin-chat-assign>Nhận xử lý</button>
                                <button type="button" class="btn btn-success" data-admin-chat-resolve>Đánh dấu đã xử lý</button>
                                <button type="button" class="btn btn-warning" data-admin-chat-reopen>Mở lại</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="g-admin-panel-body g-admin-chat-thread" data-admin-chat-thread>
                    @if ($selected_conversation)
                        @foreach ($selected_messages as $message)
                            <article class="g-admin-chat-message is-{{ $message['sender'] }}"
                                data-message-id="{{ $message['id'] }}">
                                <div class="g-admin-chat-message-bubble">
                                    <strong>
                                        @if ($message['sender'] === 'user')
                                            Khách hàng
                                        @elseif ($message['sender'] === 'system' && ($message['source'] ?? null) === 'admin')
                                            Admin
                                        @else
                                            Chatbot
                                        @endif
                                    </strong>
                                    <p>{{ $message['message'] }}</p>
                                    @if (! empty($message['products']))
                                        <div class="g-admin-chat-products">
                                            @foreach ($message['products'] as $product)
                                                <a href="{{ $product['url'] }}" target="_blank" rel="noopener">
                                                    {{ $product['name'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                    <span>{{ \Carbon\Carbon::parse($message['created_at'])->format('d/m/Y H:i') }}</span>
                                </div>
                            </article>
                        @endforeach
                    @else
                        <div class="g-admin-empty-state">Chưa có hội thoại nào để hiển thị.</div>
                    @endif
                </div>

                <div class="g-admin-panel-body">
                    <form class="g-admin-chat-reply-form" data-admin-chat-reply-form
                        @if (!$selected_conversation) hidden @endif>
                        <textarea class="form-control" rows="3" data-admin-chat-reply-input
                            placeholder="Nhập phản hồi từ admin..."></textarea>
                        <div class="g-admin-inline-actions">
                            <button type="submit" class="btn btn-info">Gửi phản hồi</button>
                        </div>
                    </form>
                </div>
            </section>
        </section>
    </div>

    <script>
        (function() {
            const inbox = document.querySelector('[data-admin-chat-inbox]');

            if (!inbox) {
                return;
            }

            const thread = inbox.querySelector('[data-admin-chat-thread]');
            const listContainer = inbox.querySelector('[data-admin-chat-list]');
            const title = inbox.querySelector('[data-admin-chat-title]');
            const meta = inbox.querySelector('[data-admin-chat-meta]');
            const replyForm = inbox.querySelector('[data-admin-chat-reply-form]');
            const replyInput = inbox.querySelector('[data-admin-chat-reply-input]');
            const threadActions = inbox.querySelector('[data-admin-chat-thread-actions]');
            const statusBadge = inbox.querySelector('[data-admin-chat-status]');
            const assigneeText = inbox.querySelector('[data-admin-chat-assignee]');
            const assignButton = inbox.querySelector('[data-admin-chat-assign]');
            const resolveButton = inbox.querySelector('[data-admin-chat-resolve]');
            const reopenButton = inbox.querySelector('[data-admin-chat-reopen]');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            let selectedConversationId = Number(inbox.getAttribute('data-selected-conversation') || 0);
            let pollTimer = null;
            let isLoading = false;

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text || '';
                return div.innerHTML;
            }

            function lastMessageId() {
                const lastMessage = thread.querySelector('[data-message-id]:last-child');

                return lastMessage ? Number(lastMessage.getAttribute('data-message-id')) : 0;
            }

            function renderMessage(message) {
                const senderLabel = message.sender === 'user'
                    ? 'Khách hàng'
                    : (message.sender === 'system' && message.source === 'admin' ? 'Admin' : 'Chatbot');

                return `
                    <article class="g-admin-chat-message is-${escapeHtml(message.sender)}" data-message-id="${message.id}">
                        <div class="g-admin-chat-message-bubble">
                            <strong>${senderLabel}</strong>
                            <p>${escapeHtml(message.message)}</p>
                            ${
                                Array.isArray(message.products) && message.products.length
                                    ? `<div class="g-admin-chat-products">${message.products.map(product => `<a href="${escapeHtml(product.url)}" target="_blank" rel="noopener">${escapeHtml(product.name)}</a>`).join('')}</div>`
                                    : ''
                            }
                            <span>${escapeHtml((message.created_at || '').replace('T', ' ').slice(0, 16))}</span>
                        </div>
                    </article>
                `;
            }

            function statusLabel(status) {
                if (status === 'resolved') {
                    return 'Đã xử lý';
                }

                if (status === 'in_progress') {
                    return 'Đang xử lý';
                }

                return 'Chưa đọc';
            }

            function updateConversationMeta(conversation) {
                threadActions.hidden = false;
                statusBadge.className = `g-admin-chat-status status-${conversation.status || 'unread'}`;
                statusBadge.textContent = statusLabel(conversation.status || 'unread');
                assigneeText.textContent = conversation.assignee?.name
                    ? `Phụ trách: ${conversation.assignee.name}`
                    : 'Chưa gán phụ trách';
            }

            function updateListItem(conversation) {
                const button = listContainer.querySelector(`[data-conversation-id="${conversation.id}"]`);

                if (!button) {
                    return;
                }

                const statusNode = button.querySelector('.g-admin-chat-status');
                const detailRows = button.querySelectorAll('span');

                if (statusNode) {
                    statusNode.className = `g-admin-chat-status status-${conversation.status || 'unread'}`;
                    statusNode.textContent = statusLabel(conversation.status || 'unread');
                }

                if (detailRows[1]) {
                    detailRows[1].textContent = conversation.assignee?.name
                        ? `Phụ trách: ${conversation.assignee.name}`
                        : 'Chưa gán phụ trách';
                }
            }

            function setThread(messages) {
                if (!Array.isArray(messages) || messages.length === 0) {
                    thread.innerHTML = '<div class="g-admin-empty-state">Hội thoại này chưa có tin nhắn.</div>';
                    return;
                }

                thread.innerHTML = messages.map(renderMessage).join('');
                thread.scrollTop = thread.scrollHeight;
            }

            function appendMessages(messages) {
                if (!Array.isArray(messages) || messages.length === 0) {
                    return;
                }

                const empty = thread.querySelector('.g-admin-empty-state');

                if (empty) {
                    empty.remove();
                }

                thread.insertAdjacentHTML('beforeend', messages.map(renderMessage).join(''));
                thread.scrollTop = thread.scrollHeight;
            }

            function setSelectedButton(conversationId) {
                listContainer.querySelectorAll('[data-conversation-id]').forEach((button) => {
                    button.classList.toggle('is-active', Number(button.getAttribute('data-conversation-id')) === conversationId);
                });
            }

            async function loadConversation(conversationId, appendOnly = false) {
                if (!conversationId || isLoading) {
                    return;
                }

                isLoading = true;

                try {
                    const suffix = appendOnly && lastMessageId() > 0 ? `?after_id=${lastMessageId()}` : '';
                    const response = await fetch(`/chat-inbox/conversations/${conversationId}${suffix}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const payload = await response.json();
                    const data = payload.data || {};
                    const conversation = data.conversation || {};
                    const messages = data.messages || [];

                    selectedConversationId = conversation.id || conversationId;
                    inbox.setAttribute('data-selected-conversation', selectedConversationId);
                    setSelectedButton(selectedConversationId);
                    replyForm.hidden = false;

                    title.textContent = conversation.customer?.name || 'Khách vãng lai';
                    meta.textContent = conversation.customer
                        ? [conversation.customer.email, conversation.customer.phone].filter(Boolean).join(' • ')
                        : `Khách vãng lai • ${conversation.session_token || ''}`;
                    updateConversationMeta(conversation);
                    updateListItem(conversation);

                    if (appendOnly) {
                        appendMessages(messages);
                    } else {
                        setThread(messages);
                    }

                    startPolling();
                } finally {
                    isLoading = false;
                }
            }

            async function sendReply(message) {
                if (!selectedConversationId || !message.trim()) {
                    return;
                }

                const response = await fetch(`/chat-inbox/conversations/${selectedConversationId}/reply`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ message })
                });

                const payload = await response.json();
                appendMessages([payload.data.message]);
                await loadConversation(selectedConversationId, false);
            }

            async function patchConversation(path, body) {
                if (!selectedConversationId) {
                    return;
                }

                const response = await fetch(`/chat-inbox/conversations/${selectedConversationId}/${path}`, {
                    method: body ? 'PATCH' : 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(body || {})
                });

                const payload = await response.json();
                updateConversationMeta(payload.data.conversation);
                setSelectedButton(selectedConversationId);
            }

            function startPolling() {
                stopPolling();

                pollTimer = window.setInterval(function() {
                    if (selectedConversationId) {
                        loadConversation(selectedConversationId, true);
                    }
                }, 3000);
            }

            function stopPolling() {
                if (pollTimer) {
                    window.clearInterval(pollTimer);
                    pollTimer = null;
                }
            }

            listContainer.addEventListener('click', function(event) {
                const button = event.target.closest('[data-conversation-id]');

                if (!button) {
                    return;
                }

                loadConversation(Number(button.getAttribute('data-conversation-id')), false);
            });

            if (replyForm) {
                replyForm.addEventListener('submit', async function(event) {
                    event.preventDefault();

                    const value = replyInput.value;

                    if (!value.trim()) {
                        return;
                    }

                    replyInput.value = '';
                    await sendReply(value);
                });
            }

            if (assignButton) {
                assignButton.addEventListener('click', async function() {
                    await patchConversation('assign');
                    await loadConversation(selectedConversationId, false);
                });
            }

            if (resolveButton) {
                resolveButton.addEventListener('click', async function() {
                    await patchConversation('status', {
                        status: 'resolved'
                    });
                    await loadConversation(selectedConversationId, false);
                });
            }

            if (reopenButton) {
                reopenButton.addEventListener('click', async function() {
                    await patchConversation('status', {
                        status: 'in_progress'
                    });
                    await loadConversation(selectedConversationId, false);
                });
            }

            if (selectedConversationId) {
                startPolling();
            }

            window.addEventListener('beforeunload', stopPolling);
        })();
    </script>
@endsection
