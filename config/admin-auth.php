<?php

return [
    'registration' => [
        'enabled' => env('ADMIN_SELF_REGISTRATION_ENABLED', false),
        'invite_code' => env('ADMIN_REGISTRATION_CODE'),
        'bootstrap_role' => env('ADMIN_BOOTSTRAP_ROLE', 'Super Admin'),
        'default_role' => env('ADMIN_DEFAULT_REGISTRATION_ROLE', 'Manager'),
    ],

    'seed_defaults' => [
        'primary_email' => env('ADMIN_SEED_EMAIL', 'admin@shop.com'),
        'primary_password' => env('ADMIN_SEED_PASSWORD', 'CHANGE_ME_STRONG_PASSWORD'),
        'editor_email' => env('ADMIN_EDITOR_SEED_EMAIL', 'editor@shop.com'),
        'editor_password' => env('ADMIN_EDITOR_SEED_PASSWORD', 'CHANGE_ME_STRONG_PASSWORD'),
    ],

    'provisioning' => [
        'default_role' => env('ADMIN_CREATED_USER_DEFAULT_ROLE', 'Staff'),
    ],
];
