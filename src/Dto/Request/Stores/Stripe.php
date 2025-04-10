<?php

namespace Twovmodules\RevenueCat\Dto\Request\Stores;

use Twovmodules\RevenueCat\Dto\BaseDto;

readonly class Stripe extends BaseDto
{
    public function __construct(
        public string $stripeAccountId
    ) {
    }
}
