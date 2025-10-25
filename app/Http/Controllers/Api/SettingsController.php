<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = DB::table('settings')
            ->where('is_public', true)
            ->pluck('value', 'key')
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    public function show(string $key): JsonResponse
    {
        $setting = DB::table('settings')
            ->where('key', $key)
            ->where('is_public', true)
            ->first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'key' => $setting->key,
                'value' => $setting->value,
                'type' => $setting->type,
            ],
        ]);
    }

    public function contact(): JsonResponse
    {
        $contactInfo = [
            'phone' => DB::table('settings')->where('key', 'contact_phone')->value('value'),
            'email' => DB::table('settings')->where('key', 'contact_email')->value('value'),
            'address' => DB::table('settings')->where('key', 'contact_address')->value('value'),
            'working_hours' => DB::table('settings')->where('key', 'working_hours')->value('value'),
            'social_media' => [
                'facebook' => DB::table('settings')->where('key', 'facebook_url')->value('value'),
                'instagram' => DB::table('settings')->where('key', 'instagram_url')->value('value'),
                'youtube' => DB::table('settings')->where('key', 'youtube_url')->value('value'),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $contactInfo,
        ]);
    }

    public function seo(): JsonResponse
    {
        $seoSettings = [
            'site_name' => DB::table('settings')->where('key', 'site_name')->value('value'),
            'site_description' => DB::table('settings')->where('key', 'site_description')->value('value'),
            'site_keywords' => DB::table('settings')->where('key', 'site_keywords')->value('value'),
            'logo_url' => DB::table('settings')->where('key', 'logo_url')->value('value'),
            'favicon_url' => DB::table('settings')->where('key', 'favicon_url')->value('value'),
        ];

        return response()->json([
            'success' => true,
            'data' => $seoSettings,
        ]);
    }
}
