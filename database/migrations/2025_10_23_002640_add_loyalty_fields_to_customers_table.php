<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('loyalty_tier_id')
                ->nullable()
                ->after('is_active')
                ->constrained('loyalty_tiers')
                ->nullOnDelete();
            $table->decimal('loyalty_points_balance', 15, 2)->default(0)->after('loyalty_tier_id');
            $table->timestamp('loyalty_tier_assigned_at')->nullable()->after('loyalty_points_balance');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['loyalty_tier_id']);
            $table->dropColumn(['loyalty_tier_id', 'loyalty_points_balance', 'loyalty_tier_assigned_at']);
        });
    }
};
