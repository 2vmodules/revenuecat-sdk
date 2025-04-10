<?php

namespace Twovmodules\RevenueCat\Dto\Response;

use Twovmodules\RevenueCat\Dto\BaseDto;

readonly class CustomerAttribute extends BaseDto
{
    public function __construct(
        public string $name,
        public string $updatedAt,
        public ?string $value,
    ) {
    }
}
