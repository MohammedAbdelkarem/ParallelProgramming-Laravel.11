<?php

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
        Schema::create('number_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained("users")->cascadeOnDelete();
            $table->string("otp")->unique();
            $table->string('phone_number')->unique();
            $table->dateTime("expire_at");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('number_updates');
    }
};
