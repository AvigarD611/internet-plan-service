<?php

use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Helper function for getting environment variables
if (!function_exists('env')) {
    function env($key, $default = null) {
        $value = $_ENV[$key] ?? false;

        if ($value === false) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'null':
            case '(null)':
                return null;
            case 'empty':
            case '(empty)':
                return '';
        }

        return $value;
    }
}