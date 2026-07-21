<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_food', function (Blueprint $table) {
            $table->unsignedBigInteger('food_id');
            $table->unsignedBigInteger('category_id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('food_id')->references('id')->on('foods')->cascadeOnDelete();
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_food');
    }
};
