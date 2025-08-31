<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show verification status
     */
    public function index()
    {
        $user = Auth::user();
        $verificationRequest = $user->verificationRequests()->latest()->first();
        
        return view('verification.index', compact('user', 'verificationRequest'));
    }

    /**
     * Show verification request form
     */
    public function create()
    {
        $user = Auth::user();
        
        // Check if user already has a pending request
        $pendingRequest = $user->verificationRequests()
            ->where('status', 'pending')
            ->first();
            
        if ($pendingRequest) {
            return redirect()->route('verification.index')
                ->with('info', 'You already have a pending verification request.');
        }

        $verificationTypes = [
            'professional' => 'Professional Verification',
            'expert_visa' => 'Visa Expert',
            'expert_jobs' => 'Career & Jobs Expert',
            'expert_relationships' => 'Relationships Expert',
            'recruiter' => 'Verified Recruiter',
            'company' => 'Company Representative'
        ];

        return view('verification.create', compact('verificationTypes'));
    }

    /**
     * Store verification request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'verification_type' => 'required|in:professional,expert_visa,expert_jobs,expert_relationships,recruiter,company',
            'full_name' => 'required|string|max:255',
            'profession' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'linkedin_url' => 'nullable|url',
            'website_url' => 'nullable|url',
            'years_experience' => 'required|integer|min:1|max:50',
            'expertise_areas' => 'required|array|min:1',
            'expertise_areas.*' => 'string|max:100',
            'certifications' => 'nullable|string',
            'bio' => 'required|string|min:50|max:1000',
            'id_document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'professional_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'additional_documents.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $user = Auth::user();

        // Check for pending request
        if ($user->verificationRequests()->where('status', 'pending')->exists()) {
            return back()->with('error', 'You already have a pending verification request.');
        }

        // Handle file uploads
        $documents = [];
        
        if ($request->hasFile('id_document')) {
            $documents['id_document'] = $request->file('id_document')->store('verifications/id', 'private');
        }
        
        if ($request->hasFile('professional_document')) {
            $documents['professional_document'] = $request->file('professional_document')->store('verifications/professional', 'private');
        }
        
        if ($request->hasFile('additional_documents')) {
            $additionalDocs = [];
            foreach ($request->file('additional_documents') as $doc) {
                $additionalDocs[] = $doc->store('verifications/additional', 'private');
            }
            $documents['additional_documents'] = $additionalDocs;
        }

        // Create verification request
        $verificationRequest = VerificationRequest::create([
            'user_id' => $user->id,
            'verification_type' => $validated['verification_type'],
            'full_name' => $validated['full_name'],
            'profession' => $validated['profession'],
            'company' => $validated['company'],
            'linkedin_url' => $validated['linkedin_url'],
            'website_url' => $validated['website_url'],
            'years_experience' => $validated['years_experience'],
            'expertise_areas' => $validated['expertise_areas'],
            'certifications' => $validated['certifications'],
            'bio' => $validated['bio'],
            'documents' => $documents,
            'status' => 'pending',
        ]);

        // Award points for verification attempt
        $user->awardPoints('verification_attempt', 10, 'verification', $verificationRequest->id);

        // Notify admins
        // TODO: Send notification to admins

        return redirect()->route('verification.index')
            ->with('success', 'Your verification request has been submitted successfully. We will review it within 2-3 business days.');
    }

    /**
     * Admin: List all verification requests
     */
    public function adminIndex(Request $request)
    {
        $this->authorize('viewAny', VerificationRequest::class);

        $query = VerificationRequest::with('user');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('verification_type', $request->type);
        }

        $requests = $query->latest()->paginate(20);

        return view('admin.verifications.index', compact('requests'));
    }

    /**
     * Admin: Show verification request details
     */
    public function adminShow(VerificationRequest $verificationRequest)
    {
        $this->authorize('view', $verificationRequest);

        return view('admin.verifications.show', compact('verificationRequest'));
    }

    /**
     * Admin: Approve verification request
     */
    public function approve(Request $request, VerificationRequest $verificationRequest)
    {
        $this->authorize('update', $verificationRequest);

        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
            'verified_areas' => 'required|array|min:1',
            'verified_areas.*' => 'string|max:100',
        ]);

        $verificationRequest->update([
            'status' => 'approved',
            'admin_notes' => $validated['admin_notes'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Update user verification status
        $user = $verificationRequest->user;
        $user->update([
            'verification_type' => $verificationRequest->verification_type,
            'verified_at' => now(),
        ]);

        // Add expertise areas
        foreach ($validated['verified_areas'] as $area) {
            $user->expertiseAreas()->firstOrCreate([
                'name' => $area,
                'category' => $this->getCategoryFromType($verificationRequest->verification_type),
            ]);
        }

        // Award points and badge
        $user->awardPoints('verification_approved', 100, 'verification', $verificationRequest->id);
        
        // Award appropriate badge
        $badgeName = $this->getBadgeForVerificationType($verificationRequest->verification_type);
        if ($badgeName) {
            $user->awardBadge($badgeName);
        }

        // Send notification to user
        // TODO: Send email notification

        return redirect()->route('admin.verifications.index')
            ->with('success', 'Verification request approved successfully.');
    }

    /**
     * Admin: Reject verification request
     */
    public function reject(Request $request, VerificationRequest $verificationRequest)
    {
        $this->authorize('update', $verificationRequest);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $verificationRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'admin_notes' => $validated['admin_notes'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Send notification to user with rejection reason
        // TODO: Send email notification

        return redirect()->route('admin.verifications.index')
            ->with('success', 'Verification request rejected.');
    }

    /**
     * Download verification document
     */
    public function downloadDocument(VerificationRequest $verificationRequest, string $type)
    {
        $this->authorize('view', $verificationRequest);

        $documents = $verificationRequest->documents;
        
        if (!isset($documents[$type])) {
            abort(404, 'Document not found');
        }

        $path = $documents[$type];
        
        if ($type === 'additional_documents' && is_array($path)) {
            // Handle multiple additional documents
            $index = request('index', 0);
            if (!isset($path[$index])) {
                abort(404, 'Document not found');
            }
            $path = $path[$index];
        }

        if (!Storage::disk('private')->exists($path)) {
            abort(404, 'Document file not found');
        }

        return Storage::disk('private')->download($path);
    }

    /**
     * Get category from verification type
     */
    private function getCategoryFromType(string $type): string
    {
        $categories = [
            'expert_visa' => 'visa',
            'expert_jobs' => 'jobs',
            'expert_relationships' => 'relationships',
            'recruiter' => 'jobs',
            'company' => 'jobs',
            'professional' => 'general',
        ];

        return $categories[$type] ?? 'general';
    }

    /**
     * Get badge name for verification type
     */
    private function getBadgeForVerificationType(string $type): ?string
    {
        $badges = [
            'expert_visa' => 'Visa Expert',
            'expert_jobs' => 'Career Expert',
            'expert_relationships' => 'Relationship Expert',
            'recruiter' => 'Verified Recruiter',
            'company' => 'Company Representative',
            'professional' => 'Verified Professional',
        ];

        return $badges[$type] ?? null;
    }
}
