<?php

return [
    'db_host' => env('DB_HOST', 'localhost'),
    'db_port' => (int)env('DB_PORT', 3306),
    'db_name' => env('DB_DATABASE', 'localhost'),
    'db_user' => env('DB_USERNAME', 'root'),
    'db_pass' => env('DB_PASSWORD', 'root'),
];