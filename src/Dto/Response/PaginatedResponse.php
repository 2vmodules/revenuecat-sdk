<?php

namespace Twovmodules\RevenueCat\Dto\Response;

use Twovmodules\RevenueCat\Dto\BaseDto;

readonly class PaginatedResponse extends BaseDto
{
    public function __construct(
        public array $items,
        public ?string $nextPage = null,
        public ?string $url = null
    ) {
    }
}
