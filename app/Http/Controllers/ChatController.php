<?php

namespace App\Http\Controllers;

use App\Application\AI\Services\ChatbotConversationService;
use App\Application\AI\Services\ChatbotService;
use App\Http\Requests\Chatbot\StoreChatMessageRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function __construct(
        private readonly ChatbotConversationService $conversations,
        private readonly ChatbotService $chatbot
    ) {
    }

    public function conversation(Request $request): JsonResponse
    {
        $sessionToken = $this->resolveSessionToken();
        $afterId = (int) $request->query('after_id', 0);

        if (! $this->conversations->tablesReady()) {
            return response()->json([
                'success' => true,
                'data' => $this->conversations->statelessConversationPayload(
                    'storefront',
                    $sessionToken,
                    $afterId > 0 ? [] : [$this->conversations->statelessWelcomeMessagePayload()]
                ),
            ]);
        }

        $conversation = $this->conversations->bootstrap(
            $this->resolveCustomerId(),
            null,
            $sessionToken,
            'storefront'
        );
        $messages = $afterId > 0
            ? $this->conversations->messagesAfter($conversation, $afterId, 50)
            : $this->conversations->recentMessages($conversation, 20);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $conversation->id,
                'channel' => $conversation->channel,
                'session_token' => $conversation->session_token,
                'last_message_at' => optional($conversation->last_message_at)->toIso8601String(),
                'messages' => $this->conversations->serializeMessages($messages),
            ],
        ]);
    }

    public function store(StoreChatMessageRequest $request): JsonResponse
    {
        $sessionToken = $this->resolveSessionToken();
        $validated = $request->validated();

        if (! $this->conversations->tablesReady()) {
            $welcomeMessage = $this->conversations->statelessWelcomeMessagePayload();
            $userMessage = $this->conversations->statelessMessagePayload(
                'user',
                $validated['message'],
                null,
                'user_message'
            );
            $reply = $this->chatbot->reply(
                $validated['message'],
                $this->resolveCustomerId(),
                $request->ip(),
                []
            );
            $botMessage = $this->conversations->statelessMessagePayload(
                'bot',
                (string) ($reply['message'] ?? config('chatbot.fallback_message')),
                (string) ($reply['intent'] ?? 'fallback'),
                (string) ($reply['response_type'] ?? 'text'),
                [
                    'products' => (array) ($reply['products'] ?? []),
                    'actions' => (array) ($reply['actions'] ?? []),
                    'source' => $reply['source'] ?? 'rule_based',
                ]
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'conversation' => $this->conversations->statelessConversationPayload(
                        'storefront',
                        $sessionToken,
                        [$welcomeMessage, $userMessage, $botMessage]
                    ),
                    'user_message' => $userMessage,
                    'bot_message' => $botMessage,
                ],
            ]);
        }

        $conversation = $this->conversations->bootstrap(
            $this->resolveCustomerId(),
            null,
            $sessionToken,
            'storefront'
        );
        $history = $this->conversations->recentMessages($conversation, 8)
            ->map(fn ($message) => [
                'sender' => $message->sender,
                'message' => $message->message,
            ])
            ->all();

        $userMessage = $this->conversations->storeUserMessage($conversation, $validated['message']);
        $reply = $this->chatbot->reply(
            $validated['message'],
            $this->resolveCustomerId(),
            $request->ip(),
            $history
        );

        $botMessage = $this->conversations->storeBotMessage(
            $conversation,
            (string) ($reply['message'] ?? config('chatbot.fallback_message')),
            (string) ($reply['response_type'] ?? 'text'),
            (string) ($reply['intent'] ?? 'fallback'),
            [
                'products' => (array) ($reply['products'] ?? []),
                'actions' => (array) ($reply['actions'] ?? []),
                'source' => $reply['source'] ?? 'rule_based',
            ]
        );

        return response()->json([
            'success' => true,
            'data' => [
                'conversation' => $this->conversations->serializeConversation($conversation),
                'user_message' => $this->conversations->serializeMessage($userMessage),
                'bot_message' => $this->conversations->serializeMessage($botMessage),
            ],
        ]);
    }

    protected function resolveSessionToken(): string
    {
        $token = (string) session('chatbot_session_token', '');

        if ($token === '') {
            $token = (string) Str::uuid();
            session(['chatbot_session_token' => $token]);
        }

        return $token;
    }

    protected function resolveCustomerId(): ?int
    {
        $customerId = (int) session('customer_id');

        return $customerId > 0 ? $customerId : null;
    }
}
