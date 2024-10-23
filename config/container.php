<?php

use DI\ContainerBuilder;
use App\Controllers\InternetPlanController;
use App\Controllers\InternetPlanSyncController;
use App\Models\InternetPlan;
use App\Services\InternetPlanClient;
use App\Services\InternetPlanSyncService;
use App\Logging\Logger;
use Lib\Services\HttpClient;
use Lib\Services\GuzzleHttpClient;
use GuzzleHttp\Client as GuzzleClient;

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    // Database
    PDO::class => function () {
        $dbConfig = require __DIR__ . '/database.php';
        $dsn = sprintf(
            "mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4",
            $dbConfig['db_host'],
            $dbConfig['db_port'],
            $dbConfig['db_name']
        );
        return new PDO($dsn, $dbConfig['db_user'], $dbConfig['db_pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    },

    // Logger
    Logger::class => function () {
        return new Logger();
    },

    // HTTP Client
    HttpClient::class => function () {
        return new GuzzleHttpClient(new GuzzleClient());
    },

    // Internet Plan Model
    InternetPlan::class => function ($container) {
        return new InternetPlan($container->get(PDO::class));
    },

    // Internet Plan Client
    InternetPlanClient::class => function ($container) {
        $apiConfig = require __DIR__ . '/api.php';
        return new InternetPlanClient(
            $container->get(HttpClient::class),
            $apiConfig,
            $container->get(Logger::class)
        );
    },

    // Internet Plan Sync Service
    InternetPlanSyncService::class => function ($container) {
        return new InternetPlanSyncService(
            $container->get(InternetPlanClient::class),
            $container->get(InternetPlan::class),
            $container->get(Logger::class)
        );
    },

    // Controllers
    InternetPlanController::class => function ($container) {
        return new InternetPlanController(
            $container->get(InternetPlan::class)  // This was the issue - injecting the correct dependency
        );
    },

    InternetPlanSyncController::class => function ($container) {
        return new InternetPlanSyncController(
            $container->get(InternetPlanSyncService::class)
        );
    },
]);

return $containerBuilder->build();