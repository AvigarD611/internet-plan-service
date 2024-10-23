<?php

namespace App\Logging;

class Logger
{
    public function log(string $level, string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        echo "[$timestamp] [$level] $message" . PHP_EOL;
    }
}