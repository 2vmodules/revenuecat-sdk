<?php

namespace Twovmodules\RevenueCat\Enum;

enum AutoRenewalStatus: string
{
    case WILL_RENEW = 'will_renew';
    case WILL_NOT_RENEW = 'will_not_renew';
    case WILL_CHANGE_PRODUCT = 'will_change_product';
    case WILL_PAUSE = 'will_pause';
    case REQUIRES_PRICE_INCREASE_CONSENT = 'requires_price_increase_consent';
    case HAS_ALREADY_RENEWED = 'has_already_renewed';
}
