<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_impressions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertisement_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->text('referrer')->nullable();
            $table->text('page_url');
            $table->string('position');
            $table->enum('device_type', ['desktop', 'mobile', 'tablet'])->default('desktop');
            $table->string('location')->nullable();
            $table->string('session_id')->nullable();
            $table->enum('impression_type', ['view', 'click'])->default('view');
            $table->decimal('revenue', 8, 4)->default(0);
            $table->boolean('is_bot')->default(false);
            $table->timestamps();

            $table->index(['advertisement_id', 'impression_type']);
            $table->index(['user_id', 'created_at']);
            $table->index(['ip_address', 'created_at']);
            $table->index(['created_at']);
            $table->index('impression_type');
            $table->index('device_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_impressions');
    }
};
