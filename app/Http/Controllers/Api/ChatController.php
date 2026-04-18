<?php

namespace App\Http\Controllers\Api;

use App\Application\AI\Services\ChatbotConversationService;
use App\Application\AI\Services\ChatbotService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chatbot\StoreChatMessageRequest;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class ChatController extends Controller
{
    public function __construct(
        private readonly ChatbotConversationService $conversations,
        private readonly ChatbotService $chatbot
    ) {
    }

    public function conversation(Request $request): JsonResponse
    {
        $customerId = $this->resolveCustomerId($request);
        $sessionToken = $this->resolveSessionToken($request);
        $afterId = (int) $request->query('after_id', 0);

        if (! $this->conversations->tablesReady()) {
            return response()->json([
                'success' => true,
                'data' => $this->conversations->statelessConversationPayload(
                    'api',
                    $sessionToken,
                    $afterId > 0 ? [] : [$this->conversations->statelessWelcomeMessagePayload()]
                ),
            ]);
        }

        $conversation = $this->conversations->bootstrap($customerId, null, $sessionToken, 'api');
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
        $customerId = $this->resolveCustomerId($request);
        $sessionToken = $this->resolveSessionToken($request);
        $validated = $request->validated();

        if (! $this->conversations->tablesReady()) {
            $welcomeMessage = $this->conversations->statelessWelcomeMessagePayload();
            $userMessage = $this->conversations->statelessMessagePayload(
                'user',
                $validated['message'],
                null,
                'user_message'
            );
            $reply = $this->chatbot->reply($validated['message'], $customerId, $request->ip(), []);
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
                        'api',
                        $sessionToken,
                        [$welcomeMessage, $userMessage, $botMessage]
                    ),
                    'user_message' => $userMessage,
                    'bot_message' => $botMessage,
                ],
            ]);
        }

        $conversation = $this->conversations->bootstrap($customerId, null, $sessionToken, 'api');

        $history = $this->conversations->recentMessages($conversation, 8)
            ->map(fn ($message) => [
                'sender' => $message->sender,
                'message' => $message->message,
            ])
            ->all();

        $userMessage = $this->conversations->storeUserMessage($conversation, $validated['message']);
        $reply = $this->chatbot->reply($validated['message'], $customerId, $request->ip(), $history);
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

    protected function resolveSessionToken(Request $request): string
    {
        $token = (string) ($request->input('session_token') ?? '');

        return $token !== '' ? $token : (string) Str::uuid();
    }

    protected function resolveCustomerId(Request $request): ?int
    {
        $bearerToken = $request->bearerToken();

        if (! $bearerToken) {
            return null;
        }

        $token = PersonalAccessToken::findToken($bearerToken);

        if (! $token || ! $token->tokenable instanceof Customer) {
            return null;
        }

        return (int) $token->tokenable->id;
    }
}
