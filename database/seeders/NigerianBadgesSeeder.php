<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\Badge;
use Illuminate\Support\Facades\DB;

class NigerianBadgesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            // Visa & Immigration Badges
            [
                'name' => 'Visa Guide',
                'category' => 'visa',
                'description' => 'Helped 10+ people with visa applications',
                'score' => 100,
                'requirements' => json_encode([
                    'visa_answers' => 10,
                    'helpful_votes' => 20,
                ]),
                'icon_class' => 'fas fa-passport',
                'image' => 'visa-guide.png',
                'display_order' => 1,
            ],
            [
                'name' => 'Immigration Expert',
                'category' => 'visa',
                'description' => 'Recognized expert in immigration matters',
                'score' => 500,
                'requirements' => json_encode([
                    'visa_answers' => 50,
                    'best_answers' => 10,
                    'helpful_votes' => 100,
                ]),
                'icon_class' => 'fas fa-globe-americas',
                'image' => 'immigration-expert.png',
                'display_order' => 2,
            ],
            [
                'name' => 'Japa Master',
                'category' => 'visa',
                'description' => 'Successfully relocated and helping others',
                'score' => 1000,
                'requirements' => json_encode([
                    'verified_relocation' => true,
                    'visa_success_stories' => 5,
                ]),
                'icon_class' => 'fas fa-plane-departure',
                'image' => 'japa-master.png',
                'display_order' => 3,
            ],

            // Job & Career Badges
            [
                'name' => 'Job Connector',
                'category' => 'job',
                'description' => 'Connected 5+ people with job opportunities',
                'score' => 150,
                'requirements' => json_encode([
                    'job_referrals' => 5,
                    'successful_connections' => 2,
                ]),
                'icon_class' => 'fas fa-handshake',
                'image' => 'job-connector.png',
                'display_order' => 4,
            ],
            [
                'name' => 'Remote Work Pro',
                'category' => 'job',
                'description' => 'Expert in remote work opportunities',
                'score' => 300,
                'requirements' => json_encode([
                    'remote_job_posts' => 10,
                    'helpful_career_advice' => 25,
                ]),
                'icon_class' => 'fas fa-laptop-house',
                'image' => 'remote-pro.png',
                'display_order' => 5,
            ],
            [
                'name' => 'Career Mentor',
                'category' => 'job',
                'description' => 'Provided valuable career guidance',
                'score' => 400,
                'requirements' => json_encode([
                    'career_advice_posts' => 20,
                    'mentorship_count' => 10,
                ]),
                'icon_class' => 'fas fa-user-tie',
                'image' => 'career-mentor.png',
                'display_order' => 6,
            ],

            // Relationship Badges
            [
                'name' => 'Relationship Advisor',
                'category' => 'relationship',
                'description' => 'Helpful relationship advice provider',
                'score' => 100,
                'requirements' => json_encode([
                    'relationship_posts' => 15,
                    'helpful_votes' => 30,
                ]),
                'icon_class' => 'fas fa-heart',
                'image' => 'relationship-advisor.png',
                'display_order' => 7,
            ],
            [
                'name' => 'Love Doctor',
                'category' => 'relationship',
                'description' => 'Expert in relationship matters',
                'score' => 300,
                'requirements' => json_encode([
                    'best_relationship_answers' => 10,
                    'success_stories' => 3,
                ]),
                'icon_class' => 'fas fa-stethoscope',
                'image' => 'love-doctor.png',
                'display_order' => 8,
            ],

            // General Community Badges
            [
                'name' => 'Welcome Wagon',
                'category' => 'general',
                'description' => 'Welcomed 20+ new members',
                'score' => 50,
                'requirements' => json_encode([
                    'welcome_messages' => 20,
                    'helpful_to_newbies' => true,
                ]),
                'icon_class' => 'fas fa-door-open',
                'image' => 'welcome-wagon.png',
                'display_order' => 9,
            ],
            [
                'name' => 'Community Champion',
                'category' => 'general',
                'description' => 'Outstanding community contributor',
                'score' => 500,
                'requirements' => json_encode([
                    'total_posts' => 100,
                    'helpful_votes' => 200,
                    'no_violations' => true,
                ]),
                'icon_class' => 'fas fa-trophy',
                'image' => 'community-champion.png',
                'display_order' => 10,
            ],
            [
                'name' => 'Naija Connect',
                'category' => 'general',
                'description' => 'Bringing Nigerians together globally',
                'score' => 200,
                'requirements' => json_encode([
                    'community_events' => 5,
                    'connections_made' => 20,
                ]),
                'icon_class' => 'fas fa-link',
                'image' => 'naija-connect.png',
                'display_order' => 11,
            ],
            [
                'name' => 'Verified Professional',
                'category' => 'general',
                'description' => 'Verified professional credentials',
                'score' => 0,
                'requirements' => json_encode([
                    'verification_completed' => true,
                ]),
                'icon_class' => 'fas fa-check-circle',
                'image' => 'verified-pro.png',
                'display_order' => 12,
            ],

            // Special Recognition Badges
            [
                'name' => 'Forum Legend',
                'category' => 'general',
                'description' => 'Legendary contributor to eForum',
                'score' => 2000,
                'requirements' => json_encode([
                    'years_active' => 2,
                    'reputation_score' => 2000,
                    'helped_users' => 500,
                ]),
                'icon_class' => 'fas fa-crown',
                'image' => 'forum-legend.png',
                'display_order' => 13,
            ],
            [
                'name' => 'Success Story',
                'category' => 'general',
                'description' => 'Shared inspiring success story',
                'score' => 100,
                'requirements' => json_encode([
                    'success_story_shared' => true,
                    'story_verified' => true,
                ]),
                'icon_class' => 'fas fa-star',
                'image' => 'success-story.png',
                'display_order' => 14,
            ],
        ];

        foreach ($badges as $badge) {
            Badge::create($badge);
        }

        // Insert expertise areas
        $expertiseAreas = [
            // Visa Expertise
            ['name' => 'US Visa Expert', 'slug' => 'us-visa-expert', 'category' => 'visa', 'description' => 'Expert in US visa applications (B1/B2, F1, H1B, etc.)'],
            ['name' => 'UK Visa Expert', 'slug' => 'uk-visa-expert', 'category' => 'visa', 'description' => 'Expert in UK visa applications (Visit, Student, Work)'],
            ['name' => 'Canada Immigration Expert', 'slug' => 'canada-immigration-expert', 'category' => 'visa', 'description' => 'Expert in Canadian immigration (Express Entry, PNP, Study)'],
            ['name' => 'Schengen Visa Expert', 'slug' => 'schengen-visa-expert', 'category' => 'visa', 'description' => 'Expert in Schengen/EU visa applications'],
            ['name' => 'Student Visa Specialist', 'slug' => 'student-visa-specialist', 'category' => 'visa', 'description' => 'Specialist in student visa applications worldwide'],
            
            // Job Expertise
            ['name' => 'Tech Recruiter', 'slug' => 'tech-recruiter', 'category' => 'job', 'description' => 'Technology sector recruitment specialist'],
            ['name' => 'Healthcare Recruiter', 'slug' => 'healthcare-recruiter', 'category' => 'job', 'description' => 'Healthcare sector recruitment specialist'],
            ['name' => 'Remote Work Specialist', 'slug' => 'remote-work-specialist', 'category' => 'job', 'description' => 'Expert in remote work opportunities'],
            ['name' => 'CV/Resume Expert', 'slug' => 'cv-resume-expert', 'category' => 'job', 'description' => 'Expert in CV/Resume writing and optimization'],
            ['name' => 'Interview Coach', 'slug' => 'interview-coach', 'category' => 'job', 'description' => 'Professional interview preparation coach'],
            
            // Relationship Expertise
            ['name' => 'Dating Coach', 'slug' => 'dating-coach', 'category' => 'relationship', 'description' => 'Expert in dating and relationships'],
            ['name' => 'Marriage Counselor', 'slug' => 'marriage-counselor', 'category' => 'relationship', 'description' => 'Professional marriage counselor'],
            ['name' => 'Long-Distance Relationship Expert', 'slug' => 'long-distance-expert', 'category' => 'relationship', 'description' => 'Expert in long-distance relationships'],
            ['name' => 'Cross-Cultural Relationship Expert', 'slug' => 'cross-cultural-expert', 'category' => 'relationship', 'description' => 'Expert in cross-cultural relationships'],
        ];

        DB::table('expertise_areas')->insert($expertiseAreas);
    }
}
