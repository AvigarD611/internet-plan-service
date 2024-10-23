<?php

namespace Tests\Unit\Controllers;

use App\Controllers\InternetPlanSyncController;
use App\Services\InternetPlanSyncService;
use PHPUnit\Framework\TestCase;

class InternetPlanSyncControllerTest extends TestCase
{
    public function testSync()
    {
        $syncService = $this->createMock(InternetPlanSyncService::class);
        $syncService->expects($this->once())
            ->method('sync');

        $controller = new InternetPlanSyncController($syncService);
        $result = $controller->sync();

        $this->assertEquals(
            ['status' => 'success', 'message' => 'Internet plans sync completed'],
            $result
        );
    }
}