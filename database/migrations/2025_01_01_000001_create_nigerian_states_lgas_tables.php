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
        // Create states table
        Schema::create('nigerian_states', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code', 3)->unique(); // e.g., LAG, ABJ, KAN
            $table->string('capital');
            $table->string('region'); // North-West, North-East, North-Central, South-West, South-East, South-South
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
            
            $table->index('name');
            $table->index('region');
        });

        // Create LGAs table
        Schema::create('nigerian_lgas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('state_id')->constrained('nigerian_states')->onDelete('cascade');
            $table->string('name');
            $table->string('code')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
            
            $table->index(['state_id', 'name']);
        });

        // Create banks table
        Schema::create('nigerian_banks', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique(); // CBN code
            $table->string('short_code', 10)->nullable(); // USSD code
            $table->string('swift_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('name');
            $table->index('code');
        });

        // Add Nigerian location fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->foreignId('state_id')->nullable()->after('address')->constrained('nigerian_states')->nullOnDelete();
            $table->foreignId('lga_id')->nullable()->after('state_id')->constrained('nigerian_lgas')->nullOnDelete();
            $table->string('phone_number')->nullable()->after('lga_id');
            $table->string('phone_country_code')->default('+234')->after('phone_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['state_id']);
            $table->dropForeign(['lga_id']);
            $table->dropColumn(['address', 'state_id', 'lga_id', 'phone_number', 'phone_country_code']);
        });
        
        Schema::dropIfExists('nigerian_banks');
        Schema::dropIfExists('nigerian_lgas');
        Schema::dropIfExists('nigerian_states');
    }
};
