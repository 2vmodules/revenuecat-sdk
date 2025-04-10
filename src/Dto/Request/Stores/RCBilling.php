<?php

namespace Twovmodules\RevenueCat\Dto\Request\Stores;

use Twovmodules\RevenueCat\Dto\BaseDto;
use Twovmodules\RevenueCat\Enum\DefaultCurrency;

readonly class RCBilling extends BaseDto
{
    public function __construct(
        public DefaultCurrency $defaultCurrency,
        public string $appName,
        public ?string $stripeAccountId,
        public ?string $supportEmail,
    ) {
    }
}
