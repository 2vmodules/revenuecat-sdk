<?php

namespace Twovmodules\RevenueCat\Dto\Response;

use Twovmodules\RevenueCat\Dto\BaseDto;

/**
 * period может быть P0D (текущие данные) или P28D (28 дней)​
 */
readonly class OverviewMetrics extends BaseDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public string $unit,
        public string $period,
        public float $value,
        public ?int $lastUpdatedAt = null,
        public ?string $lastUpdatedAtIso8601 = null
    ) {
    }
}
