<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// The cart timer was reverted; this removes the column the earlier
// 2026_10_23_000001 migration added (already applied on live).
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'cart_expires_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['cart_expires_at']);
                $table->dropColumn('cart_expires_at');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('cart_expires_at')->nullable()->index();
        });
    }
};
