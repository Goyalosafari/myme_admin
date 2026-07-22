<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('food_id')->nullable();
            $table->string('num_of_serving', 100)->nullable();
            $table->text('description');
            $table->text('ingredients');
            $table->text('nutritional_facts');
            $table->text('utensils');
            $table->string('image')->nullable();
            $table->string('ref')->nullable();
            $table->integer('order')->default(0);
            $table->integer('parent_id')->default(0);
            $table->string('status')->default('y');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
            $table->foreign('food_id')->references('id')->on('foods')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
