<?php

namespace Twovmodules\RevenueCat\Dto\Response\WebHook;

readonly class BillingIssueEvent extends BaseWebHookEvent
{
    use CommonFieldsTrait;

    public function __construct(
        array $commonFields,
        public ?int $gracePeriodExpirationAtMs = null
    ) {
        parent::__construct(...(array) BaseWebHookEvent::fromArray($commonFields));
    }

    public static function withCommonFields(array $data): static
    {
        // @phpstan-ignore new.static
        return new static($data, $data['grace_period_expiration_at_ms'] ?? null);
    }
}
