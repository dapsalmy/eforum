<?php

namespace App\Http\Controllers;

use App\Models\VisaTracking;
use App\Models\Posts;
use App\Models\Admin\Categories;
use App\Models\User;
use App\Models\Points;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VisaTrackingController extends Controller
{
    use \App\Traits\ValidatesInput;

    /**
     * Display visa tracking dashboard
     */
    public function index()
    {
        $myTrackings = collect();
        if (Auth::check()) {
            $myTrackings = Auth::user()->visaTrackings()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        // Get public visa timelines
        $publicTimelines = VisaTracking::where('is_public', true)
            ->where('status', 'approved')
            ->with('user')
            ->orderBy('decision_date', 'desc')
            ->limit(10)
            ->get();

        // Get visa statistics
        $stats = [
            'total_applications' => VisaTracking::count(),
            'approved' => VisaTracking::where('status', 'approved')->count(),
            'pending' => VisaTracking::where('status', 'submitted')->count(),
            'processing_time' => $this->getAverageProcessingTime(),
        ];

        // Popular visa types
        $popularVisaTypes = VisaTracking::select('visa_type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('visa_type')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        // Popular countries
        $popularCountries = VisaTracking::select('country')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('country')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return view('visa.index', compact(
            'myTrackings', 
            'publicTimelines', 
            'stats', 
            'popularVisaTypes',
            'popularCountries'
        ));
    }

    /**
     * Show form for creating a new visa tracking
     */
    public function create()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to track your visa application');
        }

        return view('visa.create');
    }

    /**
     * Store a newly created visa tracking
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'visa_type' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'status' => 'required|in:planning,preparing,submitted,interview_scheduled,interview_completed,approved,rejected,on_hold',
            'application_date' => 'nullable|date',
            'interview_date' => 'nullable|date|after_or_equal:application_date',
            'decision_date' => 'nullable|date|after_or_equal:interview_date',
            'notes' => 'nullable|string',
            'is_public' => 'boolean',
            'timeline' => 'nullable|array',
            'documents_checklist' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['user_id'] = Auth::id();

        // Convert arrays to JSON
        if (isset($data['timeline'])) {
            $data['timeline'] = json_encode($data['timeline']);
        }
        if (isset($data['documents_checklist'])) {
            $data['documents_checklist'] = json_encode($data['documents_checklist']);
        }

        $tracking = VisaTracking::create($data);

        // Award points for sharing visa timeline
        if ($tracking->is_public) {
            Points::award(Auth::id(), 11, 10, 'visa_timeline_shared', $tracking->id, 'Shared visa application timeline');
        }

        return redirect()->route('visa.show', $tracking->id)
            ->with('success', 'Visa tracking created successfully!');
    }

    /**
     * Display the specified visa tracking
     */
    public function show($id)
    {
        $tracking = VisaTracking::with('user')->findOrFail($id);

        // Check access
        if (!$tracking->is_public && (!Auth::check() || $tracking->user_id !== Auth::id())) {
            abort(403, 'This visa tracking is private.');
        }

        // Get similar visa applications
        $similarApplications = VisaTracking::where('id', '!=', $tracking->id)
            ->where('country', $tracking->country)
            ->where('visa_type', $tracking->visa_type)
            ->where('is_public', true)
            ->limit(5)
            ->get();

        // Forum posts related to this visa type/country
        $relatedPosts = Posts::where('status', 1)
            ->where(function($query) use ($tracking) {
                $query->where('title', 'LIKE', '%' . $tracking->visa_type . '%')
                      ->orWhere('title', 'LIKE', '%' . $tracking->country . '%')
                      ->orWhere('body', 'LIKE', '%' . $tracking->visa_type . '%')
                      ->orWhere('body', 'LIKE', '%' . $tracking->country . '%');
            })
            ->limit(5)
            ->get();

        return view('visa.show', compact('tracking', 'similarApplications', 'relatedPosts'));
    }

    /**
     * Show form for editing visa tracking
     */
    public function edit($id)
    {
        $tracking = VisaTracking::findOrFail($id);

        // Check ownership
        if ($tracking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('visa.edit', compact('tracking'));
    }

    /**
     * Update the specified visa tracking
     */
    public function update(Request $request, $id)
    {
        $tracking = VisaTracking::findOrFail($id);

        // Check ownership
        if ($tracking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'visa_type' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'status' => 'required|in:planning,preparing,submitted,interview_scheduled,interview_completed,approved,rejected,on_hold',
            'application_date' => 'nullable|date',
            'interview_date' => 'nullable|date|after_or_equal:application_date',
            'decision_date' => 'nullable|date|after_or_equal:interview_date',
            'notes' => 'nullable|string',
            'is_public' => 'boolean',
            'timeline' => 'nullable|array',
            'documents_checklist' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        // Convert arrays to JSON
        if (isset($data['timeline'])) {
            $data['timeline'] = json_encode($data['timeline']);
        }
        if (isset($data['documents_checklist'])) {
            $data['documents_checklist'] = json_encode($data['documents_checklist']);
        }

        // Award points if making public for the first time
        if (!$tracking->is_public && ($data['is_public'] ?? false)) {
            Points::award(Auth::id(), 11, 10, 'visa_timeline_shared', $tracking->id, 'Shared visa application timeline');
        }

        $tracking->update($data);

        return redirect()->route('visa.show', $tracking->id)
            ->with('success', 'Visa tracking updated successfully!');
    }

    /**
     * Remove the specified visa tracking
     */
    public function destroy($id)
    {
        $tracking = VisaTracking::findOrFail($id);

        // Check ownership
        if ($tracking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $tracking->delete();

        return redirect()->route('visa.my')
            ->with('success', 'Visa tracking deleted successfully!');
    }

    /**
     * My visa trackings
     */
    public function myTrackings()
    {
        $trackings = Auth::user()->visaTrackings()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('visa.my-trackings', compact('trackings'));
    }

    /**
     * Add timeline event
     */
    public function addTimelineEvent(Request $request, $id)
    {
        $tracking = VisaTracking::findOrFail($id);

        // Check ownership
        if ($tracking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'event' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $timeline = json_decode($tracking->timeline, true) ?? [];
        $timeline[] = [
            'date' => $request->date,
            'event' => $request->event,
            'description' => $request->description,
        ];

        // Sort timeline by date
        usort($timeline, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        $tracking->timeline = json_encode($timeline);
        $tracking->save();

        return response()->json([
            'success' => true,
            'message' => 'Timeline event added successfully'
        ]);
    }

    /**
     * Update document checklist
     */
    public function updateChecklist(Request $request, $id)
    {
        $tracking = VisaTracking::findOrFail($id);

        // Check ownership
        if ($tracking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'checklist' => 'required|array',
            'checklist.*.document' => 'required|string',
            'checklist.*.status' => 'required|in:pending,completed,not_required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tracking->documents_checklist = json_encode($request->checklist);
        $tracking->save();

        return response()->json([
            'success' => true,
            'message' => 'Document checklist updated successfully'
        ]);
    }

    /**
     * Search visa timelines
     */
    public function search(Request $request)
    {
        $query = VisaTracking::where('is_public', true)
            ->with('user');

        if ($request->has('country') && $request->country) {
            $query->where('country', $request->country);
        }

        if ($request->has('visa_type') && $request->visa_type) {
            $query->where('visa_type', 'LIKE', '%' . $request->visa_type . '%');
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('year') && $request->year) {
            $query->whereYear('application_date', $request->year);
        }

        $trackings = $query->orderBy('decision_date', 'desc')
            ->paginate(20);

        return view('visa.search', compact('trackings'));
    }

    /**
     * Visa statistics and analytics
     */
    public function statistics()
    {
        $stats = [
            'by_country' => VisaTracking::select('country')
                ->selectRaw('COUNT(*) as total')
                ->selectRaw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved')
                ->selectRaw('AVG(DATEDIFF(decision_date, application_date)) as avg_days')
                ->groupBy('country')
                ->orderBy('total', 'desc')
                ->limit(20)
                ->get(),
            
            'by_visa_type' => VisaTracking::select('visa_type')
                ->selectRaw('COUNT(*) as total')
                ->selectRaw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved')
                ->groupBy('visa_type')
                ->orderBy('total', 'desc')
                ->limit(15)
                ->get(),
            
            'by_month' => VisaTracking::selectRaw('MONTH(application_date) as month')
                ->selectRaw('YEAR(application_date) as year')
                ->selectRaw('COUNT(*) as total')
                ->whereNotNull('application_date')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->limit(12)
                ->get(),
            
            'success_rate' => $this->calculateSuccessRate(),
            'avg_processing_time' => $this->getAverageProcessingTime(),
        ];

        return view('visa.statistics', compact('stats'));
    }

    /**
     * Calculate average processing time
     */
    private function getAverageProcessingTime()
    {
        $avgDays = VisaTracking::whereNotNull('application_date')
            ->whereNotNull('decision_date')
            ->selectRaw('AVG(DATEDIFF(decision_date, application_date)) as avg_days')
            ->first();

        return $avgDays ? round($avgDays->avg_days) : 0;
    }

    /**
     * Calculate success rate
     */
    private function calculateSuccessRate()
    {
        $total = VisaTracking::whereIn('status', ['approved', 'rejected'])->count();
        $approved = VisaTracking::where('status', 'approved')->count();

        return $total > 0 ? round(($approved / $total) * 100, 1) : 0;
    }
}
