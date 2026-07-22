<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('food_id');
            $table->unsignedBigInteger('order_book_id')->nullable()->after('order_id');
            $table->boolean('is_read')->default(false)->after('status');

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('order_book_id')->references('id')->on('order_books')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['order_book_id']);
            $table->dropColumn(['user_id', 'order_book_id', 'is_read']);
        });
    }
};
