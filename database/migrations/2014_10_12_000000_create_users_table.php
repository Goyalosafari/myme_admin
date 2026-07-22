<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('mobile')->nullable();
            $table->text('address1')->nullable();
            $table->text('address2')->nullable();
            $table->string('pincode1')->nullable();
            $table->string('pincode2')->nullable();
            $table->string('landmark1')->nullable();
            $table->string('landmark2')->nullable();
            $table->string('active_addr', 20)->default('Home');
            $table->integer('status')->nullable();
            $table->integer('order')->default(0);
            $table->integer('parent_id')->default(0);
            $table->integer('approve')->default(0);
            $table->string('user_type')->default('user');
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
