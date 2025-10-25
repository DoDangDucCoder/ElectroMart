<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create legacy admin table migration
        Schema::create('legacy_admins', function (Blueprint $table) {
            $table->unsignedInteger('admin_id')->primary();
            $table->string('admin_email')->unique();
            $table->string('admin_password');
            $table->string('admin_name');
            $table->string('admin_phone');
            $table->timestamps();
        });

        // Create legacy roles table
        Schema::create('legacy_roles', function (Blueprint $table) {
            $table->unsignedInteger('id_roles')->primary();
            $table->string('name');
            $table->timestamps();
        });

        // Create legacy admin_roles pivot table
        Schema::create('legacy_admin_roles', function (Blueprint $table) {
            $table->unsignedInteger('id_admin_roles')->primary();
            $table->unsignedInteger('admin_admin_id');
            $table->unsignedInteger('roles_id_roles');
            $table->timestamps();

            $table->foreign('admin_admin_id')->references('admin_id')->on('legacy_admins')->onDelete('cascade');
            $table->foreign('roles_id_roles')->references('id_roles')->on('legacy_roles')->onDelete('cascade');
        });

        // Create legacy information table (contact info)
        Schema::create('legacy_information', function (Blueprint $table) {
            $table->unsignedInteger('info_id')->primary();
            $table->mediumText('info_contact');
            $table->text('info_map');
            $table->string('info_logo');
            $table->string('slogan_logo');
            $table->text('info_fanpage');
            $table->timestamps();
        });

        // Create legacy category posts table
        Schema::create('legacy_category_posts', function (Blueprint $table) {
            $table->unsignedInteger('cate_post_id')->primary();
            $table->string('cate_post_name');
            $table->string('cate_post_slug');
            $table->text('cate_post_desc');
            $table->unsignedInteger('cate_post_status')->default(1);
            $table->timestamps();
        });

        // Create legacy icons table
        Schema::create('legacy_icons', function (Blueprint $table) {
            $table->unsignedInteger('id_icons')->primary();
            $table->string('name');
            $table->string('image');
            $table->string('link')->nullable();
            $table->string('category');
            $table->timestamps();
        });

        // Create legacy social customers table
        Schema::create('legacy_social_customers', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->primary();
            $table->string('provider');
            $table->string('provider_user_id');
            $table->unsignedInteger('customer_id');
            $table->timestamps();

            $table->foreign('customer_id')->references('customer_id')->on('legacy_customers')->onDelete('cascade');
        });

        // Create legacy social table
        Schema::create('legacy_social', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->primary();
            $table->string('provider');
            $table->string('provider_user_id');
            $table->unsignedInteger('admin_id');
            $table->timestamps();

            $table->foreign('admin_id')->references('admin_id')->on('legacy_admins')->onDelete('cascade');
        });

        // Create legacy provinces table
        Schema::create('legacy_provinces', function (Blueprint $table) {
            $table->string('matp', 5)->primary();
            $table->string('name');
            $table->string('type');
            $table->timestamps();
        });

        // Create legacy districts table
        Schema::create('legacy_districts', function (Blueprint $table) {
            $table->string('maqh', 5)->primary();
            $table->string('name');
            $table->string('type');
            $table->string('matp', 5);
            $table->timestamps();

            $table->foreign('matp')->references('matp')->on('legacy_provinces')->onDelete('cascade');
        });

        // Create legacy wards table
        Schema::create('legacy_wards', function (Blueprint $table) {
            $table->string('xaid', 5)->primary();
            $table->string('name');
            $table->string('type');
            $table->string('maqh', 5);
            $table->timestamps();

            $table->foreign('maqh')->references('maqh')->on('legacy_districts')->onDelete('cascade');
        });

        // Create legacy feeship table
        Schema::create('legacy_feeship', function (Blueprint $table) {
            $table->unsignedInteger('fee_id')->primary();
            $table->string('fee_matp', 5);
            $table->string('fee_maqh', 5);
            $table->string('fee_xaid', 5);
            $table->decimal('fee_feeship', 10, 2);
            $table->timestamps();

            $table->foreign('fee_matp')->references('matp')->on('legacy_provinces')->onDelete('cascade');
            $table->foreign('fee_maqh')->references('maqh')->on('legacy_districts')->onDelete('cascade');
            $table->foreign('fee_xaid')->references('xaid')->on('legacy_wards')->onDelete('cascade');
        });

        // Create legacy shipping table
        Schema::create('legacy_shipping', function (Blueprint $table) {
            $table->unsignedInteger('shipping_id')->primary();
            $table->string('shipping_name');
            $table->string('shipping_address');
            $table->string('shipping_phone');
            $table->string('shipping_email');
            $table->text('shipping_notes')->nullable();
            $table->string('shipping_method');
            $table->timestamps();
        });

        // Create legacy slider table
        Schema::create('legacy_sliders', function (Blueprint $table) {
            $table->unsignedInteger('slider_id')->primary();
            $table->string('slider_name');
            $table->string('slider_image');
            $table->string('slider_desc')->nullable();
            $table->unsignedInteger('slider_status')->default(1);
            $table->timestamps();
        });

        // Create legacy visitors table
        Schema::create('legacy_visitors', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('ip_address');
            $table->string('date_visitor');
            $table->timestamps();
        });

        // Create legacy statistics table
        Schema::create('legacy_statistics', function (Blueprint $table) {
            $table->unsignedInteger('id_statistic')->primary();
            $table->string('order_date');
            $table->decimal('sales', 10, 2);
            $table->decimal('profit', 10, 2);
            $table->integer('quantity');
            $table->integer('total_order');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legacy_statistics');
        Schema::dropIfExists('legacy_visitors');
        Schema::dropIfExists('legacy_sliders');
        Schema::dropIfExists('legacy_shipping');
        Schema::dropIfExists('legacy_feeship');
        Schema::dropIfExists('legacy_wards');
        Schema::dropIfExists('legacy_districts');
        Schema::dropIfExists('legacy_provinces');
        Schema::dropIfExists('legacy_social');
        Schema::dropIfExists('legacy_social_customers');
        Schema::dropIfExists('legacy_icons');
        Schema::dropIfExists('legacy_information');
        Schema::dropIfExists('legacy_category_posts');
        Schema::dropIfExists('legacy_admin_roles');
        Schema::dropIfExists('legacy_roles');
        Schema::dropIfExists('legacy_admins');
    }
};
