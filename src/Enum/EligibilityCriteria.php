<?php

namespace Twovmodules\RevenueCat\Enum;

enum EligibilityCriteria: string
{
    case ALL = 'all';
    case GOOGLE_SDK_LT_6 = 'google_sdk_lt_6';
    case GOOGLE_SDK_GE_6 = 'google_sdk_ge_6';
}
