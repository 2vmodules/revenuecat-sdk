<?php

namespace Twovmodules\RevenueCat\Dto\Request;

use Twovmodules\RevenueCat\Dto\BaseDto;

readonly class CreateOffering extends BaseDto
{
    public function __construct(
        public string $lookupKey,
        public string $displayName,
        public ?array $metadata = null
    ) {
    }
}
