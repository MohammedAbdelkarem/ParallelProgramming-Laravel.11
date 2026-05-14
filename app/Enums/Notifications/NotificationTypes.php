<?php

namespace App\Enums\Notifications;

enum NotificationTypes: string
{
    case AUTH = 'auth';
    case TRIP = 'trip';
    case CLEARENCE_TRANSACTION = 'clearence_transaction';
    case ORDER = 'order';
    case CHAT = 'chat';
    case COMPLAINT = 'complaint';
    case WALLET = 'wallet';
    case OTHER = 'other';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}