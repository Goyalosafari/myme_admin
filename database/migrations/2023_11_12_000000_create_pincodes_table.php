<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pincodes', function (Blueprint $table) {
            $table->id();
            $table->string('pincode', 100)->nullable();
            $table->string('place_name', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('delivery_fee', 50)->default('0');
            $table->string('other_fee', 50)->default('0');
            $table->integer('order')->default(0);
            $table->integer('parent_id')->default(0);
            $table->string('status')->default('y');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pincodes');
    }
};
