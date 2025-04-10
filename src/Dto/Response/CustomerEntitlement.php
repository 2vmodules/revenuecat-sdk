<?php

namespace Twovmodules\RevenueCat\Dto\Response;

use Twovmodules\RevenueCat\Dto\BaseDto;

readonly class CustomerEntitlement extends BaseDto
{
    public function __construct(
        public string $entitlementId,
        public int $expiresAt,
    ) {
    }
}
