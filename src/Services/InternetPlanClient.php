<?php

namespace App\Services;

use Lib\Services\HttpClient;
use App\Logging\Logger;

class InternetPlanClient
{
    private $httpClient;
    private $apiConfig;
    private $logger;

    public function __construct(HttpClient $httpClient, array $apiConfig, Logger $logger)
    {
        $this->httpClient = $httpClient;
        $this->apiConfig = $apiConfig;
        $this->logger = $logger;
    }

    public function fetchPlans(): array
    {
        $url = $this->apiConfig['api_url'];

        if (strpos($url, 'file://') === 0) {
            return $this->fetchFromFile(substr($url, 7));
        } else {
            return $this->fetchFromApi($url);
        }
    }

    private function fetchFromFile(string $filePath): array
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new \RuntimeException("Failed to read mock data file");
        }
        $this->logger->log('info', 'Successfully fetched internet plans from mock file');
        return json_decode($content, true);
    }

    private function fetchFromApi(string $url): array
    {
        $retries = 0;
        while ($retries < $this->apiConfig['max_retries']) {
            try {
                $response = $this->httpClient->get($url, [
                    'timeout' => $this->apiConfig['api_timeout'],
                ]);
                $this->logger->log('info', 'Successfully fetched internet plans from API');
                return $response;
            } catch (\Exception $e) {
                $this->logger->log('error', 'Failed to fetch internet plans: ' . $e->getMessage());
                $retries++;
                if ($retries < $this->apiConfig['max_retries']) {
                    usleep($this->apiConfig['retry_delay'] * 1000);
                }
            }
        }
        throw new \RuntimeException('Failed to fetch internet plans after ' . $this->apiConfig['max_retries'] . ' attempts');
    }
}