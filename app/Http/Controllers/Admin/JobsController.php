<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JobsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display job postings list
     */
    public function index(Request $request)
    {
        $query = JobPosting::with(['user', 'category']);

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by sponsorship
        if ($request->has('visa_sponsorship') && $request->visa_sponsorship !== '') {
            $query->where('visa_sponsorship', $request->visa_sponsorship);
        }

        $jobs = $query->latest()->paginate(20);

        $stats = [
            'total' => JobPosting::count(),
            'active' => JobPosting::where('status', 'active')->count(),
            'expired' => JobPosting::where('deadline', '<', now())->count(),
            'with_sponsorship' => JobPosting::where('visa_sponsorship', true)->count(),
        ];

        return view('admin.jobs.index', compact('jobs', 'stats'));
    }

    /**
     * Show job details
     */
    public function show(JobPosting $job)
    {
        $job->load(['user', 'category', 'applicants']);
        return view('admin.jobs.show', compact('job'));
    }

    /**
     * Edit job form
     */
    public function edit(JobPosting $job)
    {
        $categories = Category::where('status', 1)->get();
        return view('admin.jobs.edit', compact('job', 'categories'));
    }

    /**
     * Update job
     */
    public function update(Request $request, JobPosting $job)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:active,inactive,removed',
            'is_featured' => 'boolean',
            'visa_sponsorship' => 'boolean',
            'deadline' => 'required|date',
        ]);

        $job->update($validated);

        return redirect()->route('admin.jobs.index')
            ->with('success', 'Job posting updated successfully.');
    }

    /**
     * Delete job
     */
    public function destroy(JobPosting $job)
    {
        // Delete company logo if exists
        if ($job->company_logo) {
            Storage::disk('public')->delete($job->company_logo);
        }

        $job->delete();

        return redirect()->route('admin.jobs.index')
            ->with('success', 'Job posting deleted successfully.');
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(JobPosting $job)
    {
        $job->update(['is_featured' => !$job->is_featured]);
        
        return back()->with('success', 'Featured status updated.');
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:delete,activate,deactivate,feature,unfeature',
            'job_ids' => 'required|array',
            'job_ids.*' => 'exists:job_postings,id',
        ]);

        $jobs = JobPosting::whereIn('id', $validated['job_ids']);

        switch ($validated['action']) {
            case 'delete':
                $jobs->delete();
                $message = 'Jobs deleted successfully.';
                break;
            case 'activate':
                $jobs->update(['status' => 'active']);
                $message = 'Jobs activated successfully.';
                break;
            case 'deactivate':
                $jobs->update(['status' => 'inactive']);
                $message = 'Jobs deactivated successfully.';
                break;
            case 'feature':
                $jobs->update(['is_featured' => true]);
                $message = 'Jobs featured successfully.';
                break;
            case 'unfeature':
                $jobs->update(['is_featured' => false]);
                $message = 'Jobs unfeatured successfully.';
                break;
        }

        return back()->with('success', $message);
    }

    /**
     * Export jobs to CSV
     */
    public function export()
    {
        $jobs = JobPosting::with(['user', 'category'])->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="jobs_export_' . date('Y-m-d') . '.csv"',
        ];

        $columns = ['ID', 'Title', 'Company', 'Category', 'Posted By', 'Applications', 'Status', 'Featured', 'Visa Sponsorship', 'Created At'];
        
        $callback = function () use ($jobs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($jobs as $job) {
                fputcsv($file, [
                    $job->id,
                    $job->title,
                    $job->company_name,
                    $job->category->name,
                    $job->user->name,
                    $job->applications,
                    $job->status,
                    $job->is_featured ? 'Yes' : 'No',
                    $job->visa_sponsorship ? 'Yes' : 'No',
                    $job->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
