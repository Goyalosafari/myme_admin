<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupens', function (Blueprint $table) {
            // Customer-facing description, e.g. "Get flat ₹50 cashback using Mymee email ID"
            $table->text('narration')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('coupens', function (Blueprint $table) {
            $table->dropColumn('narration');
        });
    }
};
