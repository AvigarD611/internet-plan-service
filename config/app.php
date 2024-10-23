<?php

return [
    'app_name' => 'Internet Plan Service',
    'app_env' => 'development', // 'production', 'staging', 'development'
    'debug' => true,
    'timezone' => 'UTC',
    'log_level' => 'debug', // 'debug', 'info', 'warning', 'error'
    'cron_schedule' => [
        'sync_internet_plans' => '*/30 * * * *', // Every 30 minutes
    ],
];