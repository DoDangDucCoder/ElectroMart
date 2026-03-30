<?php

namespace App\Http\Controllers\Admin;

use App\Application\AI\Services\ChatInboxService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chatbot\StoreChatMessageRequest;
use App\Infrastructure\Persistence\Eloquent\Support\ChatConversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatInboxController extends Controller
{
    public function __construct(
        private readonly ChatInboxService $inbox
    ) {
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'keyword' => ['nullable', 'string', 'max:255'],
            'source' => ['nullable', 'in:all,guest,customer'],
            'status' => ['nullable', 'in:all,unread,in_progress,resolved'],
            'scope' => ['nullable', 'in:all,mine'],
        ]);

        $filters['source'] = $filters['source'] ?? 'all';
        $filters['status'] = $filters['status'] ?? 'all';
        $filters['scope'] = $filters['scope'] ?? 'all';

        $admin = $request->user('web');
        $conversations = $this->inbox->paginateConversations($filters, $admin, 15);
        $selectedConversation = null;

        if ($selectedId = (int) $request->query('conversation_id', 0)) {
            $selectedConversation = $this->inbox->findConversation($selectedId, $filters, $admin);
        }

        if (! $selectedConversation) {
            $selectedConversation = $conversations->getCollection()->first();
        }

        if ($selectedConversation) {
            $selectedConversation = $this->inbox->markViewed($selectedConversation, $admin);
        }

        $selectedMessages = $selectedConversation
            ? $this->inbox->serializeMessages($this->inbox->messages($selectedConversation, null, 80))
            : [];

        return view('admin.chat_inbox.index', [
            'filters' => $filters,
            'summary' => $this->inbox->summary($filters, $admin),
            'conversations' => $conversations,
            'selected_conversation' => $selectedConversation,
            'selected_messages' => $selectedMessages,
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'keyword' => ['nullable', 'string', 'max:255'],
            'source' => ['nullable', 'in:all,guest,customer'],
            'status' => ['nullable', 'in:all,unread,in_progress,resolved'],
            'scope' => ['nullable', 'in:all,mine'],
        ]);

        $filters['source'] = $filters['source'] ?? 'all';
        $filters['status'] = $filters['status'] ?? 'all';
        $filters['scope'] = $filters['scope'] ?? 'all';
        $admin = $request->user('web');
        $conversations = $this->inbox->paginateConversations($filters, $admin, 15);

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => $this->inbox->summary($filters, $admin),
                'items' => $conversations->getCollection()
                    ->map(fn (ChatConversation $conversation) => $this->inbox->serializeConversationSummary($conversation))
                    ->all(),
                'pagination' => [
                    'current_page' => $conversations->currentPage(),
                    'last_page' => $conversations->lastPage(),
                    'total' => $conversations->total(),
                ],
            ],
        ]);
    }

    public function show(Request $request, ChatConversation $conversation): JsonResponse
    {
        $conversation = $this->inbox->markViewed($conversation, $request->user('web'));
        $afterId = (int) $request->query('after_id', 0);
        $messages = $this->inbox->messages($conversation, $afterId > 0 ? $afterId : null, 80);

        return response()->json([
            'success' => true,
            'data' => [
                'conversation' => $this->inbox->serializeConversationSummary($conversation->loadMissing(['customer', 'latestMessage'])),
                'messages' => $this->inbox->serializeMessages($messages),
            ],
        ]);
    }

    public function reply(StoreChatMessageRequest $request, ChatConversation $conversation): JsonResponse
    {
        $message = $this->inbox->reply($conversation, $request->user('web'), (string) $request->validated('message'));

        return response()->json([
            'success' => true,
            'data' => [
                'message' => $this->inbox->serializeMessages(collect([$message]))[0],
            ],
        ]);
    }

    public function assign(Request $request, ChatConversation $conversation): JsonResponse
    {
        $conversation = $this->inbox->assignToAdmin($conversation, $request->user('web'));

        return response()->json([
            'success' => true,
            'data' => [
                'conversation' => $this->inbox->serializeConversationSummary($conversation),
            ],
        ]);
    }

    public function updateStatus(Request $request, ChatConversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:unread,in_progress,resolved'],
        ]);

        $conversation = $this->inbox->updateStatus($conversation, $validated['status'], $request->user('web'));

        return response()->json([
            'success' => true,
            'data' => [
                'conversation' => $this->inbox->serializeConversationSummary($conversation),
            ],
        ]);
    }
}
