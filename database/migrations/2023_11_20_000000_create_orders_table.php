<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('food_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('order_book_id')->nullable();
            $table->integer('qty')->nullable();
            $table->double('price', 12, 2)->nullable();
            $table->double('discount', 12, 2)->default(0.00);
            $table->double('gst_value', 8, 2)->nullable();
            $table->double('final_price', 12, 2)->default(0.00);
            $table->double('total', 12, 2)->nullable();
            $table->string('date', 50)->nullable();
            $table->string('dt_from')->nullable();
            $table->string('dt_to')->nullable();
            $table->string('order_id')->nullable();
            $table->string('finyear')->nullable();
            $table->double('cgst', 12, 2)->nullable();
            $table->double('igst', 12, 2)->nullable();
            $table->double('sgst', 12, 2)->nullable();
            $table->double('cess', 12, 2)->default(0.00);
            $table->double('net_price', 12, 2)->nullable();
            $table->integer('invoice_id')->default(0);
            $table->string('time_slot')->nullable();
            $table->unsignedBigInteger('time_slot_id')->nullable();
            $table->string('status');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('food_id')->references('id')->on('foods')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('order_book_id')->references('id')->on('order_books')->nullOnDelete();
            $table->foreign('time_slot_id')->references('id')->on('time_slots')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
