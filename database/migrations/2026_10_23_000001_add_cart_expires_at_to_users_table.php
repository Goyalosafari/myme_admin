<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // When this customer's cart is cleared (set on first add, pushed back on extend).
            $table->timestamp('cart_expires_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['cart_expires_at']);
            $table->dropColumn('cart_expires_at');
        });
    }
};
