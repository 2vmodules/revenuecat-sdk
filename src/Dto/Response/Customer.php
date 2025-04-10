<?php

namespace Twovmodules\RevenueCat\Dto\Response;

use Twovmodules\RevenueCat\Dto\BaseDto;

readonly class Customer extends BaseDto
{
    public function __construct(
        public string $id,
        public string $projectId,
        public int $firstSeenAt,
        public ?int $lastSeenAt,
        public ?array $activeEntitlements = null,
        public ?array $experiment = null,
        public ?array $attributes = null
    ) {
    }
}
