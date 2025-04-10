<?php

namespace Twovmodules\RevenueCat\Tests;

use GuzzleHttp\Psr7\HttpFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\NullLogger;
use Twovmodules\RevenueCat\Configuration;
use Twovmodules\RevenueCat\Dto\Paginator;
use Twovmodules\RevenueCat\Dto\Request\CreateApp;
use Twovmodules\RevenueCat\Dto\Request\UpdateApp;
use Twovmodules\RevenueCat\Dto\Response\App;
use Twovmodules\RevenueCat\Enum\AppType;
use Twovmodules\RevenueCat\Http\HttpClient;
use Twovmodules\RevenueCat\Services\AppService;

class AppServiceTest extends TestCase
{
    public function testGetAppsPaginated(): void
    {
        $data = [
            'items' => [
                [
                    'id' => 'app1',
                    'name' => 'App 1',
                    'project_id' => 'proj1',
                    'type' => 'app_store',
                    'created_at' => time(),
                ],
                [
                    'id' => 'app2',
                    'name' => 'App 2',
                    'project_id' => 'proj1',
                    'type' => 'play_store',
                    'created_at' => time(),
                ],
            ],
            'next_page' => 'nextPageToken',
            'url' => 'https://api.example.com/projects/test_project_id/apps',
        ];

        $appService = $this->getService($data);
        $paginator = $appService->list('proj1');

        $this->assertInstanceOf(Paginator::class, $paginator);
        $this->assertInstanceOf(App::class, $paginator->items[0]);
        $this->assertCount(2, $paginator->items);
        $this->assertSame('nextPageToken', $paginator->nextPage);
    }

    public function testCreateApp(): void
    {
        $data = [
            'id' => 'app1',
            'name' => 'App 1',
            'project_id' => 'proj1',
            'type' => 'app_store',
            'created_at' => time(),
        ];

        $appService = $this->getService($data);
        $createApp = new CreateApp(name: 'App 1', type: AppType::PLAY_STORE);

        $app = $appService->create($createApp, 'proj1');

        $this->assertInstanceOf(App::class, $app);
        $this->assertSame('App 1', $app->name);
    }

    public function testGetAppById(): void
    {
        $data = [
            'id' => 'app1',
            'name' => 'App 1',
            'project_id' => 'proj1',
            'type' => 'app_store',
            'created_at' => time(),
        ];

        $appService = $this->getService($data);

        $app = $appService->get('app1', 'proj1');

        $this->assertInstanceOf(App::class, $app);
        $this->assertSame('app1', $app->id);
    }

    public function testUpdateApp(): void
    {
        $data = [
            'id' => 'app1',
            'name' => 'Updated App',
            'project_id' => 'proj1',
            'type' => 'app_store',
            'created_at' => time(),
        ];

        $appService = $this->getService($data);

        $app = $appService->update('app1', UpdateApp::fromArray([
                'name' => 'Updated App',
            ]), 'proj1');

        $this->assertInstanceOf(App::class, $app);
        $this->assertSame('Updated App', $app->name);
    }

    public function testDeleteApp(): void
    {
        $data = [];

        $appService = $this->getService($data);

        $this->expectNotToPerformAssertions();
        $appService->delete('app1', 'proj1');
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

    private function getService(array $data): AppService
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

        return new AppService($httpClient, $configuration, new HttpFactory(), new HttpFactory(), new NullLogger());
    }
}
