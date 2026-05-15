<?php

namespace App\Enums;

enum WalletTransactionEnum: string
{
    case CREDIT = 'Credit';     // Money added to wallet
    case DEBIT = 'Debit';       // Money removed from wallet

    case FREEZE = 'Freeze';     // Move money into frozen_balance
    case RELEASE = 'Release';   // Move money out of frozen_balance

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
