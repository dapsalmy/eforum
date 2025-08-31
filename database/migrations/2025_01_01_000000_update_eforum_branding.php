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
        // Update site settings to eForum branding
        DB::table('settings')->where('type', 'site_name')->update(['value' => 'eForum']);
        DB::table('settings')->where('type', 'site_title')->update(['value' => 'eForum - Nigerian Visa, Jobs & Career Community']);
        DB::table('settings')->where('type', 'site_description')->update(['value' => 'Connect with Nigerian professionals worldwide. Get visa advice, find remote jobs with sponsorship, and build meaningful relationships.']);
        DB::table('settings')->where('type', 'site_keywords')->update(['value' => 'Nigerian forum, visa advice, remote jobs, sponsorship jobs, Nigerian community, relationships, career advice']);
        DB::table('settings')->where('type', 'contact_email')->update(['value' => 'admin@eforum.ng']);
        
        // Update Google Analytics to empty (admin can add their own)
        DB::table('settings')->where('type', 'analytics')->update(['value' => '']);
        
        // Update social login redirect URIs
        DB::table('settings')->where('type', 'google_redirect_uri')->update(['value' => 'https://eforum.ng/google/callback']);
        DB::table('settings')->where('type', 'facebook_redirect_uri')->update(['value' => 'https://eforum.ng/facebook/callback']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback to ApexForum branding (not recommended)
        DB::table('settings')->where('type', 'site_name')->update(['value' => 'ApexForum']);
        DB::table('settings')->where('type', 'site_title')->update(['value' => 'The Ultimate Forum & Community Discussions Platform']);
        DB::table('settings')->where('type', 'site_description')->update(['value' => 'Connect with fellow community users and ask questions, get answers plus also earn money through tips.']);
        DB::table('settings')->where('type', 'site_keywords')->update(['value' => 'Forum, Community, Users, Developers']);
        DB::table('settings')->where('type', 'contact_email')->update(['value' => 'admin@apexforum.com']);
    }
};
