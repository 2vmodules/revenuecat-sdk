<?php

namespace Twovmodules\RevenueCat\Dto\Response\WebHook;

readonly class RenewalEvent extends BaseWebHookEvent
{
    use CommonFieldsTrait;

    public function __construct(array $commonFields)
    {
        parent::__construct(...(array) BaseWebHookEvent::fromArray($commonFields));
    }
}
