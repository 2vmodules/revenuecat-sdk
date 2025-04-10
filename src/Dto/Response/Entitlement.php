<?php

namespace Twovmodules\RevenueCat\Dto\Response;

use Twovmodules\RevenueCat\Dto\BaseDto;

readonly class Entitlement extends BaseDto
{
    public function __construct(
        public string $id,
        public string $lookupKey,
        public string $displayName,
        public int $createdAt,
        public string $projectId,
        /** @var array{items: Product[]}|null $products */
        public ?array $products = null
    ) {
    }
}
