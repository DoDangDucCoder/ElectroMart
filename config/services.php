<?php

$storefrontBrandName = env('STOREFRONT_BRAND_NAME', 'ShopW');
$storefrontBrandDescriptor = env('STOREFRONT_BRAND_DESCRIPTOR', 'Cửa hàng công nghệ');
$storefrontContactPhoneDisplay = env('STOREFRONT_CONTACT_PHONE_DISPLAY', '1900.0000');
$storefrontContactPhoneHref = (string) env('STOREFRONT_CONTACT_PHONE_HREF', '19000000');
$storefrontContactPhoneDial = preg_replace('/^tel:/i', '', $storefrontContactPhoneHref) ?: '19000000';
$storefrontContactPhoneLink = str_starts_with(strtolower($storefrontContactPhoneHref), 'tel:')
    ? $storefrontContactPhoneHref
    : 'tel:' . $storefrontContactPhoneDial;

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-5-mini'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
    ],

    'storefront_brand' => [
        'name' => $storefrontBrandName,
        'descriptor' => $storefrontBrandDescriptor,
    ],

    'storefront_support' => [
        'facebook_page_id' => env('STOREFRONT_FACEBOOK_PAGE_ID', '100332251915013'),
        'facebook_url' => env('STOREFRONT_FACEBOOK_MESSENGER_URL', 'https://m.me/' . env('STOREFRONT_FACEBOOK_PAGE_ID', '100332251915013')),
        'facebook_hours' => env('STOREFRONT_FACEBOOK_HOURS', '8h-21h'),
        'zalo_url' => env('STOREFRONT_ZALO_URL'),
        'zalo_hours' => env('STOREFRONT_ZALO_HOURS', '8h-21h'),
        'contact_phone_display' => $storefrontContactPhoneDisplay,
        'contact_phone_href' => $storefrontContactPhoneDial,
        'phone_display' => $storefrontContactPhoneDisplay,
        'phone_href' => $storefrontContactPhoneLink,
        'contact_hours' => env('STOREFRONT_CONTACT_HOURS', '8h-21h'),
        'chat_cta' => env('STOREFRONT_CHAT_CTA', 'Chat mua hàng trực tuyến từ 8h - 24h'),
    ],

];
