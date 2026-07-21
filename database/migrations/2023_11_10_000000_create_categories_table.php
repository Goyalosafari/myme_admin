<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type');
            $table->string('image')->default('25fa7a54163488dc43d4c20bd265c930.jpg');
            $table->integer('parent_id')->default(0);
            $table->string('status')->default('y');
            $table->string('company')->nullable();
            $table->string('ref')->nullable();
            $table->integer('order')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
