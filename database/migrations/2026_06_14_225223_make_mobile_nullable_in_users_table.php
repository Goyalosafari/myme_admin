<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE users MODIFY mobile VARCHAR(255) NULL');
        DB::statement('ALTER TABLE users MODIFY address1 VARCHAR(255) NULL');
        DB::statement('ALTER TABLE users MODIFY address2 VARCHAR(255) NULL');
        DB::statement('ALTER TABLE users MODIFY pincode1 VARCHAR(255) NULL');
        DB::statement('ALTER TABLE users MODIFY pincode2 VARCHAR(255) NULL');
        DB::statement('ALTER TABLE users MODIFY landmark1 VARCHAR(255) NULL');
        DB::statement('ALTER TABLE users MODIFY landmark2 VARCHAR(255) NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE users MODIFY mobile VARCHAR(255) NOT NULL');
    }
};
