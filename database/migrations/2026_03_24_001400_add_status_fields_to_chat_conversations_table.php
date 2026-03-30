<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->string('status', 30)->default('unread')->after('channel');
            $table->timestamp('assigned_at')->nullable()->after('status');
            $table->timestamp('resolved_at')->nullable()->after('assigned_at');
            $table->timestamp('admin_last_read_at')->nullable()->after('resolved_at');
            $table->index(['status', 'last_message_at']);
        });
    }

    public function down(): void
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->dropIndex(['status', 'last_message_at']);
            $table->dropColumn([
                'status',
                'assigned_at',
                'resolved_at',
                'admin_last_read_at',
            ]);
        });
    }
};
