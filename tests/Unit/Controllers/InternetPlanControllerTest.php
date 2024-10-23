<?php

namespace Tests\Unit\Controllers;

use App\Controllers\InternetPlanController;
use App\Models\InternetPlan;
use PHPUnit\Framework\TestCase;

class InternetPlanControllerTest extends TestCase
{
    private $internetPlan;
    private $controller;

    protected function setUp(): void
    {
        $this->internetPlan = $this->createMock(InternetPlan::class);
        $this->controller = new InternetPlanController($this->internetPlan);
    }

    public function testIndex()
    {
        $expectedPlans = [
            ['id' => 1, 'name' => 'Test Plan 1'],
            ['id' => 2, 'name' => 'Test Plan 2'],
        ];

        $this->internetPlan->expects($this->once())
            ->method('getAllActive')
            ->willReturn($expectedPlans);

        $result = $this->controller->index([]);

        $this->assertEquals(['data' => $expectedPlans], $result);
    }

    public function testCreate()
    {
        $planData = [
            'name' => 'New Plan',
            'price' => 99.99,
            'type' => 'Fiber',
            'category' => 'Monthly'
        ];

        $this->internetPlan->expects($this->once())
            ->method('create')
            ->with($planData)
            ->willReturn(1);

        $result = $this->controller->create($planData);

        $this->assertEquals(
            ['message' => 'Plan created', 'id' => 1],
            $result
        );
    }
}