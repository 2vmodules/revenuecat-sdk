<?php

namespace Twovmodules\RevenueCat\Dto\Response\WebHook;

trait CommonFieldsTrait
{
    public static function withCommonFields(array $data): static
    {
        $baseWebHookEvent = BaseWebHookEvent::fromArray($data);
        $mergedFields = array_merge($data, (array) $baseWebHookEvent);

        /** @phpstan-ignore-next-line */
        return new static($mergedFields);
    }
}
