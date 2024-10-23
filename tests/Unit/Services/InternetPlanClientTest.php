<?php

namespace Tests\Unit\Services;

use App\Services\InternetPlanClient;
use Lib\Services\HttpClient;
use App\Logging\Logger;
use PHPUnit\Framework\TestCase;

class InternetPlanClientTest extends TestCase
{
    public function testFetchPlansFromApi()
    {
        $httpClient = $this->createMock(HttpClient::class);
        $logger = $this->createMock(Logger::class);

        $apiConfig = [
            'api_url' => 'http://api.example.com',
            'api_timeout' => 30,
            'max_retries' => 3,
            'retry_delay' => 1000,
        ];

        $expectedResponse = [
            [
                'guid' => 'test-guid',
                'name' => 'Test Plan',
                'status' => 'Active',
                'price' => '$99.99',
                'type' => 'Fiber',
                'category' => 'Monthly',
                'tags' => ['tag1', 'tag2']
            ]
        ];

        $httpClient->method('get')
            ->willReturn($expectedResponse);

        $client = new InternetPlanClient($httpClient, $apiConfig, $logger);
        $result = $client->fetchPlans();

        $this->assertEquals($expectedResponse, $result);
    }

    public function testFetchPlansFromFile()
    {
        $httpClient = $this->createMock(HttpClient::class);
        $logger = $this->createMock(Logger::class);

        // Create temporary mock file
        $tempFile = tempnam(sys_get_temp_dir(), 'mock_');
        $mockData = json_encode([
            [
                'guid' => 'test-guid',
                'name' => 'Test Plan',
                'status' => 'Active',
                'price' => '$99.99',
                'type' => 'Fiber',
                'category' => 'Monthly',
                'tags' => ['tag1', 'tag2']
            ]
        ]);
        file_put_contents($tempFile, $mockData);

        $apiConfig = [
            'api_url' => 'file://' . $tempFile,
            'api_timeout' => 30,
            'max_retries' => 3,
            'retry_delay' => 1000,
        ];

        $client = new InternetPlanClient($httpClient, $apiConfig, $logger);
        $result = $client->fetchPlans();

        $this->assertEquals(json_decode($mockData, true), $result);

        // Clean up
        unlink($tempFile);
    }

    public function testFetchPlansWithError()
    {
        $httpClient = $this->createMock(HttpClient::class);
        $logger = $this->createMock(Logger::class);

        $apiConfig = [
            'api_url' => 'http://api.example.com',
            'api_timeout' => 30,
            'max_retries' => 1,
            'retry_delay' => 1000,
        ];

        $httpClient->method('get')
            ->willThrowException(new \RuntimeException('API Error'));

        $client = new InternetPlanClient($httpClient, $apiConfig, $logger);

        $this->expectException(\RuntimeException::class);
        $client->fetchPlans();
    }
}