<?php

namespace Twovmodules\RevenueCat\Dto\Response;

use Twovmodules\RevenueCat\Dto\BaseDto;

readonly class Offering extends BaseDto
{
    public function __construct(
        public string $id,
        public string $lookupKey,
        public string $displayName,
        public bool $isCurrent,
        public int $createdAt,
        public string $projectId,
        public ?array $metadata = null,
        /** @var array{items: Package[]}|null $packages */
        public ?array $packages = null
    ) {
    }
}
