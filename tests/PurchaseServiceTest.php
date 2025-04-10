<?php

namespace Twovmodules\RevenueCat\Tests;

use CuyZ\Valinor\Mapper\TypeTreeMapperError;
use GuzzleHttp\Psr7\HttpFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\NullLogger;
use Twovmodules\RevenueCat\Configuration;
use Twovmodules\RevenueCat\Dto\Response\Purchase;
use Twovmodules\RevenueCat\Http\HttpClient;
use Twovmodules\RevenueCat\Services\PurchaseService;

class PurchaseServiceTest extends TestCase
{
    public function testGetPurchase(): void
    {
        $data = [
            'id' => 'purchase1',
            'customer_id' => 'customer1',
            'original_customer_id' => 'customer1',
            'product_id' => 'product1',
            'purchased_at' => time(),
            'revenue_in_usd' => [
                'monthly' => 0.99,
                'yearly' => 9.99,
            ],
            'quantity' => 1,
            'status' => 'active',
            'entitlements' => [
                'items' => [
                    [
                        'id' => 'entitlement_1',
                        'lookup_key' => 'premium',
                        'display_name' => 'Premium',
                        'created_at' => time(),
                        'project_id' => 'project1',
                    ],
                ],
            ],
            'environment' => 'production',
            'store' => 'app_store',
            'store_purchase_identifier' => 'test',
            'ownership' => 'purchased',
            'country' => 'US',
            'presented_offering_id' => 'offering_1',
        ];

        $purchaseService = $this->getService($data);

        $purchase = $purchaseService->get('purchase1', 'proj1');

        $this->assertInstanceOf(Purchase::class, $purchase);
        $this->assertSame('purchase1', $purchase->id);
    }

    public function testGetPurchaseWithInvalidArgumentException(): void
    {
        $this->expectException(TypeTreeMapperError::class);

        $data = [
            'id' => 'purchase1',
            'store_purchase_identifier' => '',
        ];

        Purchase::fromArray($data);
    }

    public function testRefundPurchase(): void
    {
        $data = [
            'id' => 'purchase1',
            'customer_id' => 'customer1',
            'original_customer_id' => 'customer1',
            'product_id' => 'product1',
            'purchased_at' => time(),
            'revenue_in_usd' => [
                'monthly' => 0.99,
                'yearly' => 9.99,
            ],
            'quantity' => 1,
            'status' => 'refunded',
            'entitlements' => [
                'items' => [
                    [
                        'id' => 'entitlement_1',
                        'lookup_key' => 'premium',
                        'display_name' => 'Premium',
                        'created_at' => time(),
                        'project_id' => 'project1',
                    ],
                ],
            ],
            'environment' => 'production',
            'store' => 'app_store',
            'store_purchase_identifier' => 'test',
            'ownership' => 'purchased',
            'country' => 'US',
            'presented_offering_id' => 'offering_1',
        ];

        $purchaseService = $this->getService($data);

        $purchase = $purchaseService->refund('purchase1', 'proj1');

        $this->assertInstanceOf(Purchase::class, $purchase);
        $this->assertSame('refunded', $purchase->status);
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

    private function getService(array $data): PurchaseService
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

        return new PurchaseService(
            $httpClient,
            $configuration,
            new HttpFactory(),
            new HttpFactory(),
            new NullLogger()
        );
    }
}
