<?php

namespace App\Http\Controllers\Api;

use App\Models\VisaTracking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class VisaController extends ApiController
{
    /**
     * Get visa trackings
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = VisaTracking::with(['user'])
            ->where('is_public', true);

        // Search filters
        if ($request->has('country')) {
            $query->where('country', $request->country);
        }

        if ($request->has('visa_type')) {
            $query->where('visa_type', 'like', "%{$request->visa_type}%");
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('year')) {
            $query->whereYear('created_at', $request->year);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $trackings = $query->paginate($request->per_page ?? 20);

        return $this->success(
            $this->transformPagination($trackings),
            'Visa trackings retrieved successfully'
        );
    }

    /**
     * Get visa tracking details
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $tracking = VisaTracking::with(['user'])->find($id);

        if (!$tracking) {
            return $this->notFound('Visa tracking not found');
        }

        // Check if private and not owner
        if (!$tracking->is_public && (!auth('sanctum')->check() || auth('sanctum')->id() !== $tracking->user_id)) {
            return $this->unauthorized('This visa tracking is private');
        }

        // Get similar trackings
        $similar = VisaTracking::where('id', '!=', $id)
            ->where('country', $tracking->country)
            ->where('is_public', true)
            ->limit(5)
            ->get();

        return $this->success([
            'tracking' => $this->transformTracking($tracking, true),
            'similar_trackings' => $similar->map(function ($item) {
                return $this->transformTracking($item);
            })
        ]);
    }

    /**
     * Create a new visa tracking
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'visa_type' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'status' => 'required|in:planning,preparing,submitted,biometrics,interview_scheduled,interview_completed,approved,rejected,on_hold',
            'application_date' => 'nullable|date',
            'interview_date' => 'nullable|date|after_or_equal:application_date',
            'decision_date' => 'nullable|date|after_or_equal:interview_date',
            'documents_checklist' => 'nullable|array',
            'documents_checklist.*.document' => 'required|string',
            'documents_checklist.*.status' => 'required|in:pending,completed,not_required',
            'notes' => 'nullable|string',
            'is_public' => 'boolean'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->toArray());
        }

        try {
            $user = $request->user();
            
            $data = $request->all();
            $data['user_id'] = $user->id;
            $data['timeline'] = []; // Initialize empty timeline

            $tracking = VisaTracking::create($data);

            // Add initial timeline event
            $this->addTimelineEvent($tracking, 'Tracking Started', 'Started tracking visa application');

            // Award points for sharing
            if ($tracking->is_public) {
                $user->awardPoints('visa_shared', 30, 'visa_tracking', $tracking->id);
            }

            return $this->success([
                'tracking' => $this->transformTracking($tracking)
            ], 'Visa tracking created successfully', 201);

        } catch (\Exception $e) {
            return $this->serverError('Failed to create visa tracking');
        }
    }

    /**
     * Update visa tracking
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $tracking = VisaTracking::find($id);

        if (!$tracking) {
            return $this->notFound('Visa tracking not found');
        }

        if ($tracking->user_id !== $request->user()->id) {
            return $this->unauthorized('You can only update your own tracking');
        }

        $validator = Validator::make($request->all(), [
            'visa_type' => 'sometimes|string|max:255',
            'country' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:planning,preparing,submitted,biometrics,interview_scheduled,interview_completed,approved,rejected,on_hold',
            'application_date' => 'nullable|date',
            'interview_date' => 'nullable|date',
            'decision_date' => 'nullable|date',
            'documents_checklist' => 'nullable|array',
            'notes' => 'nullable|string',
            'is_public' => 'boolean'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->toArray());
        }

        try {
            // Track status changes
            $oldStatus = $tracking->status;
            
            $tracking->update($request->all());

            // Add timeline event for status change
            if ($request->has('status') && $oldStatus !== $request->status) {
                $this->addTimelineEvent(
                    $tracking,
                    'Status Updated',
                    "Status changed from {$oldStatus} to {$request->status}"
                );
            }

            return $this->success([
                'tracking' => $this->transformTracking($tracking->fresh())
            ], 'Visa tracking updated successfully');

        } catch (\Exception $e) {
            return $this->serverError('Failed to update visa tracking');
        }
    }

    /**
     * Delete visa tracking
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $tracking = VisaTracking::find($id);

        if (!$tracking) {
            return $this->notFound('Visa tracking not found');
        }

        if ($tracking->user_id !== $request->user()->id) {
            return $this->unauthorized('You can only delete your own tracking');
        }

        try {
            $tracking->delete();
            return $this->success(null, 'Visa tracking deleted successfully');
        } catch (\Exception $e) {
            return $this->serverError('Failed to delete visa tracking');
        }
    }

    /**
     * Get user's visa trackings
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function myTrackings(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $trackings = VisaTracking::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return $this->success(
            $this->transformPagination($trackings),
            'Your visa trackings retrieved successfully'
        );
    }

    /**
     * Add timeline event
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function addTimelineEvent(Request $request, int $id): JsonResponse
    {
        $tracking = VisaTracking::find($id);

        if (!$tracking) {
            return $this->notFound('Visa tracking not found');
        }

        if ($tracking->user_id !== $request->user()->id) {
            return $this->unauthorized('You can only update your own tracking');
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'event' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->toArray());
        }

        try {
            $timeline = $tracking->timeline ?? [];
            $timeline[] = [
                'date' => $request->date,
                'event' => $request->event,
                'description' => $request->description
            ];

            // Sort timeline by date
            usort($timeline, function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });

            $tracking->update(['timeline' => $timeline]);

            return $this->success([
                'timeline' => $timeline
            ], 'Timeline event added successfully');

        } catch (\Exception $e) {
            return $this->serverError('Failed to add timeline event');
        }
    }

    /**
     * Update document checklist
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateChecklist(Request $request, int $id): JsonResponse
    {
        $tracking = VisaTracking::find($id);

        if (!$tracking) {
            return $this->notFound('Visa tracking not found');
        }

        if ($tracking->user_id !== $request->user()->id) {
            return $this->unauthorized('You can only update your own tracking');
        }

        $validator = Validator::make($request->all(), [
            'documents' => 'required|array',
            'documents.*.document' => 'required|string',
            'documents.*.status' => 'required|in:pending,completed,not_required'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->toArray());
        }

        try {
            $tracking->update(['documents_checklist' => $request->documents]);

            return $this->success([
                'checklist' => $request->documents
            ], 'Document checklist updated successfully');

        } catch (\Exception $e) {
            return $this->serverError('Failed to update checklist');
        }
    }

    /**
     * Get visa statistics
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function statistics(Request $request): JsonResponse
    {
        $stats = [
            'total_trackings' => VisaTracking::count(),
            'public_trackings' => VisaTracking::where('is_public', true)->count(),
            'success_rate' => $this->calculateSuccessRate(),
            'average_processing_days' => $this->calculateAverageProcessingTime(),
            'by_country' => $this->getStatsByCountry(),
            'by_visa_type' => $this->getStatsByVisaType(),
            'by_status' => $this->getStatsByStatus(),
            'recent_approvals' => $this->getRecentApprovals()
        ];

        return $this->success($stats, 'Statistics retrieved successfully');
    }

    /**
     * Transform visa tracking for API
     *
     * @param VisaTracking $tracking
     * @param bool $detailed
     * @return array
     */
    protected function transformTracking(VisaTracking $tracking, bool $detailed = false): array
    {
        $data = [
            'id' => $tracking->id,
            'visa_type' => $tracking->visa_type,
            'country' => $tracking->country,
            'status' => $tracking->status,
            'status_badge' => strip_tags($tracking->getStatusBadge()),
            'is_public' => $tracking->is_public,
            'application_date' => $tracking->application_date?->toIso8601String(),
            'interview_date' => $tracking->interview_date?->toIso8601String(),
            'decision_date' => $tracking->decision_date?->toIso8601String(),
            'processing_days' => $tracking->getProcessingDaysAttribute(),
            'progress_percentage' => $tracking->getProgressPercentageAttribute(),
            'created_at' => $tracking->created_at->toIso8601String(),
            'user' => [
                'id' => $tracking->user->id,
                'name' => $tracking->user->name,
                'username' => $tracking->user->username,
                'avatar' => $tracking->user->avatar,
                'is_verified' => $tracking->user->isVerifiedProfessional()
            ]
        ];

        if ($detailed) {
            $data = array_merge($data, [
                'timeline' => $tracking->timeline ?? [],
                'documents_checklist' => $tracking->documents_checklist ?? [],
                'notes' => $tracking->notes,
                'updated_at' => $tracking->updated_at->toIso8601String()
            ]);
        }

        return $data;
    }

    /**
     * Add timeline event helper
     *
     * @param VisaTracking $tracking
     * @param string $event
     * @param string|null $description
     */
    protected function addTimelineEvent(VisaTracking $tracking, string $event, ?string $description = null): void
    {
        $timeline = $tracking->timeline ?? [];
        $timeline[] = [
            'date' => now()->format('Y-m-d'),
            'event' => $event,
            'description' => $description
        ];
        
        $tracking->update(['timeline' => $timeline]);
    }

    /**
     * Calculate success rate
     *
     * @return float
     */
    protected function calculateSuccessRate(): float
    {
        $total = VisaTracking::whereIn('status', ['approved', 'rejected'])->count();
        if ($total === 0) return 0;
        
        $approved = VisaTracking::where('status', 'approved')->count();
        return round(($approved / $total) * 100, 1);
    }

    /**
     * Calculate average processing time
     *
     * @return int
     */
    protected function calculateAverageProcessingTime(): int
    {
        $avg = VisaTracking::whereNotNull('application_date')
            ->whereNotNull('decision_date')
            ->selectRaw('AVG(DATEDIFF(decision_date, application_date)) as avg_days')
            ->first();
            
        return (int) ($avg->avg_days ?? 0);
    }

    /**
     * Get statistics by country
     *
     * @return array
     */
    protected function getStatsByCountry(): array
    {
        return VisaTracking::select('country')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved')
            ->groupBy('country')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Get statistics by visa type
     *
     * @return array
     */
    protected function getStatsByVisaType(): array
    {
        return VisaTracking::select('visa_type')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved')
            ->groupBy('visa_type')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Get statistics by status
     *
     * @return array
     */
    protected function getStatsByStatus(): array
    {
        return VisaTracking::select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();
    }

    /**
     * Get recent approvals
     *
     * @return array
     */
    protected function getRecentApprovals(): array
    {
        return VisaTracking::where('status', 'approved')
            ->where('is_public', true)
            ->with('user')
            ->orderBy('decision_date', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($tracking) {
                return $this->transformTracking($tracking);
            })
            ->toArray();
    }
}
