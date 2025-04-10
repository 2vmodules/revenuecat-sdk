<?php

namespace Twovmodules\RevenueCat\Tests;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Log\NullLogger;
use Twovmodules\RevenueCat\Configuration;
use Twovmodules\RevenueCat\Exceptions\RateLimitException;
use Twovmodules\RevenueCat\Http\HttpClient;

class HttpClientTest extends TestCase
{
    public function testSuccessfulRequest(): void
    {
        $mockResponse = new Response(200, [], '{"data": "test"}');
        $configuration = $this->createMock(Configuration::class);
        $configuration->method('getBaseUrl')
            ->willReturn('https://api.example.com');
        $configuration->method('getApiKey')
            ->willReturn('test_api_key');

        $httpClient = new HttpClient($this->createMockClient($mockResponse), $configuration, new NullLogger());

        $response = $httpClient->send(new Request('GET', 'test'));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('{"data": "test"}', (string) $response->getBody());
    }

    public function testRateLimitException(): void
    {
        $this->expectException(RateLimitException::class);
        $this->expectExceptionCode(429);

        $mockResponse = new Response(429, [
            'Retry-After' => '30',
            'RevenueCat-Rate-Limit-Current-Usage' => '25',
            'RevenueCat-Rate-Limit-Current-Limit' => '30',
        ]);
        $configuration = $this->createMock(Configuration::class);
        $configuration->method('getBaseUrl')
            ->willReturn('https://api.example.com');
        $configuration->method('getApiKey')
            ->willReturn('test_api_key');

        $httpClient = new HttpClient($this->createMockClient($mockResponse), $configuration, new NullLogger());

        $httpClient->send(new Request('GET', 'test'));
    }

    private function createMockClient(Response $response): ClientInterface
    {
        $mockClient = $this->createMock(ClientInterface::class);
        $mockClient->method('sendRequest')
            ->willReturn($response);

        return $mockClient;
    }
}
