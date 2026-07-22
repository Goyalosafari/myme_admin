<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type');
            $table->string('calorie')->nullable();
            $table->string('cooking_time')->nullable();
            $table->string('taste')->nullable();
            $table->string('price')->nullable();
            $table->string('offer_price', 50)->nullable();
            $table->string('mrp', 50)->nullable();
            $table->string('margin', 100)->nullable();
            $table->string('preferences')->nullable();
            $table->string('meal_type')->nullable();
            $table->text('food_details')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('image')->nullable();
            $table->string('offer')->nullable();
            $table->integer('gst')->nullable();
            $table->double('gst_value', 8, 2)->nullable();
            $table->string('status')->default('y');
            $table->string('ref')->nullable();
            $table->string('veg', 100)->default('no');
            $table->integer('order')->default(0);
            $table->integer('parent_id')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foods');
    }
};
