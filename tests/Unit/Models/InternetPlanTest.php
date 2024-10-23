<?php

namespace Tests\Unit\Models;

use App\Models\InternetPlan;
use PHPUnit\Framework\TestCase;
use PDO;

class InternetPlanTest extends TestCase
{
    private $pdo;
    private $internetPlan;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->internetPlan = new InternetPlan($this->pdo);
    }

    public function testFindByGuid()
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('fetch')->willReturn(['id' => 1, 'guid' => 'test-guid']);

        $this->pdo->method('prepare')->willReturn($stmt);

        $result = $this->internetPlan->findByGuid('test-guid');
        $this->assertEquals(['id' => 1, 'guid' => 'test-guid'], $result);
    }

    // Add more tests for getAllActive, setInactive, etc.
}