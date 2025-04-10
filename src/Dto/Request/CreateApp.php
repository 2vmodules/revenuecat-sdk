<?php

namespace Twovmodules\RevenueCat\Dto\Request;

use Twovmodules\RevenueCat\Dto\BaseDto;
use Twovmodules\RevenueCat\Dto\Request\Stores\Amazon;
use Twovmodules\RevenueCat\Dto\Request\Stores\AppStore;
use Twovmodules\RevenueCat\Dto\Request\Stores\MacAppStore;
use Twovmodules\RevenueCat\Dto\Request\Stores\PlayStore;
use Twovmodules\RevenueCat\Dto\Request\Stores\RCBilling;
use Twovmodules\RevenueCat\Dto\Request\Stores\Roku;
use Twovmodules\RevenueCat\Dto\Request\Stores\Stripe;
use Twovmodules\RevenueCat\Enum\AppType;

readonly class CreateApp extends BaseDto
{
    public function __construct(
        public string $name,
        public AppType $type,
        public ?Amazon $amazon = null,
        public ?AppStore $appStore = null,
        public ?PlayStore $playStore = null,
        public ?MacAppStore $macAppStore = null,
        public ?Stripe $stripe = null,
        public ?RCBilling $rcBilling = null,
        public ?Roku $roku = null,
    ) {
    }
}
