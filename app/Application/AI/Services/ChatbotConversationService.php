<?php

namespace App\Application\AI\Services;

use App\Events\ChatMessageCreated;
use App\Infrastructure\Persistence\Eloquent\Support\ChatConversation;
use App\Infrastructure\Persistence\Eloquent\Support\ChatMessage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class ChatbotConversationService
{
    public function bootstrap(
        ?int $customerId,
        ?int $userId,
        ?string $sessionToken,
        string $channel = 'storefront'
    ): ChatConversation {
        $conversation = $this->resolve($customerId, $userId, $sessionToken, $channel);

        if (! $conversation->messages()->exists()) {
            $this->storeBotMessage(
                $conversation,
                (string) config('chatbot.welcome_message'),
                'welcome',
                'welcome',
                [
                    'actions' => $this->normalizeActions(config('chatbot.welcome_actions', [])),
                ]
            );
        }

        return $conversation->fresh();
    }

    public function resolve(
        ?int $customerId,
        ?int $userId,
        ?string $sessionToken,
        string $channel = 'storefront'
    ): ChatConversation {
        $customerId = $customerId && $customerId > 0 ? $customerId : null;
        $userId = $userId && $userId > 0 ? $userId : null;
        $sessionToken = $sessionToken ?: null;

        $conversation = null;

        if ($customerId) {
            $conversation = ChatConversation::query()
                ->where('customer_id', $customerId)
                ->where('channel', $channel)
                ->latest('id')
                ->first();
        }

        if (! $conversation && $userId) {
            $conversation = ChatConversation::query()
                ->where('user_id', $userId)
                ->where('channel', $channel)
                ->latest('id')
                ->first();
        }

        if (! $conversation && $sessionToken) {
            $conversation = ChatConversation::query()
                ->where('session_token', $sessionToken)
                ->where('channel', $channel)
                ->latest('id')
                ->first();
        }

        if ($conversation) {
            $dirty = false;

            if ($customerId && ! $conversation->customer_id) {
                $conversation->customer_id = $customerId;
                $dirty = true;
            }

            if ($userId && ! $conversation->user_id) {
                $conversation->user_id = $userId;
                $dirty = true;
            }

            if ($sessionToken && ! $conversation->session_token) {
                $conversation->session_token = $sessionToken;
                $dirty = true;
            }

            if ($dirty) {
                $conversation->save();
            }

            return $conversation;
        }

        return ChatConversation::create([
            'customer_id' => $customerId,
            'user_id' => $userId,
            'session_token' => $sessionToken,
            'channel' => $channel,
            'status' => 'unread',
            'last_message_at' => now(),
            'meta' => [
                'source' => 'chatbot',
            ],
        ]);
    }

    public function storeUserMessage(ChatConversation $conversation, string $message, ?string $intent = null, array $meta = []): ChatMessage
    {
        return $this->storeMessage($conversation, 'user', $message, $intent, 'user_message', $meta);
    }

    public function storeBotMessage(
        ChatConversation $conversation,
        string $message,
        string $responseType = 'text',
        ?string $intent = null,
        array $meta = []
    ): ChatMessage {
        return $this->storeMessage($conversation, 'bot', $message, $intent, $responseType, $meta);
    }

    public function storeSystemMessage(
        ChatConversation $conversation,
        string $message,
        string $responseType = 'system',
        ?string $intent = null,
        array $meta = [],
        ?int $userId = null
    ): ChatMessage {
        return $this->storeMessage($conversation, 'system', $message, $intent, $responseType, $meta, $userId);
    }

    public function recentMessages(ChatConversation $conversation, int $limit = 20): Collection
    {
        return $conversation->messages()
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    public function serializeConversation(ChatConversation $conversation, int $limit = 20): array
    {
        $messages = $this->recentMessages($conversation, $limit);

        return [
            'id' => $conversation->id,
            'channel' => $conversation->channel,
            'session_token' => $conversation->session_token,
            'last_message_at' => optional($conversation->last_message_at)->toIso8601String(),
            'messages' => $messages->map(fn (ChatMessage $message) => $this->serializeMessage($message))->all(),
        ];
    }

    public function serializeMessages(Collection $messages): array
    {
        return $messages
            ->map(fn (ChatMessage $message) => $this->serializeMessage($message))
            ->values()
            ->all();
    }

    public function messagesAfter(ChatConversation $conversation, int $afterId, int $limit = 50): Collection
    {
        return $conversation->messages()
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->limit($limit)
            ->get();
    }

    public function serializeMessage(ChatMessage $message): array
    {
        $meta = $message->meta ?? [];

        return [
            'id' => $message->id,
            'sender' => $message->sender,
            'message' => $message->message,
            'intent' => $message->intent,
            'response_type' => $message->response_type,
            'products' => (array) ($meta['products'] ?? []),
            'actions' => (array) ($meta['actions'] ?? []),
            'source' => $meta['source'] ?? null,
            'created_at' => optional($message->created_at)->toIso8601String(),
        ];
    }

    protected function storeMessage(
        ChatConversation $conversation,
        string $sender,
        string $message,
        ?string $intent,
        ?string $responseType,
        array $meta,
        ?int $userId = null
    ): ChatMessage {
        $chatMessage = $conversation->messages()->create([
            'customer_id' => $conversation->customer_id,
            'user_id' => $userId ?? $conversation->user_id,
            'sender' => $sender,
            'message' => trim($message),
            'intent' => $intent,
            'response_type' => $responseType,
            'meta' => $meta ?: null,
        ]);

        $conversation->forceFill([
            'last_message_at' => Carbon::now(),
        ])->save();

        if ($sender === 'user') {
            $conversation->forceFill([
                'status' => 'unread',
                'resolved_at' => null,
            ])->save();
        } elseif ($sender === 'system' && (($meta['source'] ?? null) === 'admin')) {
            $conversation->forceFill([
                'status' => 'in_progress',
                'resolved_at' => null,
                'assigned_at' => $conversation->assigned_at ?? Carbon::now(),
                'admin_last_read_at' => Carbon::now(),
                'user_id' => $userId ?? $conversation->user_id,
            ])->save();
        }

        if (config('broadcasting.default')) {
            event(new ChatMessageCreated($chatMessage, [
                'conversation_id' => $conversation->id,
                'channel' => $conversation->channel,
                'message' => $this->serializeMessage($chatMessage),
            ]));
        }

        return $chatMessage;
    }

    protected function normalizeActions(array $actions): array
    {
        return collect($actions)
            ->map(function (array $action) {
                $path = (string) ($action['path'] ?? '#');

                return [
                    'label' => (string) ($action['label'] ?? 'Xem thêm'),
                    'url' => str_starts_with($path, 'http') ? $path : url($path),
                ];
            })
            ->values()
            ->all();
    }
}
