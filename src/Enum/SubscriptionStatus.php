<?php

namespace Twovmodules\RevenueCat\Enum;

enum SubscriptionStatus: string
{
    case TRIALING = 'trialing';
    case ACTIVE = 'active';
    case EXPIRED = 'expired';
    case IN_GRACE_PERIOD = 'in_grace_period';
    case IN_BILLING_RETRY = 'in_billing_retry';
    case PAUSED = 'paused';
    case UNKNOWN = 'unknown';
    case INCOMPLETE = 'incomplete';
}
