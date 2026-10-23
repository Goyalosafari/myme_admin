<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Google's formatted addresses / directions can exceed varchar(255), which made
// saving an address fail with a 500. Raw SQL because doctrine/dbal isn't installed.
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE addresses MODIFY address TEXT NULL, MODIFY landmark TEXT NULL, MODIFY instruction TEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE addresses MODIFY address VARCHAR(255) NULL, MODIFY landmark VARCHAR(255) NULL, MODIFY instruction VARCHAR(255) NULL');
    }
};
