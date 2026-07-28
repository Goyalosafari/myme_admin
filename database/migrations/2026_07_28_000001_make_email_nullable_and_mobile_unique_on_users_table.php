<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // doctrine/dbal isn't installed, so alter the column directly rather
        // than via Blueprint::change().
        DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) NULL');

        Schema::table('users', function (Blueprint $table) {
            $table->unique('mobile');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['mobile']);
        });

        DB::statement("UPDATE users SET email = CONCAT('deleted_', id, '@myme.local') WHERE email IS NULL");
        DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) NOT NULL');
    }
};
