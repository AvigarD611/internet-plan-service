<?php

namespace App\Controllers;

use App\Services\InternetPlanSyncService;

class InternetPlanSyncController
{
    private $syncService;

    public function __construct(InternetPlanSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    public function sync()
    {
        $this->syncService->sync();
        return ['status' => 'success', 'message' => 'Internet plans sync completed'];
    }
}