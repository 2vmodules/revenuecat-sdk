<?php

namespace Twovmodules\RevenueCat\Dto\Request;

use Twovmodules\RevenueCat\Dto\BaseDto;

readonly class CreateCustomerAttribute extends BaseDto
{
    public function __construct(
        public string $name,
        public ?string $value,
    ) {
    }
}
