<?php

namespace Twovmodules\RevenueCat\Dto\Response;

use Twovmodules\RevenueCat\Dto\BaseDto;

readonly class InvoiceFile extends BaseDto
{
    public function __construct(
        public string $location
    ) {
    }
}
