<?php

namespace App\Enums;

enum MediaTypeEnum: string
{
    case IMAGE = 'image';
    case VIDEO = 'video';
    case PDF  = 'pdf';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
