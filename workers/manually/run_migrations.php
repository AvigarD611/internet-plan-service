<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$container = require __DIR__ . '/../../config/container.php';

$pdo = $container->get(PDO::class);
$logger = $container->get(App\Logging\Logger::class);

$migrationsDir = __DIR__ . '/../../migrations';
$migrations = glob($migrationsDir . '/*.sql');

foreach ($migrations as $migration) {
    $migrationName = basename($migration);
    $logger->log('info', "Running migration: $migrationName");

    try {
        $sql = file_get_contents($migration);
        $pdo->exec($sql);
        $logger->log('info', "Successfully executed migration: $migrationName");
    } catch (\PDOException $e) {
        $logger->log('error', "Error executing migration $migrationName: " . $e->getMessage());
        exit(1);
    }
}

$logger->log('info', "All migrations completed successfully.");