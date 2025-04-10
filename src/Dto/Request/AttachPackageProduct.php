<?php

namespace Twovmodules\RevenueCat\Dto\Request;

use Twovmodules\RevenueCat\Dto\BaseDto;
use Twovmodules\RevenueCat\Enum\EligibilityCriteria;

readonly class AttachPackageProduct extends BaseDto
{
    public function __construct(
        public string $productId,
        public EligibilityCriteria $eligibilityCriteria
    ) {
    }
}
