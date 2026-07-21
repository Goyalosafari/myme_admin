<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupens', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('coupen_code', 100)->nullable();
            $table->string('no_of_usage', 100)->nullable();
            $table->string('discount_type', 100)->nullable();
            $table->string('discount', 100)->nullable();
            $table->string('max_discount')->nullable();
            $table->string('min_amount', 100)->nullable();
            $table->string('from_date', 100)->nullable();
            $table->string('to_date', 100)->nullable();
            $table->string('status')->default('y');
            $table->integer('order')->default(0);
            $table->integer('parent_id')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupens');
    }
};
