<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_rewards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('referrer_user_id');
            $table->unsignedBigInteger('referred_user_id')->unique();
            $table->unsignedBigInteger('order_book_id')->nullable();
            $table->double('amount', 8, 2);
            $table->timestamps();

            $table->foreign('referrer_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('referred_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('order_book_id')->references('id')->on('order_books')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_rewards');
    }
};
