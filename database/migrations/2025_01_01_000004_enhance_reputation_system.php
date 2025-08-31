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
    public function up(): void
    {
        // Enhance points table with more context
        Schema::table('points', function (Blueprint $table) {
            if (!Schema::hasColumn('points', 'context')) {
                $table->string('context')->nullable()->after('type'); // visa_help, job_referral, etc.
            }
            if (!Schema::hasColumn('points', 'related_id')) {
                $table->unsignedBigInteger('related_id')->nullable()->after('context'); // ID of related post/comment
            }
            if (!Schema::hasColumn('points', 'reason')) {
                $table->text('reason')->nullable()->after('related_id');
            }
        });

        // Create user_reputations table for aggregated reputation
        Schema::create('user_reputations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('category'); // visa_expert, job_helper, relationship_advisor
            $table->integer('score')->default(0);
            $table->integer('helpful_count')->default(0);
            $table->integer('verified_count')->default(0); // Verified helpful responses
            $table->timestamps();
            
            $table->unique(['user_id', 'category']);
            $table->index(['category', 'score']);
        });

        // Create verification_requests table
        Schema::create('verification_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('verification_type'); // professional, visa_expert, recruiter, etc.
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->json('documents')->nullable(); // Uploaded documents
            $table->json('credentials')->nullable(); // Professional credentials
            $table->text('admin_notes')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
        });

        // Create user_achievements table
        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('badge_id')->constrained()->onDelete('cascade');
            $table->string('achievement_type'); // milestone, special, contribution
            $table->json('metadata')->nullable(); // Additional achievement data
            $table->timestamp('earned_at');
            $table->timestamps();
            
            $table->unique(['user_id', 'badge_id']);
        });

        // Enhance badges table
        Schema::table('badges', function (Blueprint $table) {
            if (!Schema::hasColumn('badges', 'category')) {
                $table->string('category')->after('name'); // visa, job, relationship, general
            }
            if (!Schema::hasColumn('badges', 'description')) {
                $table->text('description')->nullable()->after('category');
            }
            if (!Schema::hasColumn('badges', 'requirements')) {
                $table->json('requirements')->nullable()->after('score'); // Requirements to earn
            }
            if (!Schema::hasColumn('badges', 'icon_class')) {
                $table->string('icon_class')->nullable()->after('image'); // FontAwesome class
            }
            if (!Schema::hasColumn('badges', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('icon_class');
            }
            if (!Schema::hasColumn('badges', 'display_order')) {
                $table->integer('display_order')->default(0)->after('is_active');
            }
        });

        // Create expertise_areas table
        Schema::create('expertise_areas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category'); // visa, job, relationship
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create user_expertise table
        Schema::create('user_expertise', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('expertise_area_id')->constrained()->onDelete('cascade');
            $table->integer('endorsement_count')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'expertise_area_id']);
        });

        // Add reputation fields to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'reputation_score')) {
                $table->integer('reputation_score')->default(0)->after('verified');
            }
            if (!Schema::hasColumn('users', 'is_trusted_contributor')) {
                $table->boolean('is_trusted_contributor')->default(false)->after('reputation_score');
            }
            if (!Schema::hasColumn('users', 'verification_type')) {
                $table->string('verification_type')->nullable()->after('is_trusted_contributor');
            }
            if (!Schema::hasColumn('users', 'verification_date')) {
                $table->timestamp('verification_date')->nullable()->after('verification_type');
            }
        });

        // Insert Nigerian-specific point types
        DB::table('settings')->insert([
            ['type' => 'points_visa_answer', 'value' => '15'],
            ['type' => 'points_job_referral', 'value' => '20'],
            ['type' => 'points_helpful_vote', 'value' => '5'],
            ['type' => 'points_best_answer', 'value' => '25'],
            ['type' => 'points_profile_verified', 'value' => '50'],
            ['type' => 'points_expertise_endorsed', 'value' => '10'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('points', function (Blueprint $table) {
            $table->dropColumn(['context', 'related_id', 'reason']);
        });

        Schema::dropIfExists('user_expertise');
        Schema::dropIfExists('expertise_areas');
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('verification_requests');
        Schema::dropIfExists('user_reputations');

        Schema::table('badges', function (Blueprint $table) {
            $table->dropColumn(['category', 'description', 'requirements', 'icon_class', 'is_active', 'display_order']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['reputation_score', 'is_trusted_contributor', 'verification_type', 'verification_date']);
        });

        DB::table('settings')->whereIn('type', [
            'points_visa_answer',
            'points_job_referral',
            'points_helpful_vote',
            'points_best_answer',
            'points_profile_verified',
            'points_expertise_endorsed',
        ])->delete();
    }
};
