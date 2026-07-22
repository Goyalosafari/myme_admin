<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_settings', function (Blueprint $table) {
            $table->id();
            $table->double('reward_amount', 8, 2)->default(100.00);
            $table->timestamps();
        });

        // Single settings row the admin screen reads/updates.
        \DB::table('referral_settings')->insert([
            'reward_amount' => 100.00,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_settings');
    }
};
