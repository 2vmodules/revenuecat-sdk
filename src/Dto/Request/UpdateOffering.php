<?php

namespace Twovmodules\RevenueCat\Dto\Request;

use Twovmodules\RevenueCat\Dto\BaseDto;

readonly class UpdateOffering extends BaseDto
{
    public function __construct(
        public string $displayName,
        public bool $isCurrent,
        public ?array $metadata = null
    ) {
    }
}
