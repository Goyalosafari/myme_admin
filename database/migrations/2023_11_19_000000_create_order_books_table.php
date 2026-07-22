<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_books', function (Blueprint $table) {
            $table->id();
            $table->string('customer')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->bigInteger('invoice')->default(0);
            $table->date('invoice_dt')->nullable();
            $table->string('prefix', 100)->default('INV-');
            $table->double('cgst', 13, 2)->nullable();
            $table->double('igst', 13, 2)->nullable();
            $table->double('sgst', 13, 2)->nullable();
            $table->double('wac', 13, 2)->nullable();
            $table->double('value', 13, 2)->nullable();
            $table->double('gst_sum', 8, 2)->nullable();
            $table->double('wallet', 8, 2)->nullable();
            $table->double('charge', 13, 2)->nullable();
            $table->double('coupon', 13, 2)->nullable();
            $table->double('payment_amount', 13, 2)->default(0.00);
            $table->string('payment_ref', 100)->nullable();
            $table->string('payment_mode', 100)->nullable();
            $table->string('payment_status', 100)->nullable();
            $table->string('status')->nullable();
            $table->string('order_id')->nullable();
            $table->string('del_md')->nullable();
            $table->string('user')->default('na');
            $table->string('ref')->nullable();
            $table->string('ref1')->nullable();
            $table->timestamp('date')->useCurrent();
            $table->string('company')->nullable();
            $table->string('del_dt', 100)->nullable();
            $table->string('finyear')->nullable();
            $table->double('cess', 12, 2)->nullable();
            $table->string('pack_user')->default('na');
            $table->integer('warehouse')->default(0);
            $table->string('print', 10)->default('No');
            $table->bigInteger('src_no')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_books');
    }
};
