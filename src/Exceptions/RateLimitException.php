<?php

namespace Twovmodules\RevenueCat\Exceptions;

use Exception;

class RateLimitException extends RevenueCatException
{
    public function __construct(
        string $message = 'Rate limit exceeded',
        int $code = 429,
        protected ?int $retryAfter = null,
        protected ?int $currentUsage = null,
        protected ?int $currentLimit = null,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getRetryAfter(): ?int
    {
        return $this->retryAfter;
    }

    public function getCurrentUsage(): ?int
    {
        return $this->currentUsage;
    }

    public function getCurrentLimit(): ?int
    {
        return $this->currentLimit;
    }
}
