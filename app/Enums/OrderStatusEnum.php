<?php

namespace App\Enums;

enum OrderStatusEnum: string
{
    case PENDING    = 'pending';
    case PAID   = 'paid';
    case TO_DST   = 'to_dst';
    case DELIVERED   = 'delivered';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
