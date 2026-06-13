<?php

return [
    'admin' => [
        'username' => env('MIT_ADMIN_USERNAME', 'admin'),
        'password' => env('MIT_ADMIN_PASSWORD', 'adminMIT123'),
        'identifier' => env('MIT_ADMIN_IDENTIFIER', 'admin-demo'),
    ],

    'target_ttd' => [
        '2022' => 4,
        '2023' => 24,
        '2024' => 72,
        'total' => 100,
        'minimum_weekly' => 8,
    ],
];
