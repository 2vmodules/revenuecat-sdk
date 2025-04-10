<?php

declare(strict_types=1);

namespace Twovmodules\RevenueCat;

class Configuration
{
    private const DEFAULT_BASE_URL = 'https://api.revenuecat.com/v2';

    public function __construct(
        private readonly string $apiKey,
        private string $baseUrl = self::DEFAULT_BASE_URL,
        private readonly bool $sandbox = false
    ) {
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function isSandbox(): bool
    {
        return $this->sandbox;
    }

    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

    public function withSandboxMode(bool $sandbox = true): self
    {
        return new self($this->apiKey, $this->baseUrl, $sandbox);
    }

    public static function create(string $apiKey): self
    {
        return new self($apiKey);
    }
}
