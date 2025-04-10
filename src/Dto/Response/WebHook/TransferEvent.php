<?php

namespace Twovmodules\RevenueCat\Dto\Response\WebHook;

use Twovmodules\RevenueCat\Dto\BaseDto;
use Twovmodules\RevenueCat\Enum\WebHookEnvironmentType;
use Twovmodules\RevenueCat\Enum\WebHookEventType;
use Twovmodules\RevenueCat\Enum\WebHookStoreType;

readonly class TransferEvent extends BaseDto
{
    public function __construct(
        public int $appId,
        public int $eventTimestampMs,
        public string $id,
        public WebHookStoreType $store,
        public array $transferredFrom,
        public array $transferredTo,
        public WebHookEventType $type,
        public WebHookEnvironmentType $environment
    ) {
    }
}
