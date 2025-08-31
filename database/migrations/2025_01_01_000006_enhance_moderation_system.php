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
        // Enhance reports table
        Schema::table('reports', function (Blueprint $table) {
            if (!Schema::hasColumn('reports', 'category')) {
                $table->string('category')->after('report_id'); // spam, abuse, misinformation, etc.
            }
            if (!Schema::hasColumn('reports', 'reason')) {
                $table->text('reason')->nullable()->after('category');
            }
            if (!Schema::hasColumn('reports', 'status')) {
                $table->string('status')->default('pending')->after('reason'); // pending, reviewed, resolved, dismissed
            }
            if (!Schema::hasColumn('reports', 'moderator_id')) {
                $table->foreignId('moderator_id')->nullable()->after('status')->constrained('users');
            }
            if (!Schema::hasColumn('reports', 'moderator_notes')) {
                $table->text('moderator_notes')->nullable()->after('moderator_id');
            }
            if (!Schema::hasColumn('reports', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('moderator_notes');
            }
            
            $table->index(['status', 'created_at']);
        });

        // Create moderation_actions table
        Schema::create('moderation_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moderator_id')->constrained('users');
            $table->morphs('actionable'); // Can be post, comment, reply, user
            $table->string('action'); // warn, hide, delete, ban, restore
            $table->text('reason');
            $table->json('metadata')->nullable(); // Additional action data
            $table->timestamps();
            
            $table->index(['moderator_id', 'created_at']);
        });

        // Create content_flags table for automated flagging
        Schema::create('content_flags', function (Blueprint $table) {
            $table->id();
            $table->morphs('flaggable'); // Can be post, comment, reply
            $table->string('flag_type'); // spam, keyword, pattern, frequency
            $table->integer('severity')->default(1); // 1-5 scale
            $table->json('details')->nullable(); // What triggered the flag
            $table->boolean('is_resolved')->default(false);
            $table->foreignId('resolved_by')->nullable()->constrained('users');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            $table->index(['flag_type', 'is_resolved']);
            $table->index(['severity', 'created_at']);
        });

        // Create trusted_contributors table
        Schema::create('trusted_contributors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('level'); // bronze, silver, gold
            $table->text('reason'); // Why they were made trusted
            $table->foreignId('approved_by')->constrained('users');
            $table->json('privileges')->nullable(); // Special permissions
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->unique('user_id');
            $table->index(['level', 'is_active']);
        });

        // Create moderation_queue table
        Schema::create('moderation_queue', function (Blueprint $table) {
            $table->id();
            $table->morphs('content'); // Can be post, comment, reply, user
            $table->string('queue_type'); // auto_flagged, reported, review
            $table->integer('priority')->default(1); // 1-5, higher is more urgent
            $table->json('flags')->nullable(); // All flags/reports for this content
            $table->string('status')->default('pending'); // pending, reviewing, resolved
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->index(['queue_type', 'status', 'priority']);
            $table->index(['assigned_to', 'status']);
        });

        // Create banned_keywords table
        Schema::create('banned_keywords', function (Blueprint $table) {
            $table->id();
            $table->string('keyword');
            $table->string('category'); // spam, abuse, adult, etc.
            $table->integer('severity')->default(1); // 1-5
            $table->string('action')->default('flag'); // flag, hide, block
            $table->boolean('is_regex')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['keyword', 'is_active']);
            $table->index('category');
        });

        // Create moderation_rules table
        Schema::create('moderation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('rule_type'); // spam_detection, new_user_limit, etc.
            $table->json('conditions'); // Rule conditions
            $table->json('actions'); // What to do when triggered
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(1);
            $table->timestamps();
            
            $table->index(['rule_type', 'is_active']);
        });

        // Add moderation fields to users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'trust_score')) {
                $table->integer('trust_score')->default(100)->after('reputation_score'); // 0-100
            }
            if (!Schema::hasColumn('users', 'violation_count')) {
                $table->integer('violation_count')->default(0)->after('trust_score');
            }
            if (!Schema::hasColumn('users', 'last_violation_at')) {
                $table->timestamp('last_violation_at')->nullable()->after('violation_count');
            }
        });

        // Insert default moderation settings
        DB::table('settings')->insert([
            ['type' => 'auto_moderation_enabled', 'value' => '1'],
            ['type' => 'new_user_post_limit', 'value' => '5'], // Per day
            ['type' => 'spam_detection_threshold', 'value' => '3'], // Reports before auto-hide
            ['type' => 'trust_score_threshold', 'value' => '50'], // Below this requires moderation
        ]);

        // Insert Nigerian-specific banned keywords
        $nigerianKeywords = [
            ['keyword' => '419', 'category' => 'scam', 'severity' => 5, 'action' => 'block'],
            ['keyword' => 'yahoo boy', 'category' => 'scam', 'severity' => 4, 'action' => 'flag'],
            ['keyword' => 'runs girl', 'category' => 'inappropriate', 'severity' => 3, 'action' => 'flag'],
            ['keyword' => 'japa scam', 'category' => 'scam', 'severity' => 4, 'action' => 'flag'],
        ];

        DB::table('banned_keywords')->insert($nigerianKeywords);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['category', 'reason', 'status', 'moderator_id', 'moderator_notes', 'resolved_at']);
        });

        Schema::dropIfExists('moderation_rules');
        Schema::dropIfExists('banned_keywords');
        Schema::dropIfExists('moderation_queue');
        Schema::dropIfExists('trusted_contributors');
        Schema::dropIfExists('content_flags');
        Schema::dropIfExists('moderation_actions');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['trust_score', 'violation_count', 'last_violation_at']);
        });

        DB::table('settings')->whereIn('type', [
            'auto_moderation_enabled',
            'new_user_post_limit',
            'spam_detection_threshold',
            'trust_score_threshold',
        ])->delete();
    }
};
