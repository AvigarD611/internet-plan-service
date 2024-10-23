<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$container = require __DIR__ . '/../../config/container.php';
$appConfig = require __DIR__ . '/../../config/app.php';

$projectRoot = realpath(__DIR__ . '/../..');

foreach ($appConfig['cron_schedule'] as $job => $schedule) {
    $command = "/usr/bin/php {$projectRoot}/workers/scheduled/{$job}.php";
    echo "{$schedule} {$command}\n";
}