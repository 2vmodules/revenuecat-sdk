<?php

namespace Twovmodules\RevenueCat\Dto\Response;

use Twovmodules\RevenueCat\Dto\BaseDto;

readonly class Project extends BaseDto
{
    public function __construct(
        public string $id,
        public string $name,
        public int $createdAt
    ) {
    }
}
