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
        Schema::create('jwt_personal_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained("users")->cascadeOnDelete();
            $table->foreignId('related_to')->nullable()->unique()->constrained("jwt_personal_tokens")->cascadeOnDelete();
            $table->foreignId('login_history_id')->nullable()->constrained("login_history")->cascadeOnDelete();
            $table->text('token');
            $table->json('claims')->nullable();
            $table->dateTime('expire_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jwt_personal_tokens');
    }
};