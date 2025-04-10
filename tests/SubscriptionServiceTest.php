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
use Twovmodules\RevenueCat\Dto\Response\Entitlement;
use Twovmodules\RevenueCat\Dto\Response\Subscription;
use Twovmodules\RevenueCat\Enum\AutoRenewalStatus;
use Twovmodules\RevenueCat\Enum\SubscriptionStatus;
use Twovmodules\RevenueCat\Http\HttpClient;
use Twovmodules\RevenueCat\Services\SubscriptionService;

class SubscriptionServiceTest extends TestCase
{
    public function testListEntitlements(): void
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
            'url' => 'url',
        ];

        $subscriptionService = $this->getService($data);
        $paginator = $subscriptionService->list('subscription1', 'prog1', 10, 'nextPageToken');

        $this->assertInstanceOf(Paginator::class, $paginator);
        $this->assertInstanceOf(Entitlement::class, $paginator->items[0]);
        $this->assertCount(2, $paginator->items);
        $this->assertSame('nextPageToken', $paginator->nextPage);
    }

    public function testGetSubscription(): void
    {
        $data = [
            'id' => 'subscription1',
            'customer_id' => 'customer1',
            'original_customer_id' => 'customer1',
            'product_id' => 'product1',
            'starts_at' => time(),
            'current_period_starts_at' => time(),
            'current_period_ends_at' => time() + 3600,
            'gives_access' => true,
            'pending_payment' => false,
            'auto_renewal_status' => AutoRenewalStatus::WILL_NOT_RENEW->value,
            'status' => 'active',
            'total_revenue_in_usd' => [
                'usd' => 0.1,
            ],
            'presented_offering_id' => 'offering_1',
            'store' => 'app_store',
            'store_subscription_identifier' => 'sub123',
            'ownership' => 'purchased',
            'entitlements' => [],
            'pending_changes' => null,
            'country' => 'US',
            'management_url' => 'https://example.com/manage',
        ];

        $subscriptionService = $this->getService($data);

        $subscription = $subscriptionService->get('subscription1', 'proj1');

        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertSame('subscription1', $subscription->id);
    }

    public function testCancelSubscription(): void
    {
        $data = [
            'id' => 'subscription1',
            'customer_id' => 'customer1',
            'original_customer_id' => 'customer1',
            'product_id' => 'product1',
            'starts_at' => time(),
            'current_period_starts_at' => time(),
            'current_period_ends_at' => time() + 3600,
            'gives_access' => true,
            'pending_payment' => false,
            'auto_renewal_status' => AutoRenewalStatus::WILL_NOT_RENEW->value,
            'status' => SubscriptionStatus::PAUSED->value,
            'total_revenue_in_usd' => [
                'usd' => 0.1,
            ],
            'presented_offering_id' => 'offering_1',
            'store' => 'app_store',
            'store_subscription_identifier' => 'sub123',
            'ownership' => 'purchased',
            'entitlements' => [],
            'pending_changes' => null,
            'country' => 'US',
            'management_url' => 'https://example.com/manage',
        ];

        $subscriptionService = $this->getService($data);

        $subscription = $subscriptionService->refund('subscription1', 'proj1');

        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertEquals('paused', $subscription->status->value);
    }

    public function testRefundSubscription(): void
    {
        $data = [
            'id' => 'subscription1',
            'customer_id' => 'customer1',
            'original_customer_id' => 'customer1',
            'product_id' => 'product1',
            'starts_at' => time(),
            'current_period_starts_at' => time(),
            'current_period_ends_at' => time() + 3600,
            'gives_access' => true,
            'pending_payment' => false,
            'auto_renewal_status' => AutoRenewalStatus::WILL_NOT_RENEW->value,
            'status' => SubscriptionStatus::TRIALING->value,
            'total_revenue_in_usd' => [
                'usd' => 0.1,
            ],
            'presented_offering_id' => 'offering_1',
            'store' => 'app_store',
            'store_subscription_identifier' => 'sub123',
            'ownership' => 'purchased',
            'entitlements' => [],
            'pending_changes' => null,
            'country' => 'US',
            'management_url' => 'https://example.com/manage',
        ];

        $subscriptionService = $this->getService($data);

        $subscription = $subscriptionService->refund('subscription1', 'proj1');

        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertEquals('trialing', $subscription->status->value);
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

    private function getService(array $data): SubscriptionService
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

        return new SubscriptionService(
            $httpClient,
            $configuration,
            new HttpFactory(),
            new HttpFactory(),
            new NullLogger()
        );
    }
}
