<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->string('link_url');
            $table->enum('ad_type', ['banner', 'sidebar', 'popup', 'video'])->default('banner');
            $table->enum('position', ['top', 'footer', 'sidebar', 'in_content'])->default('top');
            $table->enum('status', ['pending', 'active', 'paused', 'rejected', 'expired'])->default('pending');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->bigInteger('impressions')->default(0);
            $table->bigInteger('clicks')->default(0);
            $table->decimal('budget', 10, 2);
            $table->decimal('spent', 10, 2)->default(0);
            $table->json('target_audience')->nullable();
            $table->json('schedule')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->json('tags')->nullable();
            $table->json('device_targeting')->nullable();
            $table->json('location_targeting')->nullable();
            $table->integer('frequency_cap')->nullable();
            $table->integer('priority')->default(5);
            $table->boolean('is_featured')->default(false);
            $table->text('tracking_pixel')->nullable();
            $table->string('conversion_goal')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'start_date', 'end_date']);
            $table->index(['position', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['budget', 'spent']);
            $table->index('priority');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
