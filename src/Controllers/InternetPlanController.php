<?php

namespace App\Controllers;

use App\Models\InternetPlan;

class InternetPlanController
{
    private $internetPlan;

    public function __construct(InternetPlan $internetPlan)
    {
        $this->internetPlan = $internetPlan;
    }

    public function index($params)
    {
        // Get JSON body for POST request
        $requestBody = json_decode(file_get_contents('php://input'), true) ?? [];

        // Extract pagination parameters with defaults
        $page = isset($requestBody['page']) ? max(1, (int)$requestBody['page']) : 1;
        $perPage = isset($requestBody['per_page']) ? max(1, min(100, (int)$requestBody['per_page'])) : 10;

        // Extract filters, ensure they're strings or null
        $filters = [];
        if (!empty($requestBody['name'])) {
            $filters['name'] = (string)$requestBody['name'];
        }
        if (!empty($requestBody['category'])) {
            $filters['category'] = (string)$requestBody['category'];
        }

        // Get filtered and paginated results
        $result = $this->internetPlan->getAllActive($filters, $page, $perPage);

        // Add request info to response
        $result['filters'] = $filters;

        return $result;
    }

    public function create($params)
    {
        $newPlanId = $this->internetPlan->create($params);
        return [
            'message' => 'Plan created',
            'id' => $newPlanId
        ];
    }

    public function getStats()
    {
        return $this->internetPlan->getStatusStats();
    }
}