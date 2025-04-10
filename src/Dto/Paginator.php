<?php

namespace Twovmodules\RevenueCat\Dto;

/**
 * @template T
 */
readonly class Paginator
{
    /**
     * @param  T[]|array  $items
     */
    public function __construct(
        public array $items,
        public ?string $nextPage = null,
        public ?string $url = null
    ) {
    }
}
