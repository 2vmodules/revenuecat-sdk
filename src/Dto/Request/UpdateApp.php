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

readonly class UpdateApp extends BaseDto
{
    public function __construct(
        public string $name,
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
