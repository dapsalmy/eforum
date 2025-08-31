<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Models\Admin\Categories;
use App\Models\NigerianState;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\StorageService;
use App\Models\Points;

class JobPostingController extends Controller
{
    use \App\Traits\ValidatesInput;

    /**
     * Display a listing of jobs
     */
    public function index(Request $request)
    {
        $query = JobPosting::where('status', 'active')
            ->where('expires_at', '>', now())
            ->with(['user', 'category']);

        // Filters
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('job_type')) {
            $query->where('job_type', $request->job_type);
        }

        if ($request->has('visa_sponsorship')) {
            $query->where('visa_sponsorship', 1);
        }

        if ($request->has('location')) {
            $query->where('location', 'LIKE', '%' . $request->location . '%');
        }

        if ($request->has('salary_min')) {
            $query->where('salary_max', '>=', $request->salary_min);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%')
                    ->orWhere('company_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('requirements', 'LIKE', '%' . $search . '%');
            });
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'salary_high':
                $query->orderBy('salary_max', 'desc');
                break;
            case 'salary_low':
                $query->orderBy('salary_min', 'asc');
                break;
            case 'featured':
                $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $jobs = $query->paginate(20);
        $categories = Categories::whereIn('slug', ['jobs-career', 'remote-work', 'tech-jobs', 'healthcare-jobs'])->get();
        $states = NigerianState::orderBy('name')->get();

