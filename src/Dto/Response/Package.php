<?php

namespace Twovmodules\RevenueCat\Dto\Response;

use Twovmodules\RevenueCat\Dto\BaseDto;

readonly class Package extends BaseDto
{
    public function __construct(
        public string $id,
        public string $lookupKey,
        public string $displayName,
        public ?int $position = null,
        public ?int $createdAt = null,
        /** @var array{items: PackageProduct[]}|null $products */
        public ?array $products = null
    ) {
    }
}
