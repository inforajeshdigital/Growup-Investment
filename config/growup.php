<?php

return [
    // Single login user (created by `php artisan app:create-admin` or the install URL)
    'admin' => [
        'name' => env('ADMIN_NAME', 'Admin'),
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],

    // Secret key for the one-time /install/{key} URL. Leave empty to disable it.
    'install_key' => env('INSTALL_KEY'),

    // Prefix used for receipt numbers, e.g. GIT-2026-00001
    'bill_prefix' => env('BILL_PREFIX', 'GIT'),
];
