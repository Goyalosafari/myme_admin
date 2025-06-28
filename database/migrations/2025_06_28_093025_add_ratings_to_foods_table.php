<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::table('foods', function (Blueprint $table) {
            $table->integer('rating_1')->nullable()->after('order');
            $table->integer('rating_2')->nullable()->after('rating_1');
            $table->integer('rating_3')->nullable()->after('rating_2');
            $table->integer('rating_4')->nullable()->after('rating_3');
            $table->integer('rating_5')->nullable()->after('rating_4');
        });
    }

    public function down()
    {
        Schema::table('foods', function (Blueprint $table) {
            $table->dropColumn(['rating_1', 'rating_2', 'rating_3', 'rating_4', 'rating_5']);
        });
    }

};
