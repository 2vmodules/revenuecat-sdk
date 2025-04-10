<?php

namespace Twovmodules\RevenueCat\Tests;

use CuyZ\Valinor\Mapper\TypeTreeMapperError;
use PHPUnit\Framework\TestCase;
use Twovmodules\RevenueCat\Dto\Response\Entitlement;
use Twovmodules\RevenueCat\Dto\Response\Product;
use Twovmodules\RevenueCat\Dto\Response\Project;
use Twovmodules\RevenueCat\Dto\Response\Subscription;
use Twovmodules\RevenueCat\Enum\AppType;
use Twovmodules\RevenueCat\Enum\AutoRenewalStatus;
use Twovmodules\RevenueCat\Enum\OwnershipType;
use Twovmodules\RevenueCat\Enum\ProductType;
use Twovmodules\RevenueCat\Enum\StoreType;
use Twovmodules\RevenueCat\Enum\SubscriptionStatus;

class DtoTest extends TestCase
{
    public function testProjectDtoCreation(): void
    {
        $project = new Project(id: 'proj123', name: 'Test Project', createdAt: time());

        $this->assertSame('proj123', $project->id);
        $this->assertSame('Test Project', $project->name);
    }

    public function testProjectDtoToArray(): void
    {
        $project = new Project(id: 'proj123', name: 'Test Project', createdAt: time());

        $array = $project->toArray();
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('created_at', $array);
    }

    public function testDtoFromArray(): void
    {
        $subscriptionData = $this->getSubscriptionData();
        $subscription = Subscription::fromArray($subscriptionData);

        $this->assertInstanceOf(Subscription::class, $subscription);
    }

    public function testProjectDtoFromArrayWithRequiredArgumentException(): void
    {
        $this->expectException(TypeTreeMapperError::class);
        $subscriptionData = $this->getSubscriptionData();

        unset($subscriptionData['gives_access']);

        Subscription::fromArray($subscriptionData);
    }

    public function testProjectDtoFromArrayWithInvalidArgumentException(): void
    {
        $this->expectException(TypeTreeMapperError::class);
        $subscriptionData = $this->getSubscriptionData();

        $subscriptionData['entitlements'] = false;

        Subscription::fromArray($subscriptionData);
    }

    private function getSubscriptionData(): array
    {
        $entitlement = Entitlement::fromArray([
            'id' => 'ent1',
            'lookup_key' => 'key1',
            'display_name' => 'Entitlement 1',
            'created_at' => time(),
            'project_id' => 'proj1',
            'products' => [
                Product::fromArray([
                    'id' => 'prod1',
                    'store_identifier' => 'prod1',
                    'type' => ProductType::ONE_TIME->value,
                    'created_at' => time(),
                    'app_id' => 'app1',
                    'display_name' => 'Product 1',
                    'subscription' => [
                        'duration' => 'P1M',
                        'grace_period_duration' => 'P0D',
                        'trial_duration' => 'P0D',
                    ],
                    'app' => [
                        'id' => 'app1',
                        'name' => 'My App',
                        'type' => AppType::APP_STORE->value,
                        'created_at' => time(),
                        'project_id' => 'proj1',
                    ],
                ]),
            ],
        ]);

        return [
            'id' => 'sub1',
            'customer_id' => 'customer1',
            'original_customer_id' => 'customer1',
            'product_id' => 'product1',
            'starts_at' => time(),
            'current_period_starts_at' => time(),
            'current_period_ends_at' => time(),
            'gives_access' => true,
            'pending_payment' => true,
            'auto_renewal_status' => AutoRenewalStatus::HAS_ALREADY_RENEWED->value,
            'status' => SubscriptionStatus::ACTIVE->value,
            'total_revenue_in_usd' => [
                'monthly' => 0.99,
                'yearly' => 9.99,
            ],
            'presented_offering_id' => 'offering_1',
            'store' => StoreType::APP_STORE->value,
            'store_subscription_identifier' => 'test',
            'ownership' => OwnershipType::PURCHASED->value,
            'entitlements' => [$entitlement],
            'pending_changes' => null,
            'country' => 'US',
            'management_url' => null,
        ];
    }
}
