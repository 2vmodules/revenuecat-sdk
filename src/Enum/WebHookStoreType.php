<?php

namespace Twovmodules\RevenueCat\Enum;

enum WebHookStoreType: string
{
    case AMAZON = 'AMAZON';
    case APP_STORE = 'APP_STORE';
    case MAC_APP_STORE = 'MAC_APP_STORE';
    case PLAY_STORE = 'PLAY_STORE';
    case PROMOTIONAL = 'PROMOTIONAL';
    case STRIPE = 'STRIPE';
    case RC_BILLING = 'RC_BILLING';
    case ROKU = 'ROKU';
}
