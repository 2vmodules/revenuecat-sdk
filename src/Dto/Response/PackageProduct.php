<?php

namespace Twovmodules\RevenueCat\Dto\Response;

use Twovmodules\RevenueCat\Dto\BaseDto;
use Twovmodules\RevenueCat\Enum\EligibilityCriteria;

readonly class PackageProduct extends BaseDto
{
    public function __construct(
        public Product $product,
        public EligibilityCriteria $eligibilityCriteria
    ) {
    }
}
