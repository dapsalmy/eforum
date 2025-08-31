<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add common performance indexes if missing
        DB::statement('CREATE INDEX IF NOT EXISTS idx_users_reputation_score ON users (reputation_score)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_posts_created_at ON posts (created_at)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_visa_trackings_created_at ON visa_trackings (created_at)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_comments_post_id_created_at ON comments (post_id, created_at)');
    }

    public function down(): void
    {
        // Some DBs (e.g., MySQL) don’t support IF EXISTS for DROP INDEX with this syntax; guard with try/catch
        try { DB::statement('DROP INDEX idx_users_reputation_score ON users'); } catch (\Throwable $e) {}
        try { DB::statement('DROP INDEX idx_posts_created_at ON posts'); } catch (\Throwable $e) {}
        try { DB::statement('DROP INDEX idx_visa_trackings_created_at ON visa_trackings'); } catch (\Throwable $e) {}
        try { DB::statement('DROP INDEX idx_comments_post_id_created_at ON comments'); } catch (\Throwable $e) {}
    }
};


