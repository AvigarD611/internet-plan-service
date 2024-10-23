<?php

namespace Tests\Unit\Services;

use App\Services\InternetPlanSyncService;
use App\Services\InternetPlanClient;
use App\Models\InternetPlan;
use App\Logging\Logger;
use PHPUnit\Framework\TestCase;

class InternetPlanSyncServiceTest extends TestCase
{
    public function testSync()
    {
        // Create mocks
        $client = $this->createMock(InternetPlanClient::class);
        $model = $this->createMock(InternetPlan::class);
        $logger = $this->createMock(Logger::class);

        // Sample data that matches the structure from the mock API
        $mockApiResponse = [
            [
                'guid' => 'test-guid-1',
                'name' => 'Test Plan 1',
                'status' => 'Active',
                'price' => '$99.99',
                'type' => 'Fiber',
                'category' => 'Monthly',
                'tags' => ['tag1', 'tag2']
            ],
            [
                'guid' => 'test-guid-2',
                'name' => 'Test Plan 2',
                'status' => 'Active',
                'price' => '$79.99',
                'type' => 'Lan',
                'category' => 'Quarterly',
                'tags' => ['tag2', 'tag3']
            ]
        ];

        // Configure mock behaviors
        $client->method('fetchPlans')
            ->willReturn($mockApiResponse);

        $model->method('getAllActive')
            ->willReturn([
                [
                    'guid' => 'test-guid-1',
                    'name' => 'Old Plan 1',
                    'status' => 'Active',
                    'price' => 99.99,
                    'type' => 'Fiber',
                    'category' => 'Monthly',
                    'tags' => ['tag1']
                ]
            ]);

        // Create service instance
        $service = new InternetPlanSyncService($client, $model, $logger);

        // Configure expectations for model methods
        $model->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($data) {
                return $data['guid'] === 'test-guid-2' &&
                    $data['price'] === 79.99;  // Price should be converted to float
            }));

        $model->expects($this->once())
            ->method('findByGuid')
            ->with('test-guid-1')
            ->willReturn(['id' => 1]);

        $model->expects($this->once())
            ->method('update')
            ->with(
                1,
                $this->callback(function ($data) {
                    return $data['price'] === 99.99;  // Price should be converted to float
                })
            );

        // Execute the sync method
        $service->sync();
    }

    public function testSyncWithError()
    {
        $client = $this->createMock(InternetPlanClient::class);
        $model = $this->createMock(InternetPlan::class);
        $logger = $this->createMock(Logger::class);

        // Configure client to throw an exception
        $client->method('fetchPlans')
            ->willThrowException(new \Exception('API Error'));

        // Expect logger to log the error
        $logger->expects($this->once())
            ->method('log')
            ->with(
                'error',
                $this->stringContains('Internet plans sync failed')
            );

        $service = new InternetPlanSyncService($client, $model, $logger);

        $this->expectException(\Exception::class);
        $service->sync();
    }
}