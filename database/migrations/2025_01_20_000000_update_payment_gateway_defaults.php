<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Update existing payment gateways to be disabled by default
        DB::table('settings')->where('type', 'paypal_active')->update(['value' => 'No']);
        DB::table('settings')->where('type', 'stripe_active')->update(['value' => 'No']);
        DB::table('settings')->where('type', 'enable_paystack')->update(['value' => '0']);
        DB::table('settings')->where('type', 'enable_flutterwave')->update(['value' => '0']);
        
        // Add missing Nigerian payment gateway settings if they don't exist
        $nigerianSettings = [
            ['type' => 'paystack_public_key', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'paystack_secret_key', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'paystack_merchant_email', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'paystack_fee', 'value' => '1.5', 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'paystack_fee_cap', 'value' => '2000', 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'flutterwave_public_key', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'flutterwave_secret_key', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'flutterwave_encryption_key', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'flutterwave_fee', 'value' => '1.4', 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'flutterwave_fee_cap', 'value' => '2000', 'created_at' => now(), 'updated_at' => now()],
        ];
        
        foreach ($nigerianSettings as $setting) {
            DB::table('settings')->insertOrIgnore($setting);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Revert to previous defaults if needed (though this is generally not recommended for production)
        DB::table('settings')->where('type', 'paypal_active')->update(['value' => 'Yes']);
        DB::table('settings')->where('type', 'stripe_active')->update(['value' => 'Yes']);
        DB::table('settings')->where('type', 'enable_paystack')->update(['value' => '1']);
        DB::table('settings')->where('type', 'enable_flutterwave')->update(['value' => '1']);
    }
};
