<?php

namespace App\Application\AI\Services;

use App\Infrastructure\Persistence\Eloquent\Support\ChatConversation;
use App\Infrastructure\Persistence\Eloquent\Support\ChatMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class ChatInboxService
{
    public function __construct(
        private readonly ChatbotConversationService $conversations
    ) {
    }

    public function paginateConversations(array $filters = [], ?User $admin = null, int $perPage = 20): LengthAwarePaginator
    {
        return $this->conversationQuery($filters, $admin)
            ->with(['customer:id,name,email,phone', 'latestMessage', 'assignee:id,name,email'])
            ->withCount('messages')
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function summary(array $filters = [], ?User $admin = null): array
    {
        $baseQuery = $this->conversationQuery($filters, $admin);
        $conversationIdsQuery = (clone $baseQuery)->select('id');

        return [
            'total_conversations' => (clone $baseQuery)->count(),
            'guest_conversations' => (clone $baseQuery)->whereNull('customer_id')->count(),
            'customer_conversations' => (clone $baseQuery)->whereNotNull('customer_id')->count(),
            'total_messages' => ChatMessage::query()->whereIn('conversation_id', $conversationIdsQuery)->count(),
            'unread_conversations' => (clone $baseQuery)->where('status', 'unread')->count(),
            'in_progress_conversations' => (clone $baseQuery)->where('status', 'in_progress')->count(),
            'resolved_conversations' => (clone $baseQuery)->where('status', 'resolved')->count(),
            'my_conversations' => $admin ? (clone $baseQuery)->where('user_id', $admin->id)->count() : 0,
            'my_unread_conversations' => $admin ? (clone $baseQuery)->where('user_id', $admin->id)->where('status', 'unread')->count() : 0,
        ];
    }

    public function sidebarStats(?User $admin = null): array
    {
        if (! $admin) {
            return [
                'unread_total' => 0,
                'assigned_total' => 0,
                'my_unread' => 0,
            ];
        }

        return [
            'unread_total' => ChatConversation::query()->where('status', 'unread')->count(),
            'assigned_total' => ChatConversation::query()->where('user_id', $admin->id)->count(),
            'my_unread' => ChatConversation::query()
                ->where('user_id', $admin->id)
                ->where('status', 'unread')
                ->count(),
        ];
    }

    public function findConversation(int $conversationId, array $filters = [], ?User $admin = null): ?ChatConversation
    {
        return $this->conversationQuery($filters, $admin)
            ->with(['customer', 'latestMessage', 'assignee'])
            ->find($conversationId);
    }

    public function serializeConversationSummary(ChatConversation $conversation): array
    {
        $latestMessage = $conversation->latestMessage;

        return [
            'id' => $conversation->id,
            'channel' => $conversation->channel,
            'customer' => $conversation->customer ? [
                'id' => $conversation->customer->id,
                'name' => $conversation->customer->name,
                'email' => $conversation->customer->email,
                'phone' => $conversation->customer->phone,
            ] : null,
            'assignee' => $conversation->assignee ? [
                'id' => $conversation->assignee->id,
                'name' => $conversation->assignee->name,
                'email' => $conversation->assignee->email,
            ] : null,
            'status' => $conversation->status,
            'session_token' => $conversation->session_token,
            'last_message_at' => optional($conversation->last_message_at)->toIso8601String(),
            'last_message_preview' => $latestMessage?->message,
            'last_message_sender' => $latestMessage?->sender,
            'messages_count' => $conversation->messages_count ?? null,
        ];
    }

    public function messages(ChatConversation $conversation, ?int $afterId = null, int $limit = 50): Collection
    {
        $query = $conversation->messages()->orderBy('id');

        if ($afterId && $afterId > 0) {
            $query->where('id', '>', $afterId);
        }

        return $query->limit($limit)->get();
    }

    public function serializeMessages(Collection|SupportCollection $messages): array
    {
        return $messages
            ->map(fn (ChatMessage $message) => $this->conversations->serializeMessage($message))
            ->values()
            ->all();
    }

    public function reply(ChatConversation $conversation, User $admin, string $message): ChatMessage
    {
        return $this->conversations->storeSystemMessage(
            $conversation,
            $message,
            'admin_reply',
            'admin_reply',
            [
                'source' => 'admin',
                'admin_name' => $admin->name,
            ],
            $admin->id
        );
    }

    public function markViewed(ChatConversation $conversation, User $admin): ChatConversation
    {
        $conversation->forceFill([
            'status' => $conversation->status === 'resolved' ? 'resolved' : 'in_progress',
            'user_id' => $conversation->user_id ?: $admin->id,
            'assigned_at' => $conversation->assigned_at ?: now(),
            'admin_last_read_at' => now(),
        ])->save();

        return $conversation->fresh(['customer', 'latestMessage', 'assignee']);
    }

    public function assignToAdmin(ChatConversation $conversation, User $admin): ChatConversation
    {
        $conversation->forceFill([
            'user_id' => $admin->id,
            'assigned_at' => now(),
            'status' => $conversation->status === 'resolved' ? 'resolved' : 'in_progress',
            'admin_last_read_at' => now(),
        ])->save();

        return $conversation->fresh(['customer', 'latestMessage', 'assignee']);
    }

    public function updateStatus(ChatConversation $conversation, string $status, ?User $admin = null): ChatConversation
    {
        $payload = [
            'status' => $status,
        ];

        if ($admin) {
            $payload['admin_last_read_at'] = now();

            if (! $conversation->user_id) {
                $payload['user_id'] = $admin->id;
                $payload['assigned_at'] = now();
            }
        }

        if ($status === 'resolved') {
            $payload['resolved_at'] = now();
        } else {
            $payload['resolved_at'] = null;
        }

        if ($status === 'in_progress' && ! $conversation->assigned_at) {
            $payload['assigned_at'] = now();
        }

        $conversation->forceFill($payload)->save();

        return $conversation->fresh(['customer', 'latestMessage', 'assignee']);
    }

    protected function conversationQuery(array $filters = [], ?User $admin = null): Builder
    {
        return ChatConversation::query()
            ->when(! empty($filters['keyword']), function (Builder $query) use ($filters) {
                $keyword = trim((string) $filters['keyword']);

                $query->where(function (Builder $nested) use ($keyword) {
                    $nested->whereHas('customer', function (Builder $customerQuery) use ($keyword) {
                        $customerQuery
                            ->where('name', 'like', '%' . $keyword . '%')
                            ->orWhere('email', 'like', '%' . $keyword . '%')
                            ->orWhere('phone', 'like', '%' . $keyword . '%');
                    })->orWhereHas('messages', function (Builder $messageQuery) use ($keyword) {
                        $messageQuery->where('message', 'like', '%' . $keyword . '%');
                    });
                });
            })
            ->when(($filters['source'] ?? null) === 'guest', fn (Builder $query) => $query->whereNull('customer_id'))
            ->when(($filters['source'] ?? null) === 'customer', fn (Builder $query) => $query->whereNotNull('customer_id'))
            ->when(! empty($filters['status']) && $filters['status'] !== 'all', fn (Builder $query) => $query->where('status', $filters['status']))
            ->when(($filters['scope'] ?? 'all') === 'mine' && $admin, fn (Builder $query) => $query->where('user_id', $admin->id));
    }
}
