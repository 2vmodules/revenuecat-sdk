<?php

namespace Twovmodules\RevenueCat\Dto\Response;

use Twovmodules\RevenueCat\Dto\BaseDto;

readonly class Invoice extends BaseDto
{
    public function __construct(
        public string $id,
        public float $totalAmount,
        public array $lineItems,
        public int $issuedAt,
        public ?int $paidAt = null,
        public ?string $invoiceUrl = null
    ) {
    }
}
