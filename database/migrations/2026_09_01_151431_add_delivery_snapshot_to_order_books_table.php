<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Root-cause fix for delivery address being lost between checkout and the
// backend order record: order_books had nowhere to durably store the address
// actually selected for an order — only a free-text `user`/`pack_user` pair
// that the checkout flow doesn't even populate consistently, and no pincode,
// landmark, lat/long, receiver name/phone, or delivery instructions at all.
// The admin Order Details page was falling back to the customer's live
// profile fields instead (which are usually empty and can drift after the
// order was placed) because there was nothing else to show.
return new class extends Migration
{
    public function up()
    {
        Schema::table('order_books', function (Blueprint $table) {
            $table->text('delivery_address')->nullable()->after('user');
            $table->string('delivery_pincode', 20)->nullable()->after('delivery_address');
            $table->string('delivery_landmark', 255)->nullable()->after('delivery_pincode');
            $table->string('delivery_latitude', 50)->nullable()->after('delivery_landmark');
            $table->string('delivery_longitude', 50)->nullable()->after('delivery_latitude');
            $table->string('receiver_name', 255)->nullable()->after('delivery_longitude');
            $table->string('receiver_phone', 20)->nullable()->after('receiver_name');
            $table->text('delivery_instruction')->nullable()->after('receiver_phone');
        });
    }

    public function down()
    {
        Schema::table('order_books', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_address',
                'delivery_pincode',
                'delivery_landmark',
                'delivery_latitude',
                'delivery_longitude',
                'receiver_name',
                'receiver_phone',
                'delivery_instruction',
            ]);
        });
    }
};
