<?php

namespace App\Services;

use App\Models\InternetPlan;
use App\Logging\Logger;

class InternetPlanSyncService
{
    private $client;
    private $model;
    private $logger;

    public function __construct(InternetPlanClient $client, InternetPlan $model, Logger $logger)
    {
        $this->client = $client;
        $this->model = $model;
        $this->logger = $logger;
    }

    public function sync()
    {
        try {
            $plans = $this->client->fetchPlans();
            $this->processPlan($plans);
            $this->logger->log('info', 'Internet plans sync completed successfully');
        } catch (\Exception $e) {
            var_dump($e->getMessage(), $e->getFile(), $e->getLine());exit;
            $this->logger->log('error', 'Internet plans sync failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function processPlan(array $plans)
    {
        $existingPlans = $this->model->getAllActive();
        $existingGuids = array_column($existingPlans, 'guid');
        $newGuids = array_column($plans, 'guid');

        foreach ($plans as $plan) {
            $this->upsertPlan($plan);
        }

        // Find plans that are no longer in the API response
        $guidsToDeactivate = array_diff($existingGuids, $newGuids);
        foreach ($guidsToDeactivate as $guid) {
            $this->model->setInactive($guid);
            $this->logger->log('info', "Deactivated plan: {$guid}");
        }
    }

    private function upsertPlan(array $plan): void
    {
        // Format the data (remove $ from price)
        $planData = $this->formatPlanData($plan);

        // Try to find existing plan
        $existingPlan = $this->model->findByGuid($planData['guid']);

        if ($existingPlan) {
            // Update existing plan
            $this->model->update($existingPlan['id'], $planData);
            $this->logger->log('info', "Updated plan: {$planData['guid']}");
        } else {
            // Create new plan
            $this->model->create($planData);
            $this->logger->log('info', "Created new plan: {$planData['guid']}");
        }
    }

    private function formatPlanData(array $plan): array
    {
        // Remove the '$' sign from price and convert to float
        $price = str_replace('$', '', $plan['price']);
        $price = (float) $price;

        return [
            'guid' => $plan['guid'],
            'name' => $plan['name'],
            'status' => $plan['status'],
            'price' => $price,
            'type' => $plan['type'],
            'category' => $plan['category'],
            'tags' => $plan['tags']
        ];
    }
}