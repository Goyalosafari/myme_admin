<?php
// One-time migration runner — DELETE this file immediately after use!
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "<pre>";

// Add otp columns to users
if (Schema::hasTable('users')) {
    $added = [];

    if (!Schema::hasColumn('users', 'otp')) {
        Schema::table('users', function (Blueprint $table) {
            $table->string('otp', 6)->nullable()->after('mobile');
        });
        $added[] = 'otp';
    }

    if (!Schema::hasColumn('users', 'otp_expires_at')) {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('otp_expires_at')->nullable()->after('otp');
        });
        $added[] = 'otp_expires_at';
    }

    if (!Schema::hasColumn('users', 'mobile_verified')) {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('mobile_verified')->default(false)->after('otp_expires_at');
        });
        $added[] = 'mobile_verified';
    }

    if (empty($added)) {
        echo "✅ All columns already exist — nothing to do.\n";
    } else {
        echo "✅ Added columns to users table: " . implode(', ', $added) . "\n";
    }
} else {
    echo "❌ users table not found!\n";
}

echo "</pre>";
echo "<p><strong>DELETE this file from cPanel File Manager now!</strong></p>";
