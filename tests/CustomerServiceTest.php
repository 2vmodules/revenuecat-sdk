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
use Twovmodules\RevenueCat\Dto\Request\CreateCustomerAttribute;
use Twovmodules\RevenueCat\Dto\Response\Customer;
use Twovmodules\RevenueCat\Dto\Response\CustomerAttribute;
use Twovmodules\RevenueCat\Dto\Response\Subscription;
use Twovmodules\RevenueCat\Enum\AutoRenewalStatus;
use Twovmodules\RevenueCat\Http\HttpClient;
use Twovmodules\RevenueCat\Services\CustomerService;

class CustomerServiceTest extends TestCase
{
    public function testGetCustomersList(): void
    {
        $data = [
            'items' => [
                [
                    'id' => 'customer1',
                    'project_id' => 'proj1',
                    'first_seen_at' => time(),
                ],
                [
                    'id' => 'customer2',
                    'project_id' => 'proj1',
                    'first_seen_at' => time(),
                ],
            ],
            'next_page' => 'nextPageToken',
            'url' => '',
        ];

        $customerService = $this->getService($data);
        $paginator = $customerService->list('proj1');

        $this->assertInstanceOf(Paginator::class, $paginator);
        $this->assertInstanceOf(Customer::class, $paginator->items[0]);
        $this->assertCount(2, $paginator->items);
        $this->assertSame('nextPageToken', $paginator->nextPage);
    }

    public function testCreateCustomer(): void
    {
        $data = [
            'id' => 'customer1',
            'project_id' => 'proj1',
            'first_seen_at' => time(),
        ];

        $customerService = $this->getService($data);

        $customer = $customerService->create('customer1', null, 'proj1');

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertSame('customer1', $customer->id);
    }

    public function testGetCustomerById(): void
    {
        $data = [
            'id' => 'customer1',
            'project_id' => 'proj1',
            'first_seen_at' => time(),
        ];

        $customerService = $this->getService($data);
        $result = $customerService->get('customer1', 'proj1');

        $this->assertInstanceOf(Customer::class, $result);
        $this->assertSame('customer1', $result->id);
    }

    public function testDeleteCustomer(): void
    {
        $data = [];

        $customerService = $this->getService($data);

        $this->expectNotToPerformAssertions();
        $customerService->delete('customer1', 'proj1');
    }

    public function testGetSubscriptions(): void
    {
        $data = [
            'items' => [
                [
                    'id' => 'subscription1',
                    'customer_id' => 'customer1',
                    'original_customer_id' => 'customer1',
                    'product_id' => 'product1',
                    'starts_at' => time(),
                    'current_period_starts_at' => time(),
                    'current_period_ends_at' => time() + 3600,
                    'gives_access' => true,
                    'pending_payment' => false,
                    'entitlements' => [
                        'items' => [
                            [
                                'id' => 'ent1',
                                'lookup_key' => 'key1',
                                'display_name' => 'Entitlement 1',
                                'created_at' => time(),
                                'project_id' => 'proj1',
                            ],
                        ],
                    ],
                    'total_revenue_in_usd' => [
                        'amount' => 0,
                    ],
                    'auto_renewal_status' => AutoRenewalStatus::WILL_NOT_RENEW->value,
                    'status' => 'active',
                    'store' => 'app_store',
                    'store_subscription_identifier' => 'subscription1',
                    'ownership' => 'purchased',
                    'country' => 'US',
                ],
            ],
            'next_page' => null,
            'url' => null,
        ];

        $customerService = $this->getService($data);
        $paginator = $customerService->getSubscriptions('customer1', 'proj1');

        $this->assertInstanceOf(Paginator::class, $paginator);
        $this->assertInstanceOf(Subscription::class, $paginator->items[0]);
    }

    public function testGetAttributes(): void
    {
        $data = [
            'items' => [
                [
                    'name' => 'test',
                    'value' => '123',
                    'updated_at' => time(),
                ],
            ],
            'next_page' => null,
            'url' => null,
        ];

        $customerService = $this->getService($data);
        $paginator = $customerService->getAttributes('customer1', 'proj1');

        $this->assertInstanceOf(Paginator::class, $paginator);
        $this->assertInstanceOf(CustomerAttribute::class, $paginator->items[0]);
    }

    public function testCreateAttributes(): void
    {
        $data = [
            'items' => [
                [
                    'name' => 'test',
                    'updated_at' => time(),
                    'value' => 'value1',
                ],
                [
                    'name' => 'email',
                    'updated_at' => time(),
                    'value' => 'email@test.com',
                ],
            ],
            'next_page' => null,
            'url' => null,
        ];

        $customerService = $this->getService($data);
        $requestData = [
            new CreateCustomerAttribute('name', 'value1'),
            new CreateCustomerAttribute('email', 'email@test.com'),
        ];

        $paginator = $customerService->createAttributes($requestData, 'customer1', 'proj1');

        $this->assertInstanceOf(Paginator::class, $paginator);
        $this->assertInstanceOf(CustomerAttribute::class, $paginator->items[0]);
        $this->assertSame('value1', $paginator->items[0]->value);
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

    private function getService(array $data): CustomerService
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

        return new CustomerService(
            $httpClient,
            $configuration,
            new HttpFactory(),
            new HttpFactory(),
            new NullLogger()
        );
    }
}
