<?php

namespace Twovmodules\RevenueCat\Enum;

enum WebHookPeriodType: string
{
    case TRIAL = 'TRIAL';
    case INTRO = 'INTRO';
    case NORMAL = 'NORMAL';
    case PROMOTIONAL = 'PROMOTIONAL';
    case PREPAID = 'PREPAID';
}
