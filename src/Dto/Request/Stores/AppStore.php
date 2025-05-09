<?php

namespace Twovmodules\RevenueCat\Dto\Request\Stores;

use Twovmodules\RevenueCat\Dto\BaseDto;

readonly class AppStore extends BaseDto
{
    public function __construct(
        public string $bundleId,
        public ?string $sharedSecret,
    ) {
    }
}
