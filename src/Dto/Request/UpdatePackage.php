<?php

namespace Twovmodules\RevenueCat\Dto\Request;

use Twovmodules\RevenueCat\Dto\BaseDto;

readonly class UpdatePackage extends BaseDto
{
    public function __construct(
        public string $displayName,
        public int $position
    ) {
    }
}
