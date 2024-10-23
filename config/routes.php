<?php

return [
    'GET' => [
        '/api/internet-plans/sync' => [
            'controller' => App\Controllers\InternetPlanSyncController::class,
            'method' => 'sync',
            'params' => []
        ],
        '/api/internet-plans/stats' => [
            'controller' => App\Controllers\InternetPlanController::class,
            'method' => 'getStats',
            'params' => []
        ],
    ],
    'POST' => [
        '/api/internet-plans' => [
            'controller' => App\Controllers\InternetPlanController::class,
            'method' => 'index',
            'params' => []
        ],
        '/api/internet-plans/create' => [
            'controller' => App\Controllers\InternetPlanController::class,
            'method' => 'create',
            'params' => ['name', 'status', 'price', 'type', 'category', 'tags']
        ],
    ],
];