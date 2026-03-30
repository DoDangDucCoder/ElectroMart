<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tbl_slider')) {
            return;
        }

        Schema::table('tbl_slider', function (Blueprint $table) {
            if (! Schema::hasColumn('tbl_slider', 'slider_alt')) {
                $table->string('slider_alt')->nullable()->after('slider_name');
            }

            if (! Schema::hasColumn('tbl_slider', 'slider_tone')) {
                $table->string('slider_tone', 30)->nullable()->after('slider_desc');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('tbl_slider')) {
            return;
        }

        Schema::table('tbl_slider', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('tbl_slider', 'slider_alt')) {
                $columns[] = 'slider_alt';
            }

            if (Schema::hasColumn('tbl_slider', 'slider_tone')) {
                $columns[] = 'slider_tone';
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
