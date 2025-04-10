<?php

namespace Twovmodules\RevenueCat\Dto\Request;

use Twovmodules\RevenueCat\Dto\BaseDto;

readonly class CreateEntitlement extends BaseDto
{
    public function __construct(
        public string $lookupKey,
        public string $displayName
    ) {
    }
}
