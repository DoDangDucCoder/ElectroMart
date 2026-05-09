<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vietnam_provinces', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('type', 100)->nullable();
            $table->string('slug')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('vietnam_wards', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('province_code', 20)->index();
            $table->string('name');
            $table->string('type', 100)->nullable();
            $table->string('slug')->nullable()->index();
            $table->timestamps();

            $table->index(['province_code', 'code']);
        });

        Schema::table('shipping_fees', function (Blueprint $table) {
            if (! Schema::hasColumn('shipping_fees', 'vietnam_province_code')) {
                $table->string('vietnam_province_code', 20)->nullable()->after('currency')->index();
            }

            if (! Schema::hasColumn('shipping_fees', 'vietnam_ward_code')) {
                $table->string('vietnam_ward_code', 20)->nullable()->after('vietnam_province_code')->index();
            }
        });
    }

    public function down(): void
    {
        if (
            Schema::hasTable('shipping_fees')
            && Schema::hasColumn('shipping_fees', 'vietnam_province_code')
            && Schema::hasColumn('shipping_fees', 'vietnam_ward_code')
        ) {
            DB::table('shipping_fees')
                ->whereNotNull('vietnam_province_code')
                ->orWhereNotNull('vietnam_ward_code')
                ->delete();
        }

        Schema::table('shipping_fees', function (Blueprint $table) {
            if (Schema::hasColumn('shipping_fees', 'vietnam_ward_code')) {
                $table->dropColumn('vietnam_ward_code');
            }

            if (Schema::hasColumn('shipping_fees', 'vietnam_province_code')) {
                $table->dropColumn('vietnam_province_code');
            }
        });

        Schema::dropIfExists('vietnam_wards');
        Schema::dropIfExists('vietnam_provinces');
    }
};
