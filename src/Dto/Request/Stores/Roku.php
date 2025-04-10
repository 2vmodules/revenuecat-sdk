<?php

namespace Twovmodules\RevenueCat\Dto\Request\Stores;

use Twovmodules\RevenueCat\Dto\BaseDto;

readonly class Roku extends BaseDto
{
    public function __construct(
        public ?string $rokuApiKey,
        public ?string $rokuChannelId,
        public ?string $rokuChannelName,
    ) {
    }
}
