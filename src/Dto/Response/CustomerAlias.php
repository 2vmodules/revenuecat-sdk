<?php

namespace Twovmodules\RevenueCat\Dto\Response;

use Twovmodules\RevenueCat\Dto\BaseDto;

readonly class CustomerAlias extends BaseDto
{
    public function __construct(
        public string $id,
        public int $createdAt
    ) {
    }
}
