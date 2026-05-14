<?php

namespace App\Enums;

enum DayEnum: string
{
    case MONDAY    = 'Monday';
    case TUESDAY   = 'Tuesday';
    case WEDNESDAY = 'Wednesday';
    case THURSDAY  = 'Thursday';
    case FRIDAY    = 'Friday';
    case SATURDAY  = 'Saturday';
    case SUNDAY    = 'Sunday';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
