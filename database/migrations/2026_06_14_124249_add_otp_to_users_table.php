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
        Schema::table('users', function (Blueprint $table) {
            $table->string('otp', 6)->nullable()->after('mobile');
            $table->timestamp('otp_expires_at')->nullable()->after('otp');
            $table->boolean('mobile_verified')->default(false)->after('otp_expires_at');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['otp', 'otp_expires_at', 'mobile_verified']);
        });
    }
};
