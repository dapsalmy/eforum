<?php

namespace App\Http\Controllers\Api;

use App\Models\JobPosting;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class JobController extends ApiController
{
    /**
     * Get job listings
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = JobPosting::with(['user', 'category'])
            ->where('status', 'active')
            ->where('deadline', '>=', now());

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('skills', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('job_type')) {
            $query->where('job_type', $request->job_type);
        }

        if ($request->has('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        if ($request->has('is_remote')) {
            $query->where('is_remote', $request->boolean('is_remote'));
        }

        if ($request->has('has_visa_sponsorship')) {
            $query->where('visa_sponsorship', $request->boolean('has_visa_sponsorship'));
        }

        if ($request->has('min_salary')) {
            $query->where('salary_min', '>=', $request->min_salary);
        }

        if ($request->has('max_salary')) {
            $query->where('salary_max', '<=', $request->max_salary);
        }

        // Featured jobs first
        $query->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc');

        $jobs = $query->paginate($request->per_page ?? 20);

        return $this->success(
            $this->transformPagination($jobs),
            'Jobs retrieved successfully'
        );
    }

    /**
     * Get job details
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        $job = JobPosting::with(['user', 'category'])
            ->where('slug', $slug)
            ->first();

        if (!$job) {
            return $this->notFound('Job not found');
        }

        // Increment views
        $job->increment('views');

        // Check if user has applied
        $hasApplied = false;
        $isSaved = false;
        
        if ($user = auth('sanctum')->user()) {
            $hasApplied = $job->applicants()->where('user_id', $user->id)->exists();
            $isSaved = $user->savedJobs()->where('job_posting_id', $job->id)->exists();
        }

        return $this->success([
            'job' => $this->transformJob($job, true),
            'has_applied' => $hasApplied,
            'is_saved' => $isSaved,
            'similar_jobs' => $this->getSimilarJobs($job)
        ]);
    }

    /**
     * Create a new job posting
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'job_type' => 'required|in:full-time,part-time,contract,internship,freelance',
            'location' => 'nullable|string|max:255',
            'is_remote' => 'boolean',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'salary_currency' => 'nullable|string|in:NGN,USD,EUR,GBP',
            'application_link' => 'nullable|url',
            'application_email' => 'nullable|email',
            'deadline' => 'required|date|after:today',
            'visa_sponsorship' => 'boolean',
            'skills' => 'nullable|string',
            'experience_level' => 'nullable|in:entry,mid,senior,executive',
            'company_logo' => 'nullable|image|max:2048'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->toArray());
        }

        try {
            $user = $request->user();
            
            $data = $request->except('company_logo');
            $data['user_id'] = $user->id;
            $data['slug'] = Str::slug($request->title) . '-' . uniqid();
            $data['status'] = 'active';

            // Handle company logo upload
            if ($request->hasFile('company_logo')) {
                $path = $request->file('company_logo')->store('jobs/logos', 'public');
                $data['company_logo'] = $path;
            }

            $job = JobPosting::create($data);

            // Award points for posting a job
            $user->awardPoints('job_posted', 50, 'job_posting', $job->id);

            return $this->success([
                'job' => $this->transformJob($job)
            ], 'Job posted successfully', 201);

        } catch (\Exception $e) {
            return $this->serverError('Failed to create job posting');
        }
    }

    /**
     * Apply for a job
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function apply(Request $request, int $id): JsonResponse
    {
        $job = JobPosting::find($id);

        if (!$job) {
            return $this->notFound('Job not found');
        }

        if ($job->deadline < now()) {
            return $this->error('Application deadline has passed');
        }

        $user = $request->user();

        // Check if already applied
        if ($job->applicants()->where('user_id', $user->id)->exists()) {
            return $this->error('You have already applied for this job');
        }

        $validator = Validator::make($request->all(), [
            'cover_letter' => 'nullable|string|max:2000',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->toArray());
        }

        try {
            $applicationData = [
                'cover_letter' => $request->cover_letter,
                'applied_at' => now()
            ];

            // Handle resume upload
            if ($request->hasFile('resume')) {
                $path = $request->file('resume')->store('jobs/resumes', 'public');
                $applicationData['resume_path'] = $path;
            }

            $job->applicants()->attach($user->id, $applicationData);
            $job->increment('applications');

            // Send notification to job poster
            try {
                \App\Models\Notifications::create([
                    'sender_id' => $user->id,
                    'recipient_id' => $job->user_id,
                    'notification_type' => 'job_application',
                    'seen' => 2,
                ]);

                // Email notification
                $content = (object) [
                    'subject' => 'New Job Application: ' . $job->title,
                    'body' => view('emails.job-application', [
                        'job' => $job,
                        'applicant' => $user,
                        'cover_letter' => $validated['cover_letter'] ?? null,
                    ])->render(),
                ];
                \Mail::to($job->user->email)->queue(new \App\Mail\GeneralMail($content));
            } catch (\Throwable $e) {
                // Log error but don't fail the application
                \Log::error('Failed to send job application notification: ' . $e->getMessage());
            }

            return $this->success(null, 'Application submitted successfully');

        } catch (\Exception $e) {
            return $this->serverError('Failed to submit application');
        }
    }

    /**
     * Toggle save/unsave job
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function toggleSave(Request $request, int $id): JsonResponse
    {
        $job = JobPosting::find($id);

        if (!$job) {
            return $this->notFound('Job not found');
        }

        $user = $request->user();
        $isSaved = $user->savedJobs()->where('job_posting_id', $id)->exists();

        try {
            if ($isSaved) {
                $user->savedJobs()->detach($id);
                $message = 'Job removed from saved list';
            } else {
                $user->savedJobs()->attach($id);
                $message = 'Job saved successfully';
            }

            return $this->success([
                'is_saved' => !$isSaved
            ], $message);

        } catch (\Exception $e) {
            return $this->serverError('Failed to update saved status');
        }
    }

    /**
     * Get user's posted jobs
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function myJobs(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $jobs = JobPosting::where('user_id', $user->id)
            ->withCount('applicants')
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return $this->success(
            $this->transformPagination($jobs),
            'Jobs retrieved successfully'
        );
    }

    /**
     * Get user's job applications
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function myApplications(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $applications = $user->appliedJobs()
            ->with(['category', 'user'])
            ->orderBy('pivot_applied_at', 'desc')
            ->paginate($request->per_page ?? 20);

        $data = $this->transformPagination($applications);
        
        // Add application details to each job
        $data['data'] = collect($data['data'])->map(function ($job) {
            $job['application'] = [
                'applied_at' => $job['pivot']['applied_at'],
                'status' => $job['pivot']['status'] ?? 'pending',
                'cover_letter' => $job['pivot']['cover_letter']
            ];
            unset($job['pivot']);
            return $job;
        });

        return $this->success($data, 'Applications retrieved successfully');
    }

    /**
     * Get saved jobs
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function savedJobs(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $jobs = $user->savedJobs()
            ->with(['category', 'user'])
            ->where('status', 'active')
            ->orderBy('pivot_created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return $this->success(
            $this->transformPagination($jobs),
            'Saved jobs retrieved successfully'
        );
    }

    /**
     * Get job categories
     *
     * @return JsonResponse
     */
    public function categories(): JsonResponse
    {
        $categories = Category::where('status', 1)
            ->withCount(['posts' => function ($query) {
                $query->where('posts.type', 'job');
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'jobs_count' => $category->posts_count
                ];
            });

        return $this->success($categories, 'Categories retrieved successfully');
    }

    /**
     * Transform job data for API
     *
     * @param JobPosting $job
     * @param bool $detailed
     * @return array
     */
    protected function transformJob(JobPosting $job, bool $detailed = false): array
    {
        $data = [
            'id' => $job->id,
            'title' => $job->title,
            'slug' => $job->slug,
            'company_name' => $job->company_name,
            'company_logo' => $job->company_logo ? asset('storage/' . $job->company_logo) : null,
            'location' => $job->location,
            'is_remote' => $job->is_remote,
            'job_type' => $job->job_type,
            'salary_range' => $job->salary_display,
            'visa_sponsorship' => $job->visa_sponsorship,
            'deadline' => $job->deadline->toIso8601String(),
            'is_featured' => $job->is_featured,
            'created_at' => $job->created_at->toIso8601String(),
            'category' => [
                'id' => $job->category->id,
                'name' => $job->category->name,
                'slug' => $job->category->slug
            ],
            'user' => [
                'id' => $job->user->id,
                'name' => $job->user->name,
                'username' => $job->user->username,
                'avatar' => $job->user->avatar,
                'is_verified' => $job->user->isVerifiedProfessional()
            ]
        ];

        if ($detailed) {
            $data = array_merge($data, [
                'description' => $job->description,
                'requirements' => $job->requirements,
                'salary_min' => $job->salary_min,
                'salary_max' => $job->salary_max,
                'salary_currency' => $job->salary_currency,
                'application_link' => $job->application_link,
                'application_email' => $job->application_email,
                'skills' => $job->skills ? explode(',', $job->skills) : [],
                'experience_level' => $job->experience_level,
                'views' => $job->views,
                'applications' => $job->applications,
                'updated_at' => $job->updated_at->toIso8601String()
            ]);
        }

        return $data;
    }

    /**
     * Get similar jobs
     *
     * @param JobPosting $job
     * @return array
     */
    protected function getSimilarJobs(JobPosting $job): array
    {
        return JobPosting::where('id', '!=', $job->id)
            ->where(function ($query) use ($job) {
                $query->where('category_id', $job->category_id)
                    ->orWhere('job_type', $job->job_type)
                    ->orWhere('skills', 'like', "%{$job->skills}%");
            })
            ->where('status', 'active')
            ->where('deadline', '>=', now())
            ->limit(5)
            ->get()
            ->map(function ($job) {
                return $this->transformJob($job);
            })
            ->toArray();
    }
}
