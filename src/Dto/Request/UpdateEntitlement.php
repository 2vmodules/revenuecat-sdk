<?php

namespace Twovmodules\RevenueCat\Dto\Request;

use Twovmodules\RevenueCat\Dto\BaseDto;

readonly class UpdateEntitlement extends BaseDto
{
    public function __construct(
        public string $displayName
    ) {
    }
}
