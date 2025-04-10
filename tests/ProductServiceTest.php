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
use Twovmodules\RevenueCat\Dto\Request\CreateProduct;
use Twovmodules\RevenueCat\Dto\Response\Product;
use Twovmodules\RevenueCat\Http\HttpClient;
use Twovmodules\RevenueCat\Services\ProductService;

class ProductServiceTest extends TestCase
{
    public function testGetProductsList(): void
    {
        $data = [
            'items' => [
                [
                    'id' => 'prod1',
                    'store_identifier' => 'prod1',
                    'type' => 'one_time',
                    'created_at' => time(),
                    'app_id' => 'test_app_id',
                    'display_name' => 'Product 1',
                ],
                [
                    'id' => 'prod2',
                    'store_identifier' => 'prod2',
                    'type' => 'subscription',
                    'created_at' => time(),
                    'app_id' => 'test_app_id',
                    'display_name' => 'Product 2',
                ],
            ],
            'next_page' => 'nextPageToken',
            'url' => 'https://api.example.com/projects/test_project_id/products',
        ];

        $productService = $this->getService($data);
        $paginator = $productService->list('proj1');

        $this->assertInstanceOf(Paginator::class, $paginator);
        $this->assertInstanceOf(Product::class, $paginator->items[0]);
        $this->assertCount(2, $paginator->items);
        $this->assertSame('nextPageToken', $paginator->nextPage);
    }

    public function testCreateProduct(): void
    {
        $data = [
            'id' => 'prod1',
            'store_identifier' => 'prod1',
            'type' => 'one_time',
            'created_at' => time(),
            'app_id' => 'test_app_id',
            'display_name' => 'Product 1',
        ];

        $productService = $this->getService($data);
        $createProduct = CreateProduct::fromArray($data);

        $product = $productService->create($createProduct, 'proj1');

        $this->assertInstanceOf(Product::class, $product);
        $this->assertSame('Product 1', $product->displayName);
    }

    public function testGetProductById(): void
    {
        $data = [
            'id' => 'prod1',
            'store_identifier' => 'prod1',
            'type' => 'one_time',
            'created_at' => time(),
            'app_id' => 'test_app_id',
            'display_name' => 'Product 1',
        ];

        $productService = $this->getService($data);

        $product = $productService->get('prod1', 'proj1');

        $this->assertInstanceOf(Product::class, $product);
        $this->assertSame('prod1', $product->id);
    }

    public function testDeleteProduct(): void
    {
        $data = [];
        $productService = $this->getService($data);

        $this->expectNotToPerformAssertions();
        $productService->delete('prod1', 'proj1');
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

    private function getService(array $data): ProductService
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

        return new ProductService(
            $httpClient,
            $configuration,
            new HttpFactory(),
            new HttpFactory(),
            new NullLogger()
        );
    }
}
