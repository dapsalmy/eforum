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
        // Add Nigerian payment fields to transactions table
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'currency')) {
                $table->string('currency', 3)->default('NGN')->after('amount');
            }
            if (!Schema::hasColumn('transactions', 'gateway')) {
                $table->string('gateway')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('transactions', 'reference')) {
                $table->string('reference')->unique()->nullable()->after('transaction_id');
            }
            if (!Schema::hasColumn('transactions', 'purpose')) {
                $table->string('purpose')->nullable()->after('reference');
            }
            if (!Schema::hasColumn('transactions', 'metadata')) {
                $table->json('metadata')->nullable()->after('purpose');
            }
            if (!Schema::hasColumn('transactions', 'gateway_response')) {
                $table->json('gateway_response')->nullable()->after('metadata');
            }
        });

        // Add Nigerian payment fields to withdraws table
        Schema::table('withdraws', function (Blueprint $table) {
            if (!Schema::hasColumn('withdraws', 'bank_code')) {
                $table->string('bank_code')->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('withdraws', 'currency')) {
                $table->string('currency', 3)->default('NGN')->after('amount');
            }
            if (!Schema::hasColumn('withdraws', 'gateway')) {
                $table->string('gateway')->nullable()->after('status');
            }
            if (!Schema::hasColumn('withdraws', 'reference')) {
                $table->string('reference')->unique()->nullable()->after('gateway');
            }
            if (!Schema::hasColumn('withdraws', 'transfer_code')) {
                $table->string('transfer_code')->nullable()->after('reference');
            }
            if (!Schema::hasColumn('withdraws', 'recipient_code')) {
                $table->string('recipient_code')->nullable()->after('transfer_code');
            }
        });

        // Add currency field to other money-related tables
        Schema::table('deposits', function (Blueprint $table) {
            if (!Schema::hasColumn('deposits', 'currency')) {
                $table->string('currency', 3)->default('NGN')->after('amount');
            }
        });

        Schema::table('plans', function (Blueprint $table) {
            if (!Schema::hasColumn('plans', 'currency')) {
                $table->string('currency', 3)->default('NGN')->after('price');
            }
            if (!Schema::hasColumn('plans', 'price_ngn')) {
                $table->decimal('price_ngn', 10, 2)->nullable()->after('currency');
            }
        });

        Schema::table('buy_points', function (Blueprint $table) {
            if (!Schema::hasColumn('buy_points', 'currency')) {
                $table->string('currency', 3)->default('NGN')->after('price');
            }
            if (!Schema::hasColumn('buy_points', 'price_ngn')) {
                $table->decimal('price_ngn', 10, 2)->nullable()->after('currency');
            }
        });

        // Add Nigerian-specific settings
        DB::table('settings')->insert([
            ['type' => 'default_currency', 'value' => 'NGN'],
            ['type' => 'enable_paystack', 'value' => '1'],
            ['type' => 'enable_flutterwave', 'value' => '1'],
            ['type' => 'minimum_withdrawal_ngn', 'value' => '1000'],
            ['type' => 'maximum_withdrawal_ngn', 'value' => '1000000'],
            ['type' => 'withdrawal_fee_percentage', 'value' => '1.5'],
            ['type' => 'withdrawal_fee_cap_ngn', 'value' => '2000'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove Nigerian payment fields from transactions table
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'currency')) {
                $table->dropColumn('currency');
            }
            if (Schema::hasColumn('transactions', 'gateway')) {
                $table->dropColumn('gateway');
            }
            if (Schema::hasColumn('transactions', 'reference')) {
                $table->dropColumn('reference');
            }
            if (Schema::hasColumn('transactions', 'purpose')) {
                $table->dropColumn('purpose');
            }
            if (Schema::hasColumn('transactions', 'metadata')) {
                $table->dropColumn('metadata');
            }
            if (Schema::hasColumn('transactions', 'gateway_response')) {
                $table->dropColumn('gateway_response');
            }
        });

        // Remove Nigerian payment fields from withdraws table
        Schema::table('withdraws', function (Blueprint $table) {
            if (Schema::hasColumn('withdraws', 'bank_code')) {
                $table->dropColumn('bank_code');
            }
            if (Schema::hasColumn('withdraws', 'currency')) {
                $table->dropColumn('currency');
            }
            if (Schema::hasColumn('withdraws', 'gateway')) {
                $table->dropColumn('gateway');
            }
            if (Schema::hasColumn('withdraws', 'reference')) {
                $table->dropColumn('reference');
            }
            if (Schema::hasColumn('withdraws', 'transfer_code')) {
                $table->dropColumn('transfer_code');
            }
            if (Schema::hasColumn('withdraws', 'recipient_code')) {
                $table->dropColumn('recipient_code');
            }
        });

        // Remove currency fields from other tables
        Schema::table('deposits', function (Blueprint $table) {
            if (Schema::hasColumn('deposits', 'currency')) {
                $table->dropColumn('currency');
            }
        });

        Schema::table('plans', function (Blueprint $table) {
            if (Schema::hasColumn('plans', 'currency')) {
                $table->dropColumn('currency');
            }
            if (Schema::hasColumn('plans', 'price_ngn')) {
                $table->dropColumn('price_ngn');
            }
        });

        Schema::table('buy_points', function (Blueprint $table) {
            if (Schema::hasColumn('buy_points', 'currency')) {
                $table->dropColumn('currency');
            }
            if (Schema::hasColumn('buy_points', 'price_ngn')) {
                $table->dropColumn('price_ngn');
            }
        });

        // Remove Nigerian-specific settings
        DB::table('settings')->whereIn('type', [
            'default_currency',
            'enable_paystack',
            'enable_flutterwave',
            'minimum_withdrawal_ngn',
            'maximum_withdrawal_ngn',
            'withdrawal_fee_percentage',
            'withdrawal_fee_cap_ngn',
        ])->delete();
    }
};
