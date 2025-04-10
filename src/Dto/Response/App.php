<?php

namespace Twovmodules\RevenueCat\Dto\Response;

use Twovmodules\RevenueCat\Dto\BaseDto;
use Twovmodules\RevenueCat\Dto\Request\Stores\Amazon;
use Twovmodules\RevenueCat\Dto\Request\Stores\AppStore;
use Twovmodules\RevenueCat\Dto\Request\Stores\MacAppStore;
use Twovmodules\RevenueCat\Dto\Request\Stores\PlayStore;
use Twovmodules\RevenueCat\Dto\Request\Stores\RCBilling;
use Twovmodules\RevenueCat\Dto\Request\Stores\Roku;
use Twovmodules\RevenueCat\Dto\Request\Stores\Stripe;
use Twovmodules\RevenueCat\Enum\AppType;

readonly class App extends BaseDto
{
    public function __construct(
        public string $id,
        public string $name,
        public AppType $type,
        public int $createdAt,
        public string $projectId,
        public ?Amazon $amazon = null,
        public ?AppStore $appStore = null,
        public ?PlayStore $playStore = null,
        public ?MacAppStore $macAppStore = null,
        public ?Stripe $stripe = null,
        public ?RCBilling $rcBilling = null,
        public ?Roku $roku = null
    ) {
    }
}
