<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Local test data for reproducing order issues. NOT for production.
 *
 *   php artisan db:seed --class=DummyDataSeeder
 *
 * Creates an admin, customers, foods, time slots, pincodes, coupons, cart
 * items and orders in every status (upcoming, past cutoff, past date,
 * delivered, cancelled), with matching wallet / loyalty / notification rows.
 */
class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command->error('DummyDataSeeder refuses to run in production.');
            return;
        }

        $now   = Carbon::now();
        $today = Carbon::today();

        // ---- Admin (login OTP is emailed to this address) ----
        DB::table('admins')->updateOrInsert(
            ['email' => 'parvathykrenjith@gmail.com'],
            ['name' => 'Local Admin', 'mobile' => '9000000000', 'password' => Hash::make('password'), 'role' => 'admin', 'created_at' => $now, 'updated_at' => $now]
        );

        // ---- Settings rows the app reads ----
        if (!DB::table('loyalty_settings')->exists()) {
            DB::table('loyalty_settings')->insert(['points_per_amount' => 100, 'min_points_to_convert' => 50, 'created_at' => $now, 'updated_at' => $now]);
        }
        if (DB::getSchemaBuilder()->hasTable('referral_settings') && !DB::table('referral_settings')->exists()) {
            DB::table('referral_settings')->insert(['created_at' => $now, 'updated_at' => $now]);
        }

        // ---- Pincodes ----
        $pincodes = [
            ['682001', 'Ernakulam South', 30, 0],
            ['682016', 'Kadavanthra', 40, 10],
            ['682024', 'Edappally', 50, 0],
        ];
        foreach ($pincodes as [$code, $place, $fee, $other]) {
            DB::table('pincodes')->updateOrInsert(['pincode' => $code], [
                'place_name' => $place, 'district' => 'Ernakulam', 'state' => 'Kerala',
                'delivery_fee' => $fee, 'other_fee' => $other, 'status' => 'y', 'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        // ---- Time slots (ref1 = start hour the API filters on, cutoff = cancel/order cutoff hour) ----
        $slots = [];
        foreach ([['Breakfast', '07:00', '09:00', 7, 6], ['Lunch', '12:00', '14:00', 12, 10], ['Dinner', '19:00', '21:00', 19, 17]] as [$name, $start, $end, $ref1, $cutoff]) {
            DB::table('time_slots')->updateOrInsert(['time_slot' => $name], [
                'time_start' => $start, 'time_end' => $end, 'ref1' => $ref1, 'cutoff' => (string) $cutoff,
                'status' => 'y', 'created_at' => $now, 'updated_at' => $now,
            ]);
            $slots[$name] = DB::table('time_slots')->where('time_slot', $name)->first();
        }

        // ---- Categories + foods ----
        $catIds = [];
        foreach (['Meal Kits', 'Curries', 'Snacks'] as $i => $title) {
            DB::table('categories')->updateOrInsert(['title' => $title, 'type' => 'food'], ['status' => 'y', 'order' => $i, 'created_at' => $now, 'updated_at' => $now]);
            $catIds[$title] = DB::table('categories')->where('title', $title)->where('type', 'food')->value('id');
        }

        $foods = [
            // title, category, price, mrp, gst %, veg, offer, ref(recommended)
            ['Chicken Biryani Kit', 'Meal Kits', 249, 299, 5, 'no', 'yes', 'yes'],
            ['Veg Pulao Kit',       'Meal Kits', 179, 199, 5, 'yes', 'no', 'yes'],
            ['Kerala Fish Curry',   'Curries',   219, 249, 5, 'no', 'yes', 'no'],
            ['Paneer Butter Masala','Curries',   199, 229, 5, 'yes', 'no', 'yes'],
            ['Banana Chips 200g',   'Snacks',     89,  99, 12, 'yes', 'yes', 'no'],
            ['Chicken Cutlet (4)',  'Snacks',    129, 149, 12, 'no', 'no', 'no'],
        ];
        $foodRows = [];
        foreach ($foods as $i => [$title, $cat, $price, $mrp, $gst, $veg, $offer, $ref]) {
            DB::table('foods')->updateOrInsert(['title' => $title], [
                'type' => 'food', 'category_id' => $catIds[$cat], 'price' => $price, 'offer_price' => $price, 'mrp' => $mrp,
                'gst' => $gst, 'gst_value' => round($price * $gst / 100, 2), 'veg' => $veg, 'offer' => $offer, 'ref' => $ref,
                'calorie' => '450 kcal', 'cooking_time' => '20 min', 'taste' => 'Spicy', 'meal_type' => 'Lunch',
                'food_details' => "Dummy food item: {$title}", 'status' => 'y', 'order' => $i, 'created_at' => $now, 'updated_at' => $now,
            ]);
            $food = DB::table('foods')->where('title', $title)->first();
            DB::table('category_food')->updateOrInsert(['food_id' => $food->id, 'category_id' => $catIds[$cat]]);
            $foodRows[] = $food;
        }

        // ---- Coupons ----
        $coupons = [
            ['MAIL50',  'Email cashback',  'Get flat ₹50 cashback using Mymee email ID', 'flat',       50, 50, 199, 3],
            ['SAVE10',  '10% off',         'Get 10% off up to ₹100 on orders above ₹300', 'percentage', 10, 100, 300, 100],
            ['ONCE100', 'One-time ₹100',   'Flat ₹100 off, single use (already used)',    'flat',      100, 100, 500, 1],
        ];
        foreach ($coupons as [$code, $title, $narration, $type, $disc, $max, $min, $usage]) {
            DB::table('coupens')->updateOrInsert(['coupen_code' => $code], [
                'title' => $title, 'narration' => $narration, 'discount_type' => $type, 'discount' => $disc,
                'max_discount' => $max, 'min_amount' => $min, 'no_of_usage' => $usage,
                'from_date' => $today->copy()->subMonth()->toDateString(), 'to_date' => $today->copy()->addMonths(2)->toDateString(),
                'status' => 'y', 'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        // ---- Customers (password for all: password) ----
        $customers = [
            ['Anu Thomas',   'anu@example.test',   '9876500001', '682001'],
            ['Rahul Menon',  'rahul@example.test', '9876500002', '682016'],
            ['Fathima Rasheed', null,              '9876500003', '682024'],
        ];
        $users = [];
        foreach ($customers as $i => [$name, $email, $mobile, $pin]) {
            DB::table('users')->updateOrInsert(['mobile' => $mobile], [
                'name' => $name, 'email' => $email, 'password' => Hash::make('password'), 'status' => 1, 'approve' => 1,
                'user_type' => 'user', 'mobile_verified' => 1, 'referral_code' => 'MYME' . ($i + 1) . 'X',
                'address1' => "House {$i}, Test Street", 'pincode1' => $pin, 'landmark1' => 'Near Metro', 'active_addr' => 'Home',
                'created_at' => $now, 'updated_at' => $now,
            ]);
            $user = DB::table('users')->where('mobile', $mobile)->first();
            DB::table('addresses')->updateOrInsert(['user_id' => $user->id, 'type' => 'home'], [
                'name' => $name, 'phone' => $mobile, 'address' => "House {$i}, Test Street, Kochi", 'pincode' => $pin,
                'landmark' => 'Near Metro', 'status' => 1, 'latitude' => 9.9816358, 'longitude' => 76.2998842,
                'created_at' => $now, 'updated_at' => $now,
            ]);
            $users[] = $user;
        }

        // Orders are only (re)created once so re-running the seeder doesn't pile them up.
        if (DB::table('order_books')->where('payment_ref', 'like', 'DUMMY-%')->exists()) {
            $this->command->info('Dummy orders already exist — skipped orders. Use migrate:fresh --seed... or delete DUMMY-% orders to recreate.');
            return;
        }

        [$anu, $rahul, $fathima] = $users;

        // Opening wallet top-ups (debit = money IN to the wallet in this app).
        foreach ($users as $u) {
            DB::table('wallets')->insert(['user_id' => $u->id, 'debit' => 500, 'credit' => 0, 'date' => $today->copy()->subDays(10)->toDateString(), 'description' => 'Dummy opening balance', 'created_at' => $now, 'updated_at' => $now]);
        }

        // [user, status, delivery date, slot, [foodIndex => qty], coupon code|null, wallet used, label]
        $orders = [
            [$anu,     'order',     $today->copy()->addDay(),   'Lunch',     [0 => 2, 4 => 1], 'MAIL50', 0,  'Upcoming (tomorrow) — cancellable'],
            [$anu,     'order',     $today->copy(),             'Breakfast', [1 => 1],         null,     0,  'Today, cutoff passed after 06:00 — not cancellable'],
            [$anu,     'order',     $today->copy(),             'Dinner',    [3 => 1, 5 => 2], null,     50, 'Today dinner — cancellable before 17:00, wallet ₹50 used'],
            [$rahul,   'order',     $today->copy()->subDay(),   'Lunch',     [2 => 1],         null,     0,  'Delivery date passed but still "order" — never marked delivered'],
            [$rahul,   'delivered', $today->copy()->subDays(3), 'Dinner',    [0 => 1, 3 => 1], 'SAVE10', 0,  'Delivered'],
            [$rahul,   'cancel',    $today->copy()->addDays(2), 'Lunch',     [1 => 2],         'MAIL50', 0,  'Cancelled (refunded to wallet)'],
            [$fathima, 'delivered', $today->copy()->subDays(7), 'Lunch',     [4 => 3],         'ONCE100',0,  'Delivered, used the single-use coupon'],
            [$fathima, 'order',     $today->copy()->addDays(3), 'Dinner',    [5 => 1],         null,     0,  'Upcoming, no coupon'],
        ];

        $invoice = (int) (DB::table('order_books')->max('invoice') ?? 100);

        foreach ($orders as $n => [$user, $status, $delDate, $slotName, $items, $couponCode, $walletUsed, $label]) {
            $slot   = $slots[$slotName];
            $pin    = DB::table('pincodes')->where('pincode', $user->pincode1)->first();
            $charge = (float) $pin->delivery_fee + (float) $pin->other_fee;

            $value = 0; $gstSum = 0;
            foreach ($items as $fi => $qty) {
                $value  += $foodRows[$fi]->price * $qty;
                $gstSum += $foodRows[$fi]->gst_value * $qty;
            }

            $discount = 0;
            if ($couponCode) {
                $c = DB::table('coupens')->where('coupen_code', $couponCode)->first();
                $discount = $c->discount_type === 'percentage' ? min($value * $c->discount / 100, $c->max_discount) : (float) $c->discount;
            }

            $placedAt = $delDate->copy()->subDay()->setTime(18, 30);
            if ($placedAt->gt($now)) {
                $placedAt = $now->copy()->subHours(2);
            }

            $orderBookId = DB::table('order_books')->insertGetId([
                'user_id' => $user->id, 'customer' => $user->id,
                'invoice' => ++$invoice, 'invoice_dt' => $placedAt->toDateString(), 'finyear' => '2026-27',
                'value' => $value, 'gst_sum' => $gstSum, 'charge' => $charge, 'coupon' => $discount, 'coupon_code' => $couponCode,
                'wallet' => $walletUsed, 'payment_amount' => $value + $charge - $discount,
                'payment_status' => 'success', 'payment_mode' => $n % 2 ? 'cod' : 'online', 'payment_ref' => 'DUMMY-' . ($n + 1),
                'status' => $status, 'del_dt' => $delDate->toDateString(), 'ref' => $slot->id, 'ref1' => $slot->time_slot,
                'user' => $user->address1, 'pack_user' => 'Home',
                'delivery_address' => "{$user->address1}, Kochi", 'delivery_pincode' => $user->pincode1, 'delivery_landmark' => $user->landmark1,
                'receiver_name' => $user->name, 'receiver_phone' => $user->mobile, 'delivery_instruction' => $label,
                'date' => $placedAt, 'created_at' => $placedAt, 'updated_at' => $placedAt,
            ]);

            foreach ($items as $fi => $qty) {
                $food = $foodRows[$fi];
                $orderId = DB::table('orders')->insertGetId([
                    'food_id' => $food->id, 'user_id' => $user->id, 'order_book_id' => $orderBookId,
                    'qty' => $qty, 'price' => $food->price, 'gst_value' => $food->gst_value * $qty,
                    'total' => $food->price * $qty, 'final_price' => $food->price * $qty, 'net_price' => $food->price * $qty,
                    'date' => $delDate->toDateString(), 'time_slot' => $slot->time_slot, 'time_slot_id' => $slot->id,
                    'order_id' => $orderBookId, 'finyear' => '2026-27', 'status' => $status,
                    'created_at' => $placedAt, 'updated_at' => $placedAt,
                ]);
                DB::table('notifications')->insert([
                    'food_id' => $food->id, 'order_id' => $orderId, 'user_id' => $user->id, 'order_book_id' => $orderBookId,
                    'message' => $status === 'cancel' ? 'Product Cancelled' : 'Product Ordered', 'general' => 'no', 'status' => 'yes',
                    'created_at' => $placedAt, 'updated_at' => $placedAt,
                ]);
            }

            if ($walletUsed > 0) {
                DB::table('wallets')->insert(['user_id' => $user->id, 'debit' => 0, 'credit' => $walletUsed, 'date' => $placedAt->toDateString(), 'description' => "Used on order #{$orderBookId}", 'created_at' => $placedAt, 'updated_at' => $placedAt]);
            }
            if ($status === 'cancel') {
                DB::table('wallets')->insert(['user_id' => $user->id, 'debit' => $value + $charge - $discount, 'credit' => 0, 'date' => $placedAt->toDateString(), 'description' => "Refund for cancelled order #{$orderBookId}", 'created_at' => $placedAt, 'updated_at' => $placedAt]);
                DB::table('notifications')->insert(['user_id' => $user->id, 'order_book_id' => $orderBookId, 'message' => "{$user->name} cancelled Order #{$orderBookId}", 'general' => 'no', 'status' => 'yes', 'is_read' => 0, 'created_at' => $placedAt, 'updated_at' => $placedAt]);
            }
            if ($status === 'delivered') {
                DB::table('loyalty_transactions')->insert(['user_id' => $user->id, 'order_book_id' => $orderBookId, 'points' => (int) floor($value / 100), 'type' => 'earned', 'description' => "Earned on order #{$orderBookId}", 'created_at' => $placedAt, 'updated_at' => $placedAt]);
            }
        }

        // Items sitting in carts (not yet ordered).
        foreach ([[$anu, 2, 1], [$anu, 5, 2], [$fathima, 0, 1]] as [$user, $fi, $qty]) {
            $food = $foodRows[$fi];
            DB::table('orders')->insert([
                'food_id' => $food->id, 'user_id' => $user->id, 'qty' => $qty, 'price' => $food->price,
                'gst_value' => $food->gst_value * $qty, 'total' => $food->price * $qty, 'final_price' => $food->price * $qty,
                'status' => 'cart', 'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        $this->command->info('Dummy data created: ' . count($users) . ' customers, ' . count($foodRows) . ' foods, ' . count($orders) . ' orders, 3 cart items.');
    }
}
