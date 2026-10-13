<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupon_redemptions', function (Blueprint $table) {
            $table->string('status', 20)->default('redeemed')->after('discount_amount');
            $table->string('reserved_session_id')->nullable()->after('status');
            $table->timestamp('reserved_until')->nullable()->after('reserved_session_id');
            $table->timestamp('redeemed_at')->nullable()->after('reserved_until');
            $table->timestamp('released_at')->nullable()->after('redeemed_at');
            $table->string('release_reason')->nullable()->after('released_at');

            $table->index(['status', 'reserved_until']);
            $table->index(['reserved_session_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('coupon_redemptions', function (Blueprint $table) {
            $table->dropIndex(['status', 'reserved_until']);
            $table->dropIndex(['reserved_session_id', 'status']);
            $table->dropColumn([
                'status',
                'reserved_session_id',
                'reserved_until',
                'redeemed_at',
                'released_at',
                'release_reason',
            ]);
        });
    }
};
