<?php

namespace App\Enums;

enum ActorTypeEnum: string
{
    case DRIVER    = 'driver';
    case DRIVER_COMPANY    = 'driver_company';
    case CUSTOM_CLEARENCE_COMPANY    = 'custom_clearence_company';
    case CUSTOMER    = 'customer';
    case ADMIN    = 'admin';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
