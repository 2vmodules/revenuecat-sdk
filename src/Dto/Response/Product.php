<?php

namespace Twovmodules\RevenueCat\Dto\Response;

use Twovmodules\RevenueCat\Dto\BaseDto;
use Twovmodules\RevenueCat\Enum\ProductType;

readonly class Product extends BaseDto
{
    public function __construct(
        public string $id,
        public string $storeIdentifier,
        public ProductType $type,
        public int $createdAt,
        public string $appId,
        public ?string $displayName = null,
        public ?SubscriptionDetails $subscription = null,
        public ?App $app = null
    ) {
    }
}
