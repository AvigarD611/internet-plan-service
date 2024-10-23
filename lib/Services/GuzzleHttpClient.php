<?php

namespace Lib\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GuzzleHttpClient implements HttpClient
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function get(string $url, array $options = []): array
    {
        try {
            $response = $this->client->get($url, $options);
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new \RuntimeException("HTTP GET request failed: " . $e->getMessage());
        }
    }

    public function post(string $url, array $data, array $options = []): array
    {
        $options['json'] = $data;
        try {
            $response = $this->client->post($url, $options);
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new \RuntimeException("HTTP POST request failed: " . $e->getMessage());
        }
    }
}