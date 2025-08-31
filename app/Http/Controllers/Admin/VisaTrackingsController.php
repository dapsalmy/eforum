<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisaTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisaTrackingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display visa trackings list
     */
    public function index(Request $request)
    {
        $query = VisaTracking::with(['user']);

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('country', 'like', "%{$search}%")
                    ->orWhere('visa_type', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by country
        if ($request->has('country') && $request->country !== '') {
            $query->where('country', $request->country);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by visibility
        if ($request->has('is_public') && $request->is_public !== '') {
            $query->where('is_public', $request->is_public);
        }

        $trackings = $query->latest()->paginate(20);

        // Get statistics
        $stats = [
            'total' => VisaTracking::count(),
            'public' => VisaTracking::where('is_public', true)->count(),
            'approved' => VisaTracking::where('status', 'approved')->count(),
            'rejected' => VisaTracking::where('status', 'rejected')->count(),
            'in_progress' => VisaTracking::whereNotIn('status', ['approved', 'rejected'])->count(),
        ];

        // Get countries for filter
        $countries = VisaTracking::select('country')
            ->distinct()
            ->orderBy('country')
            ->pluck('country');

        return view('admin.visa-trackings.index', compact('trackings', 'stats', 'countries'));
    }

    /**
     * Show visa tracking details
     */
    public function show(VisaTracking $tracking)
    {
        $tracking->load('user');
        
        // Get similar trackings
        $similar = VisaTracking::where('country', $tracking->country)
            ->where('visa_type', $tracking->visa_type)
            ->where('id', '!=', $tracking->id)
            ->limit(5)
            ->get();

        return view('admin.visa-trackings.show', compact('tracking', 'similar'));
    }

    /**
     * Edit visa tracking
     */
    public function edit(VisaTracking $tracking)
    {
        return view('admin.visa-trackings.edit', compact('tracking'));
    }

    /**
     * Update visa tracking
     */
    public function update(Request $request, VisaTracking $tracking)
    {
        $validated = $request->validate([
            'status' => 'required|in:planning,preparing,submitted,biometrics,interview_scheduled,interview_completed,approved,rejected,on_hold',
            'is_public' => 'boolean',
            'admin_notes' => 'nullable|string',
        ]);

        $tracking->update($validated);

        return redirect()->route('admin.visa-trackings.index')
            ->with('success', 'Visa tracking updated successfully.');
    }

    /**
     * Delete visa tracking
     */
    public function destroy(VisaTracking $tracking)
    {
        $tracking->delete();

        return redirect()->route('admin.visa-trackings.index')
            ->with('success', 'Visa tracking deleted successfully.');
    }

    /**
     * Toggle public visibility
     */
    public function togglePublic(VisaTracking $tracking)
    {
        $tracking->update(['is_public' => !$tracking->is_public]);
        
        return back()->with('success', 'Visibility updated.');
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:delete,make_public,make_private',
            'tracking_ids' => 'required|array',
            'tracking_ids.*' => 'exists:visa_trackings,id',
        ]);

        $trackings = VisaTracking::whereIn('id', $validated['tracking_ids']);

        switch ($validated['action']) {
            case 'delete':
                $trackings->delete();
                $message = 'Trackings deleted successfully.';
                break;
            case 'make_public':
                $trackings->update(['is_public' => true]);
                $message = 'Trackings made public successfully.';
                break;
            case 'make_private':
                $trackings->update(['is_public' => false]);
                $message = 'Trackings made private successfully.';
                break;
        }

        return back()->with('success', $message);
    }

    /**
     * Statistics page
     */
    public function statistics()
    {
        // Success rate by country
        $countryStats = VisaTracking::select('country')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved')
            ->selectRaw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected')
            ->groupBy('country')
            ->orderByDesc('total')
            ->get()
            ->map(function ($stat) {
                $stat->success_rate = $stat->total > 0 ? round(($stat->approved / $stat->total) * 100, 1) : 0;
                return $stat;
            });

        // Average processing time
        $processingTimes = VisaTracking::whereNotNull('application_date')
            ->whereNotNull('decision_date')
            ->selectRaw('country, AVG(DATEDIFF(decision_date, application_date)) as avg_days')
            ->groupBy('country')
            ->get();

        // Monthly trends
        $monthlyTrends = VisaTracking::selectRaw('YEAR(created_at) as year')
            ->selectRaw('MONTH(created_at) as month')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        // Visa type distribution
        $visaTypes = VisaTracking::select('visa_type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('visa_type')
            ->orderByDesc('count')
            ->get();

        return view('admin.visa-trackings.statistics', compact(
            'countryStats',
            'processingTimes',
            'monthlyTrends',
            'visaTypes'
        ));
    }

    /**
     * Export visa trackings to CSV
     */
    public function export()
    {
        $trackings = VisaTracking::with('user')->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="visa_trackings_export_' . date('Y-m-d') . '.csv"',
        ];

        $columns = ['ID', 'User', 'Country', 'Visa Type', 'Status', 'Application Date', 'Decision Date', 'Processing Days', 'Public', 'Created At'];
        
        $callback = function () use ($trackings, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($trackings as $tracking) {
                $processingDays = null;
                if ($tracking->application_date && $tracking->decision_date) {
                    $processingDays = $tracking->application_date->diffInDays($tracking->decision_date);
                }

                fputcsv($file, [
                    $tracking->id,
                    $tracking->user->name,
                    $tracking->country,
                    $tracking->visa_type,
                    $tracking->status,
                    $tracking->application_date?->format('Y-m-d'),
                    $tracking->decision_date?->format('Y-m-d'),
                    $processingDays,
                    $tracking->is_public ? 'Yes' : 'No',
                    $tracking->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
