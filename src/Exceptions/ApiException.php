<?php

namespace Twovmodules\RevenueCat\Exceptions;

class ApiException extends RevenueCatException
{
    public function __construct(
        string $message,
        public readonly int $statusCode,
        public readonly ?string $docUrl = null
    ) {
        parent::__construct($message, $statusCode);
    }
}
