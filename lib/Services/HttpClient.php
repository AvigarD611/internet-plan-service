<?php

namespace Lib\Services;

interface HttpClient
{
    public function get(string $url, array $options = []): array;
    public function post(string $url, array $data, array $options = []): array;
}