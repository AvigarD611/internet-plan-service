<?php

use App\Services\InternetPlanSyncService;

require_once __DIR__ . '/../../vendor/autoload.php';

$container = require __DIR__ . '/../../config/container.php';

/** @var InternetPlanSyncService $syncService */
$syncService = $container->get(InternetPlanSyncService::class);
$logger = $container->get(App\Logging\Logger::class);

try {
    $logger->log('info', "Starting internet plans sync process");
    $syncService->sync();
    $logger->log('info', "Internet plans sync process completed successfully");
} catch (\Exception $e) {
    $logger->log('error', "Error during internet plans sync: " . $e->getMessage());
    exit(1);
}