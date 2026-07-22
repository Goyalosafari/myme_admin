<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('display_location')->nullable();
            $table->string('image')->default('25fa7a54163488dc43d4c20bd265c930.jpg');
            $table->integer('parent_id')->default(0);
            $table->string('status')->default('y');
            $table->string('company')->nullable();
            $table->string('ref')->nullable();
            $table->integer('order')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
