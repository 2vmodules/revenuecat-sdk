<?php

namespace Twovmodules\RevenueCat\Dto\Request\Stores;

use Twovmodules\RevenueCat\Dto\BaseDto;

readonly class Amazon extends BaseDto
{
    public function __construct(
        public string $packageName,
        public ?string $sharedSecret,
    ) {
    }
}
