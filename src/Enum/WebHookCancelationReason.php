<?php

namespace Twovmodules\RevenueCat\Enum;

enum WebHookCancelationReason: string
{
    case UNSUBSCRIBE = 'UNSUBSCRIBE';
    case BILLING_ERROR = 'BILLING_ERROR';
    case DEVELOPER_INITIATED = 'DEVELOPER_INITIATED';
    case PRICE_INCREASE = 'PRICE_INCREASE';
    case CUSTOMER_SUPPORT = 'CUSTOMER_SUPPORT';
    case UNKNOWN = 'UNKNOWN';
    case SUBSCRIPTION_PAUSED = 'SUBSCRIPTION_PAUSED';
}
