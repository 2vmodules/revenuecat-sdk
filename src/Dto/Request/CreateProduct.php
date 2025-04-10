<?php

namespace Twovmodules\RevenueCat\Dto\Request;

use Twovmodules\RevenueCat\Dto\BaseDto;
use Twovmodules\RevenueCat\Enum\ProductType;

readonly class CreateProduct extends BaseDto
{
    public function __construct(
        public string $storeIdentifier,
        public string $appId,
        public ProductType $type,
        public ?string $displayName = null
    ) {
    }
}
