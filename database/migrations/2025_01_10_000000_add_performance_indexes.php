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
        // Add indexes for frequently queried columns
        Schema::table('users', function (Blueprint $table) {
            $table->index('reputation_score');
            $table->index('trust_score');
            $table->index('created_at');
            $table->index(['is_trusted_contributor', 'reputation_score']);
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->index('created_at');
            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'status']);
        });

        Schema::table('visa_trackings', function (Blueprint $table) {
            $table->index('created_at');
            $table->index(['is_public', 'created_at']);
            $table->index('application_date');
            $table->index('decision_date');
        });

        Schema::table('job_postings', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('deadline');
            $table->index(['status', 'deadline']);
            $table->index(['is_featured', 'status', 'created_at']);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->index(['post_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['reputation_score']);
            $table->dropIndex(['trust_score']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['is_trusted_contributor', 'reputation_score']);
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['user_id', 'status']);
        });

        Schema::table('visa_trackings', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['is_public', 'created_at']);
            $table->dropIndex(['application_date']);
            $table->dropIndex(['decision_date']);
        });

        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['deadline']);
            $table->dropIndex(['status', 'deadline']);
            $table->dropIndex(['is_featured', 'status', 'created_at']);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['post_id', 'created_at']);
            $table->dropIndex(['user_id', 'created_at']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['status', 'created_at']);
        });
    }
};
