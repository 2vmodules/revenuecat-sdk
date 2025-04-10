<?php

namespace Twovmodules\RevenueCat\Dto\Response\WebHook;

readonly class SubscriptionPausedEvent extends BaseWebHookEvent
{
    use CommonFieldsTrait;

    public function __construct(
        array $commonFields,
        public ?int $autoResumeAtMs = null
    ) {
        parent::__construct(...(array) BaseWebHookEvent::fromArray($commonFields));
    }

    public static function withCommonFields(array $data): static
    {
        // @phpstan-ignore new.static
        return new static($data, $data['auto_resume_at_ms'] ?? null);
    }
}
