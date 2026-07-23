<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('pincode')->nullable();
            $table->string('landmark')->nullable();
            $table->string('instruction')->nullable();
            $table->string('type')->default('home');
            $table->unsignedTinyInteger('status')->default(1);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        // Backfill from the legacy address1/address2 columns so existing users keep their addresses.
        $users = DB::table('users')->select(
            'id', 'mobile', 'address1', 'address2', 'pincode1', 'pincode2', 'landmark1', 'landmark2'
        )->get();

        foreach ($users as $user) {
            if (!empty($user->address1)) {
                DB::table('addresses')->insert([
                    'user_id'    => $user->id,
                    'phone'      => $user->mobile,
                    'address'    => $user->address1,
                    'pincode'    => $user->pincode1,
                    'landmark'   => $user->landmark1,
                    'type'       => 'home',
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if (!empty($user->address2)) {
                DB::table('addresses')->insert([
                    'user_id'    => $user->id,
                    'phone'      => $user->mobile,
                    'address'    => $user->address2,
                    'pincode'    => $user->pincode2,
                    'landmark'   => $user->landmark2,
                    'type'       => 'work',
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
