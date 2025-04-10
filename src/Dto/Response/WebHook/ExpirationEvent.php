<?php

namespace Twovmodules\RevenueCat\Dto\Response\WebHook;

use Twovmodules\RevenueCat\Enum\WebHookCancelationReason;

readonly class ExpirationEvent extends BaseWebHookEvent
{
    use CommonFieldsTrait;

    public function __construct(
        array $commonFields,
        public ?WebHookCancelationReason $expirationReason
    ) {
        parent::__construct(...(array) BaseWebHookEvent::fromArray($commonFields));
    }

    public static function withCommonFields(array $data): static
    {
        // @phpstan-ignore new.static
        return new static($data, WebHookCancelationReason::tryFrom($data['expiration_reason']) ?? null);
    }
}
