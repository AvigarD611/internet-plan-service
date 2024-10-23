<?php

namespace Lib\Models;

use PDO;

abstract class BaseModel implements Model
{
    protected $db;
    protected $table;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create(array $data): int
    {
        $this->beforeSave($data);

        // Wrap column names in backticks
        $columns = implode(', ', array_map(function($column) {
            return "`{$column}`";
        }, array_keys($data)));

        $values = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO `{$this->table}` ({$columns}) VALUES ({$values})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $this->beforeSave($data);

        // Wrap column names in backticks
        $set = [];
        foreach ($data as $column => $value) {
            $set[] = "`{$column}` = :{$column}";
        }
        $set = implode(', ', $set);

        $sql = "UPDATE `{$this->table}` SET {$set} WHERE `id` = :id";
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM `{$this->table}` WHERE `id` = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `id` = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $this->afterFetch($result);
        }

        return $result ?: null;
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM `{$this->table}`";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as &$result) {
            $this->afterFetch($result);
        }

        return $results;
    }

    protected function beforeSave(array &$data): void
    {
        // Override in child classes if needed
    }

    protected function afterFetch(array &$data): void
    {
        // Override in child classes if needed
    }
}