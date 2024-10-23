<?php

return [
    'api_url' => str_replace(
        '{APP_PATH}',
        dirname(__DIR__),
        env('API_URL', 'file://{APP_PATH}/mock/internet_plans.json')
    ),
    'api_timeout' => (int)env('API_TIMEOUT', 30),
    'max_retries' => (int)env('API_MAX_RETRIES', 3),
    'retry_delay' => (int)env('API_RETRY_DELAY', 1000),
];