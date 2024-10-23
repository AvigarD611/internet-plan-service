<?php

namespace App\Models;

use Lib\Models\BaseModel;

class InternetPlan extends BaseModel
{
    protected $table = 'internet_plans';

    protected $fillable = [
        'guid', 'name', 'status', 'price', 'type', 'category', 'tags'
    ];

    public function getAllActive(array $filters = [], int $page = 1, int $perPage = 10): array
    {
        $params = [];
        $conditions = ['`status` = :status'];
        $params['status'] = 'Active';

        // Add name filter if provided
        if (!empty($filters['name'])) {
            $conditions[] = '`name` LIKE :name';
            $params['name'] = '%' . $filters['name'] . '%';
        }

        // Add category filter if provided
        if (!empty($filters['category'])) {
            $conditions[] = '`category` = :category';
            $params['category'] = $filters['category'];
        }

        // Build the base query
        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM `{$this->table}`";
        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        // Add pagination with named parameters
        $offset = ($page - 1) * $perPage;
        $sql .= " LIMIT :offset, :per_page";
        $params['offset'] = $offset;
        $params['per_page'] = $perPage;

        // Execute the query
        $stmt = $this->db->prepare($sql);

        // Bind all parameters with their types
        foreach ($params as $key => $value) {
            if ($key === 'offset' || $key === 'per_page') {
                $stmt->bindValue($key, $value, \PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value, \PDO::PARAM_STR);
            }
        }

        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Get total count for pagination
        $totalStmt = $this->db->query("SELECT FOUND_ROWS()");
        $total = $totalStmt->fetchColumn();

        foreach ($results as &$result) {
            $this->afterFetch($result);
        }

        return [
            'data' => $results,
            'pagination' => [
                'total' => (int)$total,
                'per_page' => $perPage,
                'current_page' => $page,
                'total_pages' => ceil($total / $perPage)
            ]
        ];
    }

    public function findByGuid(string $guid): ?array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `guid` = :guid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['guid' => $guid]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result) {
            $this->afterFetch($result);
        }

        return $result ?: null;
    }

    public function setInactive(string $guid): bool
    {
        $sql = "UPDATE `{$this->table}` SET `status` = 'Inactive' WHERE `guid` = :guid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['guid' => $guid]);
    }

    public function getStatusStats(): array
    {
        $sql = "SELECT 
                    status, 
                    COUNT(*) as count 
                FROM `{$this->table}` 
                GROUP BY status";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Initialize counts
        $stats = [
            'active' => 0,
            'inactive' => 0
        ];

        // Process results
        foreach ($results as $row) {
            $status = strtolower($row['status']);
            $stats[$status] = (int)$row['count'];
        }

        return $stats;
    }

    protected function beforeSave(array &$data): void
    {
        if (isset($data['tags']) && is_array($data['tags'])) {
            $data['tags'] = json_encode($data['tags']);
        }
    }

    protected function afterFetch(array &$data): void
    {
        if (isset($data['tags']) && is_string($data['tags'])) {
            $data['tags'] = json_decode($data['tags'], true);
        }
    }
}