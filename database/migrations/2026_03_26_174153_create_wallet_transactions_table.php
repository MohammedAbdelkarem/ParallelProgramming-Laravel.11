<?php

use App\Enums\WalletTransactionEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('wallet_id')->constrained('wallets')->cascadeOnDelete();

            $table->enum('type', WalletTransactionEnum::values());
            $table->decimal('amount', 12, 2);

            // Polymorphic reference (order, settlement, refund, dispute)
            $table->nullableMorphs('reference');

            $table->morphs('from');
            $table->morphs('to');

            $table->string('description')->nullable();

            $table->string('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
