<?php

namespace Twovmodules\RevenueCat\Dto\Response\WebHook;

use Twovmodules\RevenueCat\Enum\WebHookCancelationReason;

readonly class CancellationEvent extends BaseWebHookEvent
{
    public function __construct(
        array $commonFields,
        public ?WebHookCancelationReason $cancelReason = null
    ) {
        parent::__construct(...(array) BaseWebHookEvent::fromArray($commonFields));
    }

    public static function withCommonFields(array $data): static
    {
        // @phpstan-ignore new.static
        return new static($data, WebHookCancelationReason::tryFrom($data['cancel_reason']) ?? null);
    }
}
