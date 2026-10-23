<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One use of a coupon reserved for a customer between applying it in
        // the cart and placing the order. Released when it expires unused.
        Schema::create('coupon_holds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupen_id')->constrained('coupens')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('expires_at')->index();
            $table->timestamps();

            $table->unique(['coupen_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_holds');
    }
};
