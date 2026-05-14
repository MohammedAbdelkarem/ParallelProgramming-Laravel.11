<?php

use App\Enums\Notifications\NotificationScreens;
use App\Enums\Notifications\NotificationTypes;
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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('title');
            $table->json('body');
            $table->enum('type', NotificationTypes::values())->nullable();
            // $table->enum('page', NotificationScreens::values())->default(NotificationScreens::HOME->value);
            $table->boolean("clickable")->default(false);
            $table->string("requested_id")->nullable();     //String instead of unsignedBigInteger because it may be account_name /slug/ email not integer id only
            $table->boolean('is_public')->default(false);
            $table->json("extra_data")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};