<?php

namespace App\Enums;

enum ReportStatusEnum: string
{
    case PENDING       = 'Pending';
    case PROCCESSING    = 'Proccessing';
    case COMPLETED     = 'Completed';
    case FAILED        = 'Failed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
