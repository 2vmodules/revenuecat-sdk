<?php

namespace Twovmodules\RevenueCat\Tests;

use GuzzleHttp\Psr7\HttpFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\NullLogger;
use Twovmodules\RevenueCat\Configuration;
use Twovmodules\RevenueCat\Dto\Response\OverviewMetrics;
use Twovmodules\RevenueCat\Http\HttpClient;
use Twovmodules\RevenueCat\Services\OverviewMetricsService;

class OverviewMetricsServiceTest extends TestCase
{
    public function testGetOverviewMetrics(): void
    {
        $data = [
            'metrics' => [
                [
                    'id' => 'metric1',
                    'name' => 'Metric 1',
                    'description' => 'Description 1',
                    'unit' => '#',
                    'period' => 'P28D',
                    'value' => 100,
                ],
                [
                    'id' => 'metric2',
                    'name' => 'Metric 2',
                    'description' => 'Description 2',
                    'unit' => '#',
                    'period' => 'P28D',
                    'value' => 200,
                ],
            ],
        ];

        $overviewMetricsService = $this->getService($data);

        $result = $overviewMetricsService->get('test_project_id');

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(OverviewMetrics::class, $result[0]);
        $this->assertInstanceOf(OverviewMetrics::class, $result[1]);
    }

    private function createMockClient(ResponseInterface $response): ClientInterface
    {
        $mockClient = $this->createMock(ClientInterface::class);
        $mockClient->method('sendRequest')
            ->willReturn($response);

        return $mockClient;
    }

    private function createMockStream(string $content): StreamInterface
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')
            ->willReturn($content);

        return $stream;
    }

    private function getService(array $data): OverviewMetricsService
    {
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getBody')
            ->willReturn($this->createMockStream(json_encode($data, JSON_THROW_ON_ERROR)));

        $configuration = $this->createMock(Configuration::class);
        $configuration->method('getBaseUrl')
            ->willReturn('https://api.example.com');
        $configuration->method('getApiKey')
            ->willReturn('test_api_key');

        $httpClient = new HttpClient($this->createMockClient($mockResponse), $configuration, new NullLogger());

        return new OverviewMetricsService(
            $httpClient,
            $configuration,
            new HttpFactory(),
            new HttpFactory(),
            new NullLogger()
        );
    }
}
