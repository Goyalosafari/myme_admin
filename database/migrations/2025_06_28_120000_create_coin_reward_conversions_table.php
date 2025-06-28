<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('coin_reward_conversions', function (Blueprint $table) {
            $table->id();
            $table->decimal('coin_to_reward_rate', 10, 2);
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('coin_reward_conversions');
    }
};
