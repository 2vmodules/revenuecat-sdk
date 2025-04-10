<?php

namespace Twovmodules\RevenueCat\Dto\Response\WebHook;

use InvalidArgumentException;
use Twovmodules\RevenueCat\Enum\WebHookEventType;

class WebhookEventFactory
{
    public static function createWebhookEventByType(array $payload): BaseWebHookEvent|TransferEvent
    {
        /** @var WebHookEventType|null $eventType */
        $eventType = WebHookEventType::tryFrom($payload['type']);

        if (empty($eventType)) {
            throw new InvalidArgumentException('Invalid webhook event type');
        }

        return match ($eventType) {
            WebHookEventType::TEST => TestEvent::withCommonFields($payload),
            WebHookEventType::INITIAL_PURCHASE => InitialPurchaseEvent::withCommonFields($payload),
            WebHookEventType::CANCELLATION => CancellationEvent::withCommonFields($payload),
            WebHookEventType::UNCANCELLATION => UncancellationEvent::withCommonFields($payload),
            WebHookEventType::RENEWAL => RenewalEvent::withCommonFields($payload),
            WebHookEventType::NON_RENEWING_PURCHASE => NonRenewingPurchaseEvent::withCommonFields($payload),
            WebHookEventType::SUBSCRIPTION_PAUSED => SubscriptionPausedEvent::withCommonFields($payload),
            WebHookEventType::BILLING_ISSUE => BillingIssueEvent::withCommonFields($payload),
            WebHookEventType::EXPIRATION => ExpirationEvent::withCommonFields($payload),
            WebHookEventType::TRANSFER => TransferEvent::fromArray($payload),
            WebHookEventType::PRODUCT_CHANGE => ProductChangeEvent::withCommonFields($payload),
            WebHookEventType::SUBSCRIPTION_EXTENDED => SubscriptionExtendedEvent::withCommonFields($payload),
            WebHookEventType::TEMPORARY_ENTITLEMENT_GRANT => TemporaryEntitlementGrantEvent::withCommonFields($payload),
            WebHookEventType::INVOICE_ISSUANCE => InvoiceIssuanceEvent::withCommonFields($payload),
            WebHookEventType::SUBSCRIBER_ALIAS => SubscriberAliasEvent::withCommonFields($payload),
        };
    }
}
