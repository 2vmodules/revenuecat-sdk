<?php

namespace Twovmodules\RevenueCat\Dto\Response;

use Twovmodules\RevenueCat\Dto\BaseDto;

readonly class SubscriptionDetails extends BaseDto
{
    public function __construct(
        public ?string $duration,
        public ?string $gracePeriodDuration,
        public ?string $trialDuration
    ) {
    }
}
