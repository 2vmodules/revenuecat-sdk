<?php

namespace Twovmodules\RevenueCat\Dto\Request;

use Twovmodules\RevenueCat\Dto\BaseDto;

readonly class CreatePackage extends BaseDto
{
    public function __construct(
        public string $lookupKey,
        public string $displayName,
        public ?int $position = null,
    ) {
    }
}
