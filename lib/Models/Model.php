<?php

namespace Lib\Models;

interface Model
{
    public function create(array $data): int;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function find(int $id): ?array;
    public function findAll(): array;
}