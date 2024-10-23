<?php

namespace Tests\Unit\Services;

use Lib\Services\GuzzleHttpClient;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class GuzzleHttpClientTest extends TestCase
{
    public function testGet()
    {
        $guzzleClient = $this->createMock(Client::class);
        $guzzleClient->method('get')
            ->willReturn(new Response(200, [], '{"key": "value"}'));

        $client = new GuzzleHttpClient($guzzleClient);
        $result = $client->get('http://example.com');

        $this->assertEquals(['key' => 'value'], $result);
    }

    // Add a test for the post method as well
}