<?php

namespace Twovmodules\RevenueCat\Dto\Response;

use Twovmodules\RevenueCat\Dto\BaseDto;
use Twovmodules\RevenueCat\Enum\EnvironmentType;
use Twovmodules\RevenueCat\Enum\OwnershipType;
use Twovmodules\RevenueCat\Enum\StoreType;

readonly class Purchase extends BaseDto
{
    public function __construct(
        public string $id,
        public string $customerId,
        public string $originalCustomerId,
        public string $productId,
        public int $purchasedAt,
        public array $revenueInUsd,
        public int $quantity,
        public string $status,
        /** @var array{items: Entitlement[]}|null $products */
        public ?array $entitlements,
        public EnvironmentType $environment,
        public StoreType $store,
        public string $storePurchaseIdentifier,
        public ?OwnershipType $ownership = null,
        public ?string $country = null,
        public ?string $presentedOfferingId = null,
    ) {
    }
}
