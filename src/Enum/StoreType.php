<?php

namespace Twovmodules\RevenueCat\Enum;

enum StoreType: string
{
    case AMAZON = 'amazon';
    case APP_STORE = 'app_store';
    case MAC_APP_STORE = 'mac_app_store';
    case PLAY_STORE = 'play_store';
    case PROMOTIONAL = 'promotional';
    case STRIPE = 'stripe';
    case RC_BILLING = 'rc_billing';
    case ROKU = 'roku';
}
