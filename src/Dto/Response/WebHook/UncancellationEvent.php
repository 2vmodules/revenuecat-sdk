<?php

namespace Twovmodules\RevenueCat\Dto\Response\WebHook;

readonly class UncancellationEvent extends BaseWebHookEvent
{
    use CommonFieldsTrait;

    public function __construct(array $commonFields)
    {
        parent::__construct(...(array) BaseWebHookEvent::fromArray($commonFields));
    }
}
