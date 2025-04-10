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
use Twovmodules\RevenueCat\Dto\Request\CreateEntitlement;
use Twovmodules\RevenueCat\Dto\Request\UpdateEntitlement;
use Twovmodules\RevenueCat\Dto\Response\Entitlement;
use Twovmodules\RevenueCat\Dto\Response\Product;
use Twovmodules\RevenueCat\Http\HttpClient;
use Twovmodules\RevenueCat\Services\EntitlementService;

class EntitlementServiceTest extends TestCase
{
    public function testGetEntitlementsPaginated(): void
    {
        $data = [
            'items' => [
                [
                    'id' => 'ent1',
                    'lookup_key' => 'key1',
                    'display_name' => 'Entitlement 1',
                    'created_at' => time(),
                    'project_id' => 'project1',
                ],
                [
                    'id' => 'ent2',
                    'lookup_key' => 'key2',
                    'display_name' => 'Entitlement 2',
                    'created_at' => time(),
                    'project_id' => 'project1',
                ],
            ],
            'next_page' => 'nextPageToken',
            'url' => null,
        ];

        $entitlementService = $this->getService($data);
        $paginator = $entitlementService->list('proj1');

        $this->assertInstanceOf(Paginator::class, $paginator);
        $this->assertInstanceOf(Entitlement::class, $paginator->items[0]);
        $this->assertCount(2, $paginator->items);
        $this->assertSame('nextPageToken', $paginator->nextPage);
    }

    public function testGetEntitlementById(): void
    {
        $data = [
            'id' => 'ent1',
            'lookup_key' => 'key1',
            'display_name' => 'Entitlement 1',
            'created_at' => time(),
            'project_id' => 'project1',
        ];

        $entitlementService = $this->getService($data);
        $result = $entitlementService->get('ent1', 'proj1');

        $this->assertInstanceOf(Entitlement::class, $result);
        $this->assertSame('ent1', $result->id);
    }

    public function testCreateEntitlement(): void
    {
        $data = [
            'id' => 'ent1',
            'lookup_key' => 'key1',
            'display_name' => 'Entitlement 1',
            'created_at' => time(),
            'project_id' => 'project1',
        ];

        $entitlementService = $this->getService($data);
        $createEntitlement = new CreateEntitlement('key1', 'Entitlement 1');

        $entitlement = $entitlementService->create($createEntitlement, 'proj1');

        $this->assertInstanceOf(Entitlement::class, $entitlement);
        $this->assertSame('Entitlement 1', $entitlement->displayName);
    }

    public function testUpdateEntitlement(): void
    {
        $data = [
            'id' => 'ent1',
            'lookup_key' => 'key1',
            'display_name' => 'Updated Entitlement',
            'created_at' => time(),
            'project_id' => 'project1',
        ];

        $entitlementService = $this->getService($data);
        $updateEntitlement = new UpdateEntitlement('Updated Entitlement');

        $entitlement = $entitlementService->update('ent1', $updateEntitlement, 'proj1');

        $this->assertInstanceOf(Entitlement::class, $entitlement);
        $this->assertSame('Updated Entitlement', $entitlement->displayName);
    }

    public function testDeleteEntitlement(): void
    {
        $data = [];

        $entitlementService = $this->getService($data);
        $this->expectNotToPerformAssertions();
        $entitlementService->delete('ent1', 'proj1');
    }

    public function testGetProductsForEntitlement(): void
    {
        $data = [
            'items' => [
                [
                    'id' => 'prod1',
                    'display_name' => 'Product 1',
                    'store_identifier' => 'prod1',
                    'type' => 'one_time',
                    'created_at' => time(),
                    'app_id' => 'app1',
                ],
            ],
            'next_page' => 'next_page_token',
            'url' => null,
        ];

        $entitlementService = $this->getService($data);
        $paginator = $entitlementService->getProducts('ent1', 'proj1');

        $this->assertInstanceOf(Paginator::class, $paginator);
        $this->assertInstanceOf(Product::class, $paginator->items[0]);
    }

    public function testAttachProductToEntitlement(): void
    {
        $data = [
            'id' => 'ent1',
            'lookup_key' => 'key1',
            'display_name' => 'Entitlement 1',
            'created_at' => time(),
            'project_id' => 'project1',
        ];

        $entitlementService = $this->getService($data);
        $entitlement = $entitlementService->attachProduct('ent1', ['prod1'], 'proj1');

        $this->assertInstanceOf(Entitlement::class, $entitlement);
    }

    public function testDetachProductFromEntitlement(): void
    {
        $data = [
            'id' => 'ent1',
            'lookup_key' => 'key1',
            'display_name' => 'Entitlement 1',
            'created_at' => time(),
            'project_id' => 'project1',
        ];

        $entitlementService = $this->getService($data);
        $entitlement = $entitlementService->detachProduct('ent1', ['prod1'], 'proj1');

        $this->assertInstanceOf(Entitlement::class, $entitlement);
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

    private function getService(array $data): EntitlementService
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

        return new EntitlementService(
            $httpClient,
            $configuration,
            new HttpFactory(),
            new HttpFactory(),
            new NullLogger()
        );
    }
}
