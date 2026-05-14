<?php

namespace App\Enums;

enum SystemSettingsKeys: string
{
    //TODO:TEMPLATE
    case PRICE_FOR_KM         = 'Price for KM';
    case ORDER_CIRCLE_RADIUS_KM   = 'Order circle radius KM';
    case PLATFORM_RATIO_PERCENTAGE     = 'Platform ratio percentage';
    case TAX_VALUE_PERCENTAGE     = 'Tax value percentage';
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}