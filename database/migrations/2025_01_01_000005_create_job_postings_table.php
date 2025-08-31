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
        // Create job_postings table
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('company_name');
            $table->string('company_website')->nullable();
            $table->string('company_logo')->nullable();
            $table->string('job_type'); // remote, hybrid, onsite
            $table->string('employment_type'); // full-time, part-time, contract, internship
            $table->string('location')->nullable(); // For non-remote jobs
            $table->boolean('visa_sponsorship')->default(false);
            $table->string('visa_types')->nullable(); // JSON array of visa types sponsored
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->string('salary_currency', 3)->default('NGN');
            $table->string('salary_period')->default('monthly'); // hourly, monthly, yearly
            $table->json('required_skills')->nullable();
            $table->json('preferred_skills')->nullable();
            $table->text('requirements');
            $table->text('benefits')->nullable();
            $table->text('how_to_apply');
            $table->string('application_url')->nullable();
            $table->string('application_email')->nullable();
            $table->date('deadline')->nullable();
            $table->integer('views')->default(0);
            $table->integer('applications')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->string('status')->default('active'); // active, expired, filled, draft
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'published_at']);
            $table->index(['visa_sponsorship', 'status']);
            $table->index('job_type');
            $table->index('employment_type');
            $table->fullText(['title', 'description', 'requirements']);
        });

        // Create job_applications table
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_posting_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('cover_letter')->nullable();
            $table->string('resume')->nullable(); // File path
            $table->json('answers')->nullable(); // Custom screening questions
            $table->string('status')->default('pending'); // pending, reviewed, shortlisted, rejected
            $table->text('notes')->nullable(); // Recruiter notes
            $table->timestamps();
            
            $table->unique(['job_posting_id', 'user_id']);
            $table->index(['status', 'created_at']);
        });

        // Create job_saved table
        Schema::create('job_saved', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('job_posting_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['user_id', 'job_posting_id']);
        });

        // Create visa_tracking table
        Schema::create('visa_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('visa_type');
            $table->string('country');
            $table->string('status'); // planning, documents, submitted, interview, approved, rejected
            $table->date('application_date')->nullable();
            $table->date('interview_date')->nullable();
            $table->date('decision_date')->nullable();
            $table->json('timeline')->nullable(); // Array of timeline events
            $table->json('documents_checklist')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_public')->default(false); // Share timeline publicly
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['country', 'visa_type']);
        });

        // Create visa_timeline_updates table
        Schema::create('visa_timeline_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_tracking_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('status');
            $table->date('update_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_timeline_updates');
        Schema::dropIfExists('visa_trackings');
        Schema::dropIfExists('job_saved');
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('job_postings');
    }
};
