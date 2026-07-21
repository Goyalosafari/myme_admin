<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('food_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->decimal('ratings', 3, 2)->nullable();
            $table->string('feedback')->nullable();
            $table->timestamps();

            $table->foreign('food_id')->references('id')->on('foods')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
