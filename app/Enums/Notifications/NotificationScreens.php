<?php

namespace App\Enums\Notifications;

enum NotificationScreens: string
{
    case HOME           = '/home';
    case PROFILE_SCREEN = '/profile';
    case PRODUCTS       = '/products';
    case PRODUCT_SCREEN = '/product';
    case CUSTOMER_SERVICE_CARDS         = '/customer-service-cards';
    case CUSTOMER_SERVICE_CARD_SCREEN   = '/customer-service-card';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
