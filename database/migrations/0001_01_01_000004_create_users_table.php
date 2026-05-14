<?php

use App\Enums\AccountStatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->default(2)->constrained('roles'); // 1 => Super Admin / 2 => admin / 3=> doctor / 4=> patient
            $table->string('name')->nullable(); // nullable to fill them in the third screen (profile info)
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->date('birth_date')->nullable();
            $table->boolean('is_male')->nullable();
            $table->string('language')->default('en');
            $table->boolean('active_notifications')->default(true);
            $table->dateTime('deactive_at')->nullable();
            $table->enum('account_status', AccountStatusEnum::values())->default(AccountStatusEnum::PENDING->value);
            $table->timestamp('account_verified_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};