<?php

namespace App\Http\Controllers\Admin;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\GeneralMail;

class ApiKeyController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * List all API key requests
     */
    public function index(Request $request)
    {
        $query = ApiKey::with(['user', 'approvedBy']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $apiKeys = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total' => ApiKey::count(),
            'pending' => ApiKey::where('status', 'pending')->count(),
            'active' => ApiKey::where('status', 'active')->count(),
            'suspended' => ApiKey::where('status', 'suspended')->count(),
            'expired' => ApiKey::where('status', 'expired')->count(),
        ];

        return view('admin.api-keys.index', compact('apiKeys', 'stats'));
    }

    /**
     * Show API key details
     */
    public function show(ApiKey $apiKey)
    {
        $apiKey->load(['user', 'approvedBy']);
        
        return view('admin.api-keys.show', compact('apiKey'));
    }

    /**
     * Approve API key request
     */
    public function approve(Request $request, ApiKey $apiKey)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
            'rate_limit' => 'sometimes|integer|min:10|max:1000',
            'expires_at' => 'sometimes|nullable|date|after:now'
        ]);

        $apiKey->update([
            'status' => 'active',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'notes' => $request->notes ?? $apiKey->notes,
            'rate_limit' => $request->rate_limit ?? $apiKey->rate_limit,
            'expires_at' => $request->expires_at ?? $apiKey->expires_at
        ]);

        // Notify user about approval
        $this->notifyUser($apiKey, 'approved');

        return redirect()->route('admin.api-keys.index')
            ->with('success', 'API key approved successfully');
    }

    /**
     * Reject API key request
     */
    public function reject(Request $request, ApiKey $apiKey)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        $apiKey->update([
            'status' => 'suspended',
            'notes' => $request->rejection_reason
        ]);

        // Notify user about rejection
        $this->notifyUser($apiKey, 'rejected', $request->rejection_reason);

        return redirect()->route('admin.api-keys.index')
            ->with('success', 'API key rejected successfully');
    }

    /**
     * Suspend API key
     */
    public function suspend(Request $request, ApiKey $apiKey)
    {
        $request->validate([
            'suspension_reason' => 'required|string|max:1000'
        ]);

        $apiKey->update([
            'status' => 'suspended',
            'notes' => $request->suspension_reason
        ]);

        // Notify user about suspension
        $this->notifyUser($apiKey, 'suspended', $request->suspension_reason);

        return redirect()->route('admin.api-keys.index')
            ->with('success', 'API key suspended successfully');
    }

    /**
     * Reactivate API key
     */
    public function reactivate(ApiKey $apiKey)
    {
        $apiKey->update([
            'status' => 'active',
            'notes' => 'Reactivated by admin'
        ]);

        // Notify user about reactivation
        $this->notifyUser($apiKey, 'reactivated');

        return redirect()->route('admin.api-keys.index')
            ->with('success', 'API key reactivated successfully');
    }

    /**
     * Delete API key
     */
    public function destroy(ApiKey $apiKey)
    {
        $apiKey->delete();

        return redirect()->route('admin.api-keys.index')
            ->with('success', 'API key deleted successfully');
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,suspend,delete',
            'api_keys' => 'required|array',
            'api_keys.*' => 'exists:api_keys,id'
        ]);

        $apiKeys = ApiKey::whereIn('id', $request->api_keys)->get();

        foreach ($apiKeys as $apiKey) {
            switch ($request->action) {
                case 'approve':
                    $apiKey->update([
                        'status' => 'active',
                        'approved_by' => Auth::id(),
                        'approved_at' => now()
                    ]);
                    $this->notifyUser($apiKey, 'approved');
                    break;

                case 'reject':
                    $apiKey->update([
                        'status' => 'suspended',
                        'notes' => 'Bulk rejected by admin'
                    ]);
                    $this->notifyUser($apiKey, 'rejected', 'Bulk rejected by admin');
                    break;

                case 'suspend':
                    $apiKey->update([
                        'status' => 'suspended',
                        'notes' => 'Bulk suspended by admin'
                    ]);
                    $this->notifyUser($apiKey, 'suspended', 'Bulk suspended by admin');
                    break;

                case 'delete':
                    $apiKey->delete();
                    break;
            }
        }

        $action = ucfirst($request->action);
        return redirect()->route('admin.api-keys.index')
            ->with('success', "{$action} completed for " . count($apiKeys) . " API keys");
    }

    /**
     * Get API key statistics
     */
    public function statistics()
    {
        $stats = [
            'total_keys' => ApiKey::count(),
            'active_keys' => ApiKey::where('status', 'active')->count(),
            'pending_requests' => ApiKey::where('status', 'pending')->count(),
            'suspended_keys' => ApiKey::where('status', 'suspended')->count(),
            'expired_keys' => ApiKey::where('status', 'expired')->count(),
            'keys_this_month' => ApiKey::whereMonth('created_at', now()->month)->count(),
            'approvals_this_month' => ApiKey::whereMonth('approved_at', now()->month)->count(),
        ];

        // Top users by API key count
        $topUsers = User::withCount('apiKeys')
            ->having('api_keys_count', '>', 0)
            ->orderBy('api_keys_count', 'desc')
            ->limit(10)
            ->get();

        // Recent activity
        $recentActivity = ApiKey::with(['user', 'approvedBy'])
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get();

        return view('admin.api-keys.statistics', compact('stats', 'topUsers', 'recentActivity'));
    }

    /**
     * Notify user about API key status change
     */
    private function notifyUser(ApiKey $apiKey, string $action, string $reason = null): void
    {
        $user = $apiKey->user;

        // In-app notification
        \App\Models\Notifications::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $user->id,
            'notification_type' => 'api_key_' . $action,
            'seen' => 2,
        ]);

        // Email notification
        try {
            $subject = match($action) {
                'approved' => 'Your API Key Request Was Approved',
                'rejected' => 'Your API Key Request Was Rejected',
                'suspended' => 'Your API Key Was Suspended',
                'reactivated' => 'Your API Key Was Reactivated',
                default => 'API Key Status Update'
            };

            $content = (object) [
                'subject' => $subject,
                'body' => view('emails.api-key-status', [
                    'apiKey' => $apiKey,
                    'user' => $user,
                    'action' => $action,
                    'reason' => $reason,
                    'admin' => Auth::user()
                ])->render(),
            ];

            Mail::to($user->email)->queue(new GeneralMail($content));
        } catch (\Throwable $e) {
            \Log::error('Failed to send API key status notification: ' . $e->getMessage());
        }
    }
}
