<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Eloquent\Support\ChatConversation;
use App\Infrastructure\Persistence\Eloquent\Support\ChatMessage;
use App\Models\User;
use App\Support\AdminAccess;
use App\Support\AdminPermissions;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminChatInboxTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    public function test_admin_can_render_chat_inbox_and_reply_to_conversation(): void
    {
        $this->postJson('/chatbot/messages', [
            'message' => 'Tôi cần hỗ trợ bảo hành',
        ])->assertOk();

        $admin = $this->createAdminUser('Admin');

        $this->actingAs($admin, 'web')
            ->get('/chat-inbox')
            ->assertOk()
            ->assertSee('Hộp thư chatbot')
            ->assertSee('Khách vãng lai');

        $conversationId = \DB::table('chat_conversations')->value('id');

        $this->actingAs($admin, 'web')
            ->getJson('/chat-inbox/conversations/' . $conversationId)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.conversation.id', $conversationId)
            ->assertJsonPath('data.conversation.status', 'in_progress')
            ->assertJsonPath('data.conversation.assignee.id', $admin->id);

        $this->actingAs($admin, 'web')
            ->postJson('/chat-inbox/conversations/' . $conversationId . '/reply', [
                'message' => 'Admin đã tiếp nhận và sẽ hỗ trợ thêm.',
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.message.response_type', 'admin_reply');

        $this->actingAs($admin, 'web')
            ->patchJson('/chat-inbox/conversations/' . $conversationId . '/status', [
                'status' => 'resolved',
            ])
            ->assertOk()
            ->assertJsonPath('data.conversation.status', 'resolved');

        $this->assertDatabaseHas('chat_messages', [
            'conversation_id' => $conversationId,
            'sender' => 'system',
            'message' => 'Admin đã tiếp nhận và sẽ hỗ trợ thêm.',
            'user_id' => $admin->id,
        ]);

        $this->assertDatabaseHas('chat_conversations', [
            'id' => $conversationId,
            'user_id' => $admin->id,
            'status' => 'resolved',
        ]);
    }

    public function test_editor_and_manager_cannot_access_chat_inbox(): void
    {
        $editor = $this->createAdminUser('Editor');
        $manager = $this->createAdminUser('Manager');

        $this->actingAs($editor, 'web')
            ->get('/chat-inbox')
            ->assertForbidden();

        $this->actingAs($manager, 'web')
            ->get('/chat-inbox')
            ->assertForbidden();
    }

    public function test_admin_can_filter_only_conversations_assigned_to_them(): void
    {
        $adminA = $this->createAdminUser('Admin');
        $adminB = $this->createAdminUser('Admin');

        $conversationForAdminA = ChatConversation::create([
            'user_id' => $adminA->id,
            'session_token' => 'session-admin-a',
            'channel' => 'storefront',
            'status' => 'unread',
            'last_message_at' => now()->subMinute(),
            'meta' => ['source' => 'chatbot'],
        ]);

        ChatMessage::create([
            'conversation_id' => $conversationForAdminA->id,
            'user_id' => $adminA->id,
            'sender' => 'user',
            'message' => 'Tôi cần hỗ trợ đổi trả',
            'response_type' => 'user_message',
        ]);

        $conversationForAdminB = ChatConversation::create([
            'user_id' => $adminB->id,
            'session_token' => 'session-admin-b',
            'channel' => 'storefront',
            'status' => 'in_progress',
            'last_message_at' => now(),
            'meta' => ['source' => 'chatbot'],
        ]);

        ChatMessage::create([
            'conversation_id' => $conversationForAdminB->id,
            'user_id' => $adminB->id,
            'sender' => 'user',
            'message' => 'Tôi muốn hỏi về sản phẩm',
            'response_type' => 'user_message',
        ]);

        $this->actingAs($adminA, 'web')
            ->get('/chat-inbox?scope=mine')
            ->assertOk()
            ->assertSee('Chỉ hiển thị các hội thoại đã gán cho bạn.')
            ->assertSee('session-admin-a')
            ->assertDontSee('session-admin-b');

        $this->actingAs($adminA, 'web')
            ->getJson('/chat-inbox/conversations?scope=mine')
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.id', $conversationForAdminA->id)
            ->assertJsonPath('data.items.0.assignee.id', $adminA->id);
    }

    public function test_admin_sidebar_and_dashboard_show_unread_chat_indicators(): void
    {
        $admin = $this->createAdminUser('Admin');

        $conversation = ChatConversation::create([
            'user_id' => $admin->id,
            'session_token' => 'sidebar-unread-session',
            'channel' => 'storefront',
            'status' => 'unread',
            'last_message_at' => now(),
            'meta' => ['source' => 'chatbot'],
        ]);

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'user_id' => $admin->id,
            'sender' => 'user',
            'message' => 'Tôi cần hỗ trợ chat gấp',
            'response_type' => 'user_message',
        ]);

        $this->actingAs($admin, 'web')
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('data-admin-chat-unread-badge', false)
            ->assertSee('data-admin-chat-dashboard-card', false)
            ->assertSee('Hộp thư chat')
            ->assertSee('Xem hội thoại của tôi');
    }

    public function test_admin_chat_inbox_falls_back_gracefully_when_chat_tables_are_missing(): void
    {
        $admin = $this->createAdminUser('Admin');

        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_conversations');

        $this->actingAs($admin, 'web')
            ->get('/chat-inbox')
            ->assertOk()
            ->assertSee('Hộp thư chatbot')
            ->assertSee('Bảng dữ liệu chat chưa sẵn sàng');

        $this->actingAs($admin, 'web')
            ->getJson('/chat-inbox/conversations')
            ->assertOk()
            ->assertJsonPath('data.tables_ready', false)
            ->assertJsonPath('data.summary.total_conversations', 0)
            ->assertJsonCount(0, 'data.items');

        $this->actingAs($admin, 'web')
            ->get('/dashboard')
            ->assertOk()
            ->assertDontSee('data-admin-chat-unread-badge', false);
    }

    protected function createAdminUser(string $roleName): User
    {
        foreach (AdminPermissions::all() as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => AdminAccess::guardName(),
            ]);
        }

        $role = Role::firstOrCreate([
            'name' => $roleName,
            'guard_name' => AdminAccess::guardName(),
        ]);
        $role->syncPermissions(AdminPermissions::defaultsForRole($roleName));

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
