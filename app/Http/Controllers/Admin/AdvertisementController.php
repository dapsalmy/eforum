<?php

namespace App\Http\Controllers\Admin;

use App\Models\Advertisement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\GeneralMail;

class AdvertisementController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * List all advertisements
     */
    public function index(Request $request)
    {
        $query = Advertisement::with(['user', 'approvedBy']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by position
        if ($request->has('position')) {
            $query->where('position', $request->position);
        }

        // Search by title
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $advertisements = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calculate statistics
        $stats = [
            'total_ads' => Advertisement::count(),
            'active_ads' => Advertisement::where('status', 'active')->count(),
            'pending_ads' => Advertisement::where('status', 'pending')->count(),
            'total_revenue' => Advertisement::sum('spent'),
        ];

        return view('admin.advertisements.index', compact('advertisements', 'stats'));
    }

    /**
     * Show advertisement details
     */
    public function show(Advertisement $advertisement)
    {
        $advertisement->load(['user', 'approvedBy']);
        return view('admin.advertisements.show', compact('advertisement'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.advertisements.create');
    }

    /**
     * Store new advertisement
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image_url' => 'nullable|url',
            'link_url' => 'required|url',
            'ad_type' => 'required|in:banner,sidebar,popup,video',
            'position' => 'required|in:top,footer,sidebar,in_content',
            'budget' => 'required|numeric|min:1|max:10000',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'permissions' => 'array',
            'rate_limit' => 'integer|min:10|max:1000',
            'notes' => 'nullable|string|max:1000'
        ]);

        Advertisement::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'description' => $request->description,
            'image_url' => $request->image_url,
            'link_url' => $request->link_url,
            'ad_type' => $request->ad_type,
            'position' => $request->position,
            'status' => 'active', // Admin-created ads are auto-approved
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'budget' => $request->budget,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'permissions' => $request->permissions ?? ['read'],
            'rate_limit' => $request->rate_limit ?? 120,
            'notes' => $request->notes
        ]);

        return redirect()->route('admin.advertisements.index')
            ->with('success', 'Advertisement created successfully');
    }

    /**
     * Show edit form
     */
    public function edit(Advertisement $advertisement)
    {
        return view('admin.advertisements.edit', compact('advertisement'));
    }

    /**
     * Update advertisement
     */
    public function update(Request $request, Advertisement $advertisement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image_url' => 'nullable|url',
            'link_url' => 'required|url',
            'ad_type' => 'required|in:banner,sidebar,popup,video',
            'position' => 'required|in:top,footer,sidebar,in_content',
            'budget' => 'required|numeric|min:1|max:10000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'permissions' => 'array',
            'rate_limit' => 'integer|min:10|max:1000',
            'notes' => 'nullable|string|max:1000'
        ]);

        $advertisement->update($request->only([
            'title', 'description', 'image_url', 'link_url', 'ad_type',
            'position', 'budget', 'start_date', 'end_date', 'permissions',
            'rate_limit', 'notes'
        ]));

        return redirect()->route('admin.advertisements.index')
            ->with('success', 'Advertisement updated successfully');
    }

    /**
     * Approve advertisement
     */
    public function approve(Request $request, Advertisement $advertisement)
    {
        $advertisement->update([
            'status' => 'active',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'notes' => $request->notes ?? $advertisement->notes
        ]);

        // Notify user about approval
        $this->notifyUser($advertisement, 'approved');

        return redirect()->route('admin.advertisements.index')
            ->with('success', 'Advertisement approved successfully');
    }

    /**
     * Reject advertisement
     */
    public function reject(Request $request, Advertisement $advertisement)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        $advertisement->update([
            'status' => 'rejected',
            'notes' => $request->rejection_reason
        ]);

        // Notify user about rejection
        $this->notifyUser($advertisement, 'rejected', $request->rejection_reason);

        return redirect()->route('admin.advertisements.index')
            ->with('success', 'Advertisement rejected successfully');
    }

    /**
     * Pause advertisement
     */
    public function pause(Advertisement $advertisement)
    {
        $advertisement->update(['status' => 'paused']);

        return redirect()->route('admin.advertisements.index')
            ->with('success', 'Advertisement paused successfully');
    }

    /**
     * Resume advertisement
     */
    public function resume(Advertisement $advertisement)
    {
        $advertisement->update(['status' => 'active']);

        return redirect()->route('admin.advertisements.index')
            ->with('success', 'Advertisement resumed successfully');
    }

    /**
     * Delete advertisement
     */
    public function destroy(Advertisement $advertisement)
    {
        $advertisement->delete();

        return redirect()->route('admin.advertisements.index')
            ->with('success', 'Advertisement deleted successfully');
    }

    /**
     * Notify user about advertisement status change
     */
    private function notifyUser(Advertisement $advertisement, string $action, string $reason = null): void
    {
        $user = $advertisement->user;

        // In-app notification
        \App\Models\Notifications::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $user->id,
            'notification_type' => 'advertisement_' . $action,
            'seen' => 2,
        ]);

        // Email notification
        try {
            $subject = match($action) {
                'approved' => 'Your Advertisement Was Approved',
                'rejected' => 'Your Advertisement Was Rejected',
                default => 'Advertisement Status Update'
            };

            $content = (object) [
                'subject' => $subject,
                'body' => view('emails.advertisement-status', [
                    'advertisement' => $advertisement,
                    'user' => $user,
                    'action' => $action,
                    'reason' => $reason,
                    'admin' => Auth::user()
                ])->render(),
            ];

            Mail::to($user->email)->queue(new GeneralMail($content));
        } catch (\Throwable $e) {
            \Log::error('Failed to send advertisement status notification: ' . $e->getMessage());
        }
    }
}
