<?php

namespace App\Services\Wallet;

use App\Constants\ExceptionMessages;
use App\Enums\OrderStatusEnum;
use App\Enums\WalletTransactionEnum;
use App\Jobs\TransferJob;
use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Traits\NotificationHelper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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

    public function transfer($from, $to, float $amount, $reference, $notes)
    {
        // TransferJob::dispatch(
        //     $from,
        //     $to,
        //     $amount,
        //     $reference,
        //     $notes
        // );
        $this->utilityTransfer($from, $to, $amount, $reference, $notes);
    }

    public function utilityTransfer($from, $to, float $amount, $reference, $notes)
    {
        // 1️⃣ إنشاء القفل الموزع على مستوى الـ RAM (ينتهي تلقائياً بعد 10 ثوانٍ)
        // $lock = Cache::lock("lock:wallet:transfer:from:{$from}", 10);

        // // 2️⃣ محاولة الحصول على القفل فوراً، إذا فشل (بسبب طلب متزامن) يرتد الكود هنا دون انتظار
        // if (! $lock->get()) {
        //     return forbiddenFailure(
        //         ['wallet_id' => $from],
        //         "هناك عملية تحويل معلقة قيد المعالجة حالياً لهذه المحفظة، يرجى الانتظار."
        //     );
        // }

        // // 3️⃣ تنفيذ العملية المالية بأمان بعد ضمان انفراد هذا الطلب بالسيرفر
        // $result = DB::transaction(function () use ($from, $to, $amount, $reference, $notes) {
            
        //     // ترتيب الأقفال لمنع الـ Deadlock داخل قاعدة البيانات كما هو في كودك الأصلي
        //     $walletA = Wallet::where('id', min($from, $to))->lockForUpdate()->firstOrFail();
        //     $walletB = Wallet::where('id', max($from, $to))->lockForUpdate()->firstOrFail();

        //     $fromWallet = $from == $walletA->id ? $walletA : $walletB;
        //     $toWallet   = $to   == $walletA->id ? $walletA : $walletB;

        //     // التحقق من الرصيد
        //     if ($fromWallet->balance < $amount) {
        //         return forbiddenFailure(
        //             ['available' => (int)$fromWallet->balance, 'amount' => $amount],
        //             ExceptionMessages::MSG_AMOUNT_BIGGER_THAN_AVAILABLE
        //         );
        //     }

        //     // تجهيز البيانات الديناميكية (Polymorphic)
        //     $fromType = get_class($fromWallet->user);
        //     $toType   = get_class($toWallet->user);
        //     $refType  = Order::class;

        //     $fromName = class_basename($fromType);
        //     $toName   = class_basename($toType);
        //     $refName  = class_basename($reference) ?? null;

        //     // 🟢 خصم المحفظة المرسِلة وتسجيل المعاملة
        //     WalletTransaction::create([
        //         'wallet_id' => $fromWallet->id,
        //         'type' => WalletTransactionEnum::DEBIT->value,
        //         'amount' => $amount,
        //         'from_id' => $fromWallet->user_id,
        //         'from_type' => $fromType,
        //         'to_id' => $toWallet->user_id,
        //         'to_type' => $toType,
        //         'reference_id' => $reference->id,
        //         'reference_type' => $refType,
        //         'description' => "Debit from {$fromName} to {$toName} for {$refName}",
        //         'notes' => $notes,
        //     ]);
        //     $fromWallet->decrement('balance', $amount);

        //     // 🔵 شحن المحفظة المستقبلة وتسجيل المعاملة
        //     WalletTransaction::create([
        //         'wallet_id' => $toWallet->id,
        //         'type' => WalletTransactionEnum::CREDIT->value,
        //         'amount' => $amount,
        //         'from_id' => $fromWallet->user_id,
        //         'from_type' => $fromType,
        //         'to_id' => $toWallet->user_id,
        //         'to_type' => $toType,
        //         'reference_id' => $reference->id,
        //         'reference_type' => $refType,
        //         'description' => "Credit to {$toName} from {$fromName} for {$refName}",
        //         'notes' => $notes,
        //     ]);
        //     $toWallet->increment('balance', $amount);

        //     // تحديث حالة الطلب إن وُجد
        //     if ($reference instanceof Order) {
        //         $reference->update(['status' => OrderStatusEnum::PAID->value]);
        //     }

        //     return true;
        // });

        // // 4️⃣ تحرير القفل الموزع يدوياً فور انتهاء المعاملة بنجاح أو فشل الرصيد لفتح المجال للطلبات التالية
        // $lock->release();

        // return $result;

        $walletA = Wallet::find(1);
        $walletB = Wallet::where('user_id' , auth()->id())->first();



        $fromWallet = $from == $walletA->id ? $walletA : $walletB;
        $toWallet   = $to   == $walletA->id ? $walletA : $walletB;

        // التحقق من الرصيد
        if ($fromWallet->balance < $amount) {
            return forbiddenFailure(
                ['available' => (int)$fromWallet->balance, 'amount' => $amount],
                ExceptionMessages::MSG_AMOUNT_BIGGER_THAN_AVAILABLE
            );
        }

        // تجهيز البيانات الديناميكية (Polymorphic)
        $fromType = get_class($fromWallet->user);
        $toType   = get_class($toWallet->user);
        $refType  = Order::class;

        $fromName = class_basename($fromType);
        $toName   = class_basename($toType);
        $refName  = class_basename($reference) ?? null;

        // 🟢 خصم المحفظة المرسِلة وتسجيل المعاملة
        WalletTransaction::create([
            'wallet_id' => $fromWallet->id,
            'type' => WalletTransactionEnum::DEBIT->value,
            'amount' => $amount,
            'from_id' => $fromWallet->user_id,
            'from_type' => $fromType,
            'to_id' => $toWallet->user_id,
            'to_type' => $toType,
            'reference_id' => $reference->id,
            'reference_type' => $refType,
            'description' => "Debit from {$fromName} to {$toName} for {$refName}",
            'notes' => $notes,
        ]);
        $fromWallet->decrement('balance', $amount);

        // 🔵 شحن المحفظة المستقبلة وتسجيل المعاملة
        WalletTransaction::create([
            'wallet_id' => $toWallet->id,
            'type' => WalletTransactionEnum::CREDIT->value,
            'amount' => $amount,
            'from_id' => $fromWallet->user_id,
            'from_type' => $fromType,
            'to_id' => $toWallet->user_id,
            'to_type' => $toType,
            'reference_id' => $reference->id,
            'reference_type' => $refType,
            'description' => "Credit to {$toName} from {$fromName} for {$refName}",
            'notes' => $notes,
        ]);
        $toWallet->increment('balance', $amount);

        // تحديث حالة الطلب إن وُجد
        if ($reference instanceof Order) {
            $reference->update(['status' => OrderStatusEnum::PAID->value]);
        }

        return true;
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
