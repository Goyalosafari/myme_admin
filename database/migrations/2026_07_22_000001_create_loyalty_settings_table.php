<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('points_per_amount')->default(100);
            $table->unsignedInteger('min_points_to_convert')->default(50);
            $table->timestamps();
        });

        // Single settings row the admin screen reads/updates.
        \DB::table('loyalty_settings')->insert([
            'points_per_amount'     => 100,
            'min_points_to_convert' => 50,
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_settings');
    }
};
