<?php

namespace App\Http\Controllers\Administration\Finance;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\TransferRequest;
use App\Http\Requests\Wallet\UpdateFeeRequest;
use App\Http\Resources\Settlement\SettlementResource;
use App\Http\Resources\Wallet\WalletResource;
use App\Http\Resources\WalletTransaction\WalletTransactionResource;
use App\Models\Settlement;
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

    public function getActorProfile($id , Request $request)
    {
        return success(
            $this->walletService->getActorProfile($id , $request->query('type')),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function transfer(TransferRequest $request)
    {
        $validatedData = $request->validated();

        $from = auth()->user()->currentProfile();

        $to = getModel($validatedData['to_type'])::find($validatedData['to_id']);
        
        $settlement = Settlement::find($validatedData['settlement_id']);

        return success(
            $this->walletService->transfer(
                $from,
                $to,
                $validatedData['amount'],
                $settlement,
                $validatedData['notes']
            ),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function archive($settlement_id)
    {
        return success(
            $this->walletService->archiveSettlement($settlement_id),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function getSettlements(Request $request)
    {
        return success(
            $this->walletService->getSettlementsForAdmin($request->all()),
            ApiMessages::MSG_SUCCESS,
            SettlementResource::class,
            $request->has('per_page')
        );
    }

    public function showSettlement($id)
    {
        return success(
            $this->walletService->showSettlementForAdmin($id),
            ApiMessages::MSG_SUCCESS,
            SettlementResource::class
        );
    }

    public function getSentTransactions(Request $request)
    {
        return success(
            $this->walletService->getSentWalletTransactions($request->all()),
            ApiMessages::MSG_SUCCESS,
            WalletTransactionResource::class,
            $request->has('per_page')
        );
    }

    public function getReceivedTransactions(Request $request)
    {
        return success(
            $this->walletService->getReceivedWalletTransactions($request->all()),
            ApiMessages::MSG_SUCCESS,
            WalletTransactionResource::class,
            $request->has('per_page')
        );
    }

    public function updateWithdrawalFeeSettings(UpdateFeeRequest $request , $wallet_id)
    {
        return success(
            $this->walletService->updateFeeForWallet($request->validated(), $wallet_id),
            ApiMessages::MSG_SUCCESS,
        );
    }
}
