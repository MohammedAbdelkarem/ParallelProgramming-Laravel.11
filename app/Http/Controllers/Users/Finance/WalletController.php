<?php

namespace App\Http\Controllers\Users\Finance;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\TransferRequest;
use App\Http\Resources\Settlement\SettlementResource;
use App\Http\Resources\Wallet\WalletResource;
use App\Http\Resources\WalletTransaction\WalletTransactionResource;
use App\Models\Order;
use App\Models\Settlement;
use App\Models\User;
use App\Services\Wallet\WalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{


    public function __construct(
        protected WalletService $walletService
    )
    {
    }

    public function getMyWallet()
    {
        return success(
            $this->walletService->getMyWallet(),
            ApiMessages::MSG_SUCCESS,
            WalletResource::class
        );
    }

    public function transfer(TransferRequest $request)
    {
        $validatedData = $request->validated();

        $from = User::class;

        $to = User::class;
        
        $order = Order::find($validatedData['order_id']) ?? null;

        return success(
            $this->walletService->transfer(
                $from,
                $to,
                $validatedData['amount'],
                $order,
                $validatedData['notes']
            ),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function getSentTransactions(Request $request)
    {
        return success(
            $this->walletService->getSentWalletTransactions($request->all()),
            ApiMessages::MSG_SUCCESS,
            WalletTransactionResource::class
        );
    }

    public function getReceivedTransactions(Request $request)
    {
        return success(
            $this->walletService->getReceivedWalletTransactions($request->all()),
            ApiMessages::MSG_SUCCESS,
            WalletTransactionResource::class
        );
    }
}
