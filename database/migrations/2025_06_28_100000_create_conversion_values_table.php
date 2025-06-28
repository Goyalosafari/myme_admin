<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('conversion_values', function (Blueprint $table) {
            $table->id();
            $table->decimal('coin_conversion_rate', 10, 2);
            $table->integer('referrer_reward_points');
            $table->integer('referee_reward_points');
            $table->decimal('minimum_applicable_amount', 10, 2)->default(0); // New field
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('conversion_values');
    }
};
