<?php

namespace Tests\Feature;

use App\Application\Shared\Services\CacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SettingsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_endpoints_fall_back_cleanly_when_table_is_missing(): void
    {
        Schema::dropIfExists('settings');
        Cache::forget('all_settings');

        $this->getJson('/api/v1/settings')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('tables_ready', false)
            ->assertExactJson([
                'success' => true,
                'data' => [],
                'tables_ready' => false,
            ]);

        $this->getJson('/api/v1/settings/contact')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('tables_ready', false)
            ->assertJsonPath('data.phone', null)
            ->assertJsonPath('data.email', null);

        $this->getJson('/api/v1/settings/seo')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('tables_ready', false)
            ->assertJsonPath('data.site_name', null)
            ->assertJsonPath('data.logo_url', null);

        $this->getJson('/api/v1/settings/site_name')
            ->assertNotFound()
            ->assertJsonPath('success', false)
            ->assertJsonPath('tables_ready', false);
    }

    public function test_settings_endpoints_and_cache_work_with_legacy_schema_without_is_public_column(): void
    {
        Cache::forget('all_settings');

        DB::table('settings')->insert([
            [
                'key' => 'site_name',
                'value' => 'Legacy ShopW',
                'type' => 'string',
                'group' => 'seo',
                'meta' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'contact_phone',
                'value' => '1900.0000',
                'type' => 'string',
                'group' => 'contact',
                'meta' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->getJson('/api/v1/settings')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('tables_ready', true)
            ->assertJsonPath('data.site_name', 'Legacy ShopW')
            ->assertJsonPath('data.contact_phone', '1900.0000');

        $this->getJson('/api/v1/settings/contact')
            ->assertOk()
            ->assertJsonPath('data.phone', '1900.0000');

        $this->getJson('/api/v1/settings/site_name')
            ->assertOk()
            ->assertJsonPath('data.value', 'Legacy ShopW');

        $this->assertSame([
            'site_name' => 'Legacy ShopW',
            'contact_phone' => '1900.0000',
        ], app(CacheService::class)->getSettings());
    }

    public function test_settings_endpoints_only_expose_public_values_when_visibility_column_exists(): void
    {
        Schema::table('settings', function ($table) {
            $table->boolean('is_public')->default(true);
        });

        Cache::forget('all_settings');

        DB::table('settings')->insert([
            [
                'key' => 'site_name',
                'value' => 'Public ShopW',
                'type' => 'string',
                'group' => 'seo',
                'meta' => null,
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'contact_phone',
                'value' => '0909-secret',
                'type' => 'string',
                'group' => 'contact',
                'meta' => null,
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'contact_email',
                'value' => 'hello@shopw.test',
                'type' => 'string',
                'group' => 'contact',
                'meta' => null,
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'logo_url',
                'value' => '/private/logo.svg',
                'type' => 'string',
                'group' => 'seo',
                'meta' => null,
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->getJson('/api/v1/settings')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('tables_ready', true)
            ->assertJsonPath('data.site_name', 'Public ShopW')
            ->assertJsonPath('data.contact_email', 'hello@shopw.test')
            ->assertJsonMissingPath('data.contact_phone')
            ->assertJsonMissingPath('data.logo_url');

        $this->getJson('/api/v1/settings/contact')
            ->assertOk()
            ->assertJsonPath('data.phone', null)
            ->assertJsonPath('data.email', 'hello@shopw.test');

        $this->getJson('/api/v1/settings/seo')
            ->assertOk()
            ->assertJsonPath('data.site_name', 'Public ShopW')
            ->assertJsonPath('data.logo_url', null);

        $this->getJson('/api/v1/settings/contact_phone')
            ->assertNotFound()
            ->assertJsonPath('success', false)
            ->assertJsonPath('tables_ready', true);

        $this->assertSame([
            'site_name' => 'Public ShopW',
            'contact_email' => 'hello@shopw.test',
        ], app(CacheService::class)->getSettings());
    }
}
