<?php

namespace Twovmodules\RevenueCat\Dto\Response\WebHook;

use Twovmodules\RevenueCat\Dto\BaseDto;
use Twovmodules\RevenueCat\Enum\WebHookEnvironmentType;
use Twovmodules\RevenueCat\Enum\WebHookEventType;
use Twovmodules\RevenueCat\Enum\WebHookPeriodType;
use Twovmodules\RevenueCat\Enum\WebHookStoreType;

readonly class BaseWebHookEvent extends BaseDto
{
    public function __construct(
        public string $id,
        public ?string $appId,
        public string $appUserId,
        public array $aliases,
        public ?float $commissionPercentage,
        public ?string $countryCode,
        public ?string $currency,
        public ?string $entitlementId,
        public ?array $entitlementIds,
        public WebHookEnvironmentType $environment,
        public int $eventTimestampMs,
        public ?int $expirationAtMs,
        public ?bool $isFamilyShare,
        public ?string $offerCode,
        public string $originalAppUserId,
        public ?string $originalTransactionId,
        public WebHookPeriodType $periodType,
        public ?string $presentedOfferingId,
        public ?float $price,
        public ?float $priceInPurchasedCurrency,
        public ?string $productId,
        public int $purchasedAtMs,
        public WebHookStoreType $store,
        public ?array $subscriberAttributes,
        public ?float $takehomePercentage,
        public ?float $taxPercentage,
        public ?string $transactionId,
        public WebHookEventType $type
    ) {
    }
}
