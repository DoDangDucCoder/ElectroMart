<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SettingsController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = $this->publicSettings();

        return response()->json([
            'success' => true,
            'data' => $settings,
            'tables_ready' => $this->settingsTableReady(),
        ]);
    }

    public function show(string $key): JsonResponse
    {
        if (! $this->settingsTableReady()) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found',
                'tables_ready' => false,
            ], 404);
        }

        $settingQuery = DB::table('settings')->where('key', $key);

        if ($this->hasPublicVisibilityColumn()) {
            $settingQuery->where('is_public', true);
        }

        $setting = $settingQuery->first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found',
                'tables_ready' => true,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'key' => $setting->key,
                'value' => $setting->value,
                'type' => $setting->type,
            ],
            'tables_ready' => true,
        ]);
    }

    public function contact(): JsonResponse
    {
        $settings = $this->publicSettings();

        $contactInfo = [
            'phone' => $settings['contact_phone'] ?? null,
            'email' => $settings['contact_email'] ?? null,
            'address' => $settings['contact_address'] ?? null,
            'working_hours' => $settings['working_hours'] ?? null,
            'social_media' => [
                'facebook' => $settings['facebook_url'] ?? null,
                'instagram' => $settings['instagram_url'] ?? null,
                'youtube' => $settings['youtube_url'] ?? null,
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $contactInfo,
            'tables_ready' => $this->settingsTableReady(),
        ]);
    }

    public function seo(): JsonResponse
    {
        $settings = $this->publicSettings();

        $seoSettings = [
            'site_name' => $settings['site_name'] ?? null,
            'site_description' => $settings['site_description'] ?? null,
            'site_keywords' => $settings['site_keywords'] ?? null,
            'logo_url' => $settings['logo_url'] ?? null,
            'favicon_url' => $settings['favicon_url'] ?? null,
        ];

        return response()->json([
            'success' => true,
            'data' => $seoSettings,
            'tables_ready' => $this->settingsTableReady(),
        ]);
    }

    protected function settingsTableReady(): bool
    {
        return Schema::hasTable('settings');
    }

    protected function hasPublicVisibilityColumn(): bool
    {
        return $this->settingsTableReady() && Schema::hasColumn('settings', 'is_public');
    }

    protected function publicSettings(): array
    {
        if (! $this->settingsTableReady()) {
            return [];
        }

        $query = DB::table('settings');

        if ($this->hasPublicVisibilityColumn()) {
            $query->where('is_public', true);
        }

        return $query->pluck('value', 'key')->toArray();
    }
}
