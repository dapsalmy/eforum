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
        // First, let's add some fields to the categories table if they don't exist
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'icon_class')) {
                $table->string('icon_class')->nullable()->after('image');
            }
            if (!Schema::hasColumn('categories', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('status');
            }
            if (!Schema::hasColumn('categories', 'display_order')) {
                $table->integer('display_order')->default(0)->after('is_featured');
            }
        });

        // Insert eForum specific categories
        $categories = [
            [
                'name' => 'Visa & Immigration',
                'slug' => 'visa-immigration',
                'description' => 'Discuss visa applications, immigration processes, and travel documentation for Nigerians',
                'image' => 'visa-immigration.jpg',
                'icon_class' => 'fas fa-passport',
                'status' => 1,
                'is_featured' => true,
                'display_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jobs & Career',
                'slug' => 'jobs-career',
                'description' => 'Remote jobs, sponsorship opportunities, and career development for Nigerian professionals',
                'image' => 'jobs-career.jpg',
                'icon_class' => 'fas fa-briefcase',
                'status' => 1,
                'is_featured' => true,
                'display_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Relationships & Dating',
                'slug' => 'relationships-dating',
                'description' => 'Relationship advice, dating tips, and marriage discussions in the Nigerian context',
                'image' => 'relationships.jpg',
                'icon_class' => 'fas fa-heart',
                'status' => 1,
                'is_featured' => true,
                'display_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Student Visa',
                'slug' => 'student-visa',
                'description' => 'Study abroad opportunities, scholarships, and student visa guidance',
                'image' => 'student-visa.jpg',
                'icon_class' => 'fas fa-graduation-cap',
                'status' => 1,
                'is_featured' => false,
                'display_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Work Visa',
                'slug' => 'work-visa',
                'description' => 'Work permits, employment visas, and international job opportunities',
                'image' => 'work-visa.jpg',
                'icon_class' => 'fas fa-id-card',
                'status' => 1,
                'is_featured' => false,
                'display_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Remote Work',
                'slug' => 'remote-work',
                'description' => 'Remote job opportunities, freelancing, and digital nomad lifestyle',
                'image' => 'remote-work.jpg',
                'icon_class' => 'fas fa-laptop-house',
                'status' => 1,
                'is_featured' => false,
                'display_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tech Jobs',
                'slug' => 'tech-jobs',
                'description' => 'Technology careers, programming jobs, and IT opportunities',
                'image' => 'tech-jobs.jpg',
                'icon_class' => 'fas fa-code',
                'status' => 1,
                'is_featured' => false,
                'display_order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Healthcare Jobs',
                'slug' => 'healthcare-jobs',
                'description' => 'Medical, nursing, and healthcare job opportunities abroad',
                'image' => 'healthcare-jobs.jpg',
                'icon_class' => 'fas fa-stethoscope',
                'status' => 1,
                'is_featured' => false,
                'display_order' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Marriage & Family',
                'slug' => 'marriage-family',
                'description' => 'Marriage advice, family planning, and parenting discussions',
                'image' => 'marriage-family.jpg',
                'icon_class' => 'fas fa-users',
                'status' => 1,
                'is_featured' => false,
                'display_order' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'General Discussion',
                'slug' => 'general-discussion',
                'description' => 'General topics and discussions for the Nigerian community',
                'image' => 'general.jpg',
                'icon_class' => 'fas fa-comments',
                'status' => 1,
                'is_featured' => false,
                'display_order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('categories')->insert($categories);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the categories we added
        DB::table('categories')->whereIn('slug', [
            'visa-immigration',
            'jobs-career',
            'relationships-dating',
            'student-visa',
            'work-visa',
            'remote-work',
            'tech-jobs',
            'healthcare-jobs',
            'marriage-family',
            'general-discussion'
        ])->delete();

        // Remove the columns we added
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'icon_class')) {
                $table->dropColumn('icon_class');
            }
            if (Schema::hasColumn('categories', 'is_featured')) {
                $table->dropColumn('is_featured');
            }
            if (Schema::hasColumn('categories', 'display_order')) {
                $table->dropColumn('display_order');
            }
        });
    }
};
