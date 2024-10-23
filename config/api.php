<?php

return [
    'api_url' => 'file://' . __DIR__ . '/../mock/internet_plans.json',
    'api_timeout' => 30,
    'max_retries' => 3,
    'retry_delay' => 1000, // in milliseconds
];