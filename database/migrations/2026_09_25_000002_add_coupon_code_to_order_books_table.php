<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_books', function (Blueprint $table) {
            // Which coupon was applied — `coupon` only holds the discount amount,
            // so usage per coupon could not be counted before this.
            $table->string('coupon_code', 100)->nullable()->after('coupon')->index();
        });
    }

    public function down(): void
    {
        Schema::table('order_books', function (Blueprint $table) {
            $table->dropIndex(['coupon_code']);
            $table->dropColumn('coupon_code');
        });
    }
};
