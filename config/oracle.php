<?php

return [
    'oracle' => [
        'driver'        => 'oracle',
        'tns'           => env('DB_TNS', ''),
        'host'          => env('DB_HOST', '172.23.50.95'),
        'port'          => env('DB_PORT', '1521'),
        'database'      => env('DB_DATABASE', 'CATGIS'),
        'username'      => env('DB_USERNAME3', 'CATASTROUSR'),
        'password'      => env('DB_PASSWORD3', 'k4t4str03d'),
        'charset'       => env('DB_CHARSET', 'AL32UTF8'),
        'prefix'        => env('DB_PREFIX', ''),
        'prefix_schema' => env('DB_SCHEMA_PREFIX', 'CATASTRO'),
        // 'edition'       => env('DB_EDITION', 'ora$base'),
    ],
];
