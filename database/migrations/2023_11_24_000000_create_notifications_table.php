<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('food_id')->nullable();
            $table->string('message')->nullable();
            $table->string('general');
            $table->string('status');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('food_id')->references('id')->on('foods')->nullOnDelete();
            $table->foreign('order_id')->references('id')->on('orders')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
