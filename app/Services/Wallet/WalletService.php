<?php

namespace App\Services\Wallet;

use App\Constants\ExceptionMessages;
use App\Enums\OrderStatusEnum;
use App\Enums\WalletTransactionEnum;
use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Traits\NotificationHelper;

/**
 * Class WalletService.
 */
class WalletService
{
    use NotificationHelper;
    public function __construct(
    ) {}
    public function getMyWallet()
    {
        $user = auth()->user();

        return $user->wallet;
    }


    public function transfer($from, $to, float $amount, $reference , $notes)
    {
        // Resolve wallet owners (User is the wallet owner for all profiles)
        $fromWallet = Wallet::find(3);
        $toWallet   = Wallet::find(1);

        // dd($toWallet);

        if ($fromWallet->balance < $amount) {
            return forbiddenFailure(['available' => (int)$fromWallet->balance , 'amount' => $amount] , ExceptionMessages::MSG_AMOUNT_BIGGER_THAN_AVAILABLE);
        }

        // Resolve polymorphic types
        $fromType = User::class;
        $toType   = User::class;
        $refType  = Order::class;

        $from_name = 'User';
        $to_name   = 'Admin';
        $ref_name  = class_basename($reference) ?? null;



        // 1. Debit sender wallet
        WalletTransaction::create([
            'wallet_id' => $fromWallet->id,
            'type' => WalletTransactionEnum::DEBIT->value,
            'amount' => $amount,

            'from_id' => 3,
            'from_type' => $fromType,

            'to_id' => 1,
            'to_type' => $toType,

            'reference_id' => $reference->id,
            'reference_type' => $refType,

            'description' => WalletTransactionEnum::DEBIT->value . ' from ' . $from_name . ' for ' . $ref_name,

            'notes' => $notes
        ]);


        $fromWallet->balance -= $amount;
        $fromWallet->save();

        // 2. Credit receiver wallet
        WalletTransaction::create([
            'wallet_id' => $toWallet->id,
            'type' => WalletTransactionEnum::CREDIT->value,
            'amount' => $amount,

            'from_id' => 3,
            'from_type' => $fromType,

            'to_id' => 1,
            'to_type' => $toType,

            'reference_id' => $reference->id,
            'reference_type' => $refType,

            'description' => WalletTransactionEnum::CREDIT->value . ' to ' . $to_name . ' for ' . $ref_name,

            'notes' => $notes
        ]);
        

        Order::where('id' , $reference->id)->update([
            'status' => OrderStatusEnum::PAID->value
        ]);

        $toWallet->balance += $amount;
        $toWallet->save();
    }

    public function getSentWalletTransactions($data)
    {
        $user = auth()->user();

        $sent_transactions = WalletTransaction::where('from_id', $user->id)
            ->where('from_type', User::class);
        
        return getOrPaginate(
            $sent_transactions,
            $data
        );
    }

    public function getReceivedWalletTransactions($data)
    {
        $user = auth()->user();

        $received_transactions = WalletTransaction::where('to_id', $user->id)
            ->where('to_type', User::class);

        return getOrPaginate(
            $received_transactions,
            $data
        );
    }
}