        return view('jobs.index', compact('jobs', 'categories', 'states'));
    }

    /**
     * Show job details
     */
    public function show($slug)
    {
        $job = JobPosting::where('slug', $slug)
            ->where('status', 'active')
            ->with(['user', 'category', 'savedByUsers'])
            ->firstOrFail();

        // Increment views
        $job->increment('views');

        // Related jobs
        $relatedJobs = JobPosting::where('category_id', $job->category_id)
            ->where('id', '!=', $job->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->limit(5)
            ->get();

        $isSaved = false;
        if (Auth::check()) {
            $isSaved = $job->savedByUsers()->where('user_id', Auth::id())->exists();
        }

        return view('jobs.show', compact('job', 'relatedJobs', 'isSaved'));
    }

    /**
     * Show form for creating a new job
     */
    public function create()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to post a job');
        }

        // Check if user is verified to post jobs
        if (!Auth::user()->isVerifiedProfessional() && Auth::user()->job_postings_count >= 3) {
            return redirect()->route('jobs.index')
                ->with('error', 'Please verify your professional profile to post more than 3 jobs');
        }

        $categories = Categories::whereIn('slug', ['jobs-career', 'remote-work', 'tech-jobs', 'healthcare-jobs'])->get();
        $states = NigerianState::orderBy('name')->get();

        return view('jobs.create', compact('categories', 'states'));
    }

    /**
     * Store a newly created job
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'company_name' => 'required|string|max:255',
            'company_website' => 'nullable|url',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'job_type' => 'required|in:remote,hybrid,onsite',
            'employment_type' => 'required|in:full-time,part-time,contract,internship,freelance',
            'location' => 'nullable|string|max:255',
            'visa_sponsorship' => 'boolean',
            'visa_types' => 'nullable|string|max:255',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'salary_currency' => 'required|in:NGN,USD,EUR,GBP',
            'salary_period' => 'required|in:hourly,daily,weekly,monthly,yearly',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'benefits' => 'nullable|string',
            'how_to_apply' => 'required|string',
            'application_url' => 'nullable|url',
            'application_email' => 'nullable|email',
            'deadline' => 'nullable|date|after:today',
            'required_skills' => 'nullable|array',
            'preferred_skills' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['user_id'] = Auth::id();
        $data['slug'] = Str::slug($data['title']) . '-' . Str::random(6);
        $data['status'] = 'active';
        $data['published_at'] = now();
        $data['expires_at'] = $data['deadline'] ?? now()->addDays(30);

        // Handle company logo upload
        if ($request->hasFile('company_logo')) {
            $data['company_logo'] = StorageService::upload(
                $request->file('company_logo'),
                'uploads/jobs/logos'
            );
        }

        // Convert skills arrays to JSON
        if (isset($data['required_skills'])) {
            $data['required_skills'] = json_encode($data['required_skills']);
        }
        if (isset($data['preferred_skills'])) {
            $data['preferred_skills'] = json_encode($data['preferred_skills']);
        }

        $job = JobPosting::create($data);

        // Award points for job posting
        Points::award(Auth::id(), 12, 10, 'job_posting', $job->id, 'Posted a job opportunity');

        // Update user's job posting count
        Auth::user()->increment('job_postings_count');

        return redirect()->route('jobs.show', $job->slug)
            ->with('success', 'Job posted successfully!');
    }

    /**
     * Show form for editing a job
     */
    public function edit($id)
    {
        $job = JobPosting::findOrFail($id);

        // Check ownership
        if ($job->user_id !== Auth::id() && !Auth::user()->isModerator()) {
            abort(403, 'Unauthorized action.');
        }

        $categories = Categories::whereIn('slug', ['jobs-career', 'remote-work', 'tech-jobs', 'healthcare-jobs'])->get();
        $states = NigerianState::orderBy('name')->get();

        return view('jobs.edit', compact('job', 'categories', 'states'));
    }

    /**
     * Update the specified job
     */
    public function update(Request $request, $id)
    {
        $job = JobPosting::findOrFail($id);

        // Check ownership
        if ($job->user_id !== Auth::id() && !Auth::user()->isModerator()) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'company_name' => 'required|string|max:255',
            'company_website' => 'nullable|url',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'job_type' => 'required|in:remote,hybrid,onsite',
            'employment_type' => 'required|in:full-time,part-time,contract,internship,freelance',
            'location' => 'nullable|string|max:255',
            'visa_sponsorship' => 'boolean',
            'visa_types' => 'nullable|string|max:255',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'salary_currency' => 'required|in:NGN,USD,EUR,GBP',
            'salary_period' => 'required|in:hourly,daily,weekly,monthly,yearly',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'benefits' => 'nullable|string',
            'how_to_apply' => 'required|string',
            'application_url' => 'nullable|url',
            'application_email' => 'nullable|email',
            'deadline' => 'nullable|date|after:today',
            'required_skills' => 'nullable|array',
            'preferred_skills' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        // Handle company logo upload
        if ($request->hasFile('company_logo')) {
            // Delete old logo
            if ($job->company_logo) {
                StorageService::delete($job->company_logo);
            }
            $data['company_logo'] = StorageService::upload(
                $request->file('company_logo'),
                'uploads/jobs/logos'
            );
        }

        // Convert skills arrays to JSON
        if (isset($data['required_skills'])) {
            $data['required_skills'] = json_encode($data['required_skills']);
        }
        if (isset($data['preferred_skills'])) {
            $data['preferred_skills'] = json_encode($data['preferred_skills']);
        }

        $job->update($data);

        return redirect()->route('jobs.show', $job->slug)
            ->with('success', 'Job updated successfully!');
    }

    /**
     * Remove the specified job
     */
    public function destroy($id)
    {
        $job = JobPosting::findOrFail($id);

        // Check ownership
        if ($job->user_id !== Auth::id() && !Auth::user()->isModerator()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete company logo
        if ($job->company_logo) {
            StorageService::delete($job->company_logo);
        }

        $job->delete();

        return redirect()->route('jobs.index')
            ->with('success', 'Job deleted successfully!');
    }

    /**
     * Save/unsave a job
     */
    public function toggleSave(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Please login to save jobs'], 401);
        }

        $job = JobPosting::findOrFail($request->job_id);
        
        if ($job->savedByUsers()->where('user_id', Auth::id())->exists()) {
            $job->savedByUsers()->detach(Auth::id());
            $saved = false;
        } else {
            $job->savedByUsers()->attach(Auth::id());
            $saved = true;
        }

        return response()->json([
            'success' => true,
            'saved' => $saved,
            'message' => $saved ? 'Job saved successfully' : 'Job removed from saved'
        ]);
    }

    /**
     * Apply for a job
     */
    public function apply(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to apply for jobs');
        }

        $job = JobPosting::findOrFail($id);

        // Check if already applied
        if ($job->applicants()->where('user_id', Auth::id())->exists()) {
            return redirect()->back()->with('error', 'You have already applied for this job');
        }

        // Track application
        $job->applicants()->attach(Auth::id(), [
            'applied_at' => now(),
            'status' => 'pending'
        ]);

        $job->increment('applications');

        // Send notification to job poster
        if ($job->user->email_notifications) {
            // Queue email notification
            \Mail::to($job->user->email)->queue(new \App\Mail\JobApplication($job, Auth::user()));
        }

        // Award points for applying
        Points::award(Auth::id(), 12, 2, 'job_application', $job->id, 'Applied for a job');

        return redirect()->back()->with('success', 'Application submitted successfully!');
    }

    /**
     * My posted jobs
     */
    public function myJobs()
    {
        $jobs = Auth::user()->jobPostings()
            ->withCount('applicants')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('jobs.my-jobs', compact('jobs'));
    }

    /**
     * My job applications
     */
    public function myApplications()
    {
        $applications = Auth::user()->jobApplications()
            ->with('job')
            ->orderBy('pivot_applied_at', 'desc')
            ->paginate(20);

        return view('jobs.my-applications', compact('applications'));
    }

    /**
     * View job applicants (for job poster)
     */
    public function applicants($id)
    {
        $job = JobPosting::findOrFail($id);

        // Check ownership
        if ($job->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $applicants = $job->applicants()
            ->with(['state', 'lga'])
            ->orderBy('pivot_applied_at', 'desc')
            ->paginate(20);

        return view('jobs.applicants', compact('job', 'applicants'));
    }

    /**
     * Report a job posting
     */
    public function report(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_id' => 'required|exists:job_postings,id',
            'reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $job = JobPosting::findOrFail($request->job_id);

        // Create report
        \App\Models\Reports::create([
            'reporter_id' => Auth::id(),
            'reported_id' => $job->user_id,
            'report_type' => 'job',
            'report_id' => $job->id,
            'category' => 'job_posting',
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        $this->logSecurityEvent('job_reported', [
            'job_id' => $job->id,
            'reason' => $request->reason
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Job reported successfully. Our team will review it.'
        ]);
    }
}
