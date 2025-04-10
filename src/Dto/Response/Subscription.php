<?php

namespace Twovmodules\RevenueCat\Dto\Response;

use Twovmodules\RevenueCat\Dto\BaseDto;
use Twovmodules\RevenueCat\Enum\AutoRenewalStatus;
use Twovmodules\RevenueCat\Enum\OwnershipType;
use Twovmodules\RevenueCat\Enum\StoreType;
use Twovmodules\RevenueCat\Enum\SubscriptionStatus;

readonly class Subscription extends BaseDto
{
    public function __construct(
        public string $id,
        public string $customerId,
        public string $originalCustomerId,
        public ?string $productId,
        public int $startsAt,
        public int $currentPeriodStartsAt,
        public ?int $currentPeriodEndsAt,
        public bool $givesAccess,
        public bool $pendingPayment,
        public AutoRenewalStatus $autoRenewalStatus,
        public SubscriptionStatus $status,
        public array $totalRevenueInUsd,
        public ?string $presentedOfferingId,
        public StoreType $store,
        public string $storeSubscriptionIdentifier,
        public OwnershipType $ownership,
        /** @var array{items: Entitlement[]} $entitlements */
        public array $entitlements,
        public ?array $pendingChanges,
        public ?string $country,
        public ?string $managementUrl
    ) {
    }
}
