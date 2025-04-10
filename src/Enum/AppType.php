<?php

namespace Twovmodules\RevenueCat\Enum;

enum AppType: string
{
    case APP_STORE = 'app_store';
    case PLAY_STORE = 'play_store';
    case STRIPE = 'stripe';
    case AMAZON = 'amazon';
    case ROKU = 'roku';
    case RC_BILLING = 'rc_billing';
}
