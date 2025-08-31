<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Post;
use App\Models\Comment;
use App\Models\JobPosting;
use App\Models\User;
use App\Models\ModerationAction;
use App\Models\ContentFlag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ModerationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display moderation dashboard
     */
    public function index()
    {
        $stats = [
            'pending_reports' => Report::where('status', 0)->count(),
            'resolved_reports' => Report::where('status', 1)->count(),
            'flagged_content' => ContentFlag::where('status', 'pending')->count(),
            'banned_users' => User::whereNotNull('banned_at')->count(),
            'recent_actions' => ModerationAction::with('moderator')->latest()->limit(10)->get(),
        ];

        $recentReports = Report::with(['user', 'reportable'])
            ->where('status', 0)
            ->latest()
            ->limit(10)
            ->get();

        $topReporters = User::withCount('reports')
            ->having('reports_count', '>', 0)
            ->orderBy('reports_count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.moderation.index', compact('stats', 'recentReports', 'topReporters'));
    }

    /**
     * Display all reports
     */
    public function reports(Request $request)
    {
        $query = Report::with(['user', 'reportable']);

        // Filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('reportable_type', 'App\\Models\\' . ucfirst($request->type));
        }

        if ($request->has('reason')) {
            $query->where('reason', $request->reason);
        }

        $reports = $query->latest()->paginate(20);

        return view('admin.moderation.reports', compact('reports'));
    }

    /**
     * View report details
     */
    public function showReport(Report $report)
    {
        $report->load(['user', 'reportable.user', 'moderator']);
        
        // Get similar reports for the same content
        $similarReports = Report::where('reportable_type', $report->reportable_type)
            ->where('reportable_id', $report->reportable_id)
            ->where('id', '!=', $report->id)
            ->with('user')
            ->get();

        // Get moderation history for the content author
        $contentAuthor = $report->reportable->user;
        $authorHistory = ModerationAction::where('user_id', $contentAuthor->id)
            ->with('moderator')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.moderation.report-detail', compact('report', 'similarReports', 'authorHistory'));
    }

    /**
     * Resolve report
     */
    public function resolveReport(Request $request, Report $report)
    {
        $validated = $request->validate([
            'action' => 'required|in:dismiss,warn,remove,ban',
            'moderator_notes' => 'nullable|string|max:1000',
            'ban_duration' => 'required_if:action,ban|nullable|integer|min:1',
        ]);

        DB::transaction(function () use ($report, $validated) {
            // Update report status
            $report->update([
                'status' => 1,
                'moderator_id' => Auth::id(),
                'moderator_notes' => $validated['moderator_notes'],
                'resolved_at' => now(),
            ]);

            // Mark similar reports as resolved
            Report::where('reportable_type', $report->reportable_type)
                ->where('reportable_id', $report->reportable_id)
                ->where('status', 0)
                ->update([
                    'status' => 1,
                    'moderator_id' => Auth::id(),
                    'resolved_at' => now(),
                ]);

            $contentAuthor = $report->reportable->user;

            // Create moderation action record
            $moderationAction = ModerationAction::create([
                'user_id' => $contentAuthor->id,
                'moderator_id' => Auth::id(),
                'action' => $validated['action'],
                'reason' => $report->reason,
                'content_type' => class_basename($report->reportable_type),
                'content_id' => $report->reportable_id,
                'notes' => $validated['moderator_notes'],
            ]);

            // Take action based on decision
            switch ($validated['action']) {
                case 'warn':
                    $this->warnUser($contentAuthor, $report);
                    break;
                    
                case 'remove':
                    $this->removeContent($report->reportable);
                    $this->warnUser($contentAuthor, $report, true);
                    break;
                    
                case 'ban':
                    $this->banUser($contentAuthor, $validated['ban_duration'], $report->reason);
                    $this->removeContent($report->reportable);
                    break;
            }
        });

        return redirect()->route('admin.moderation.reports')
            ->with('success', 'Report resolved successfully.');
    }

    /**
     * Content flagging system
     */
    public function flags()
    {
        $flags = ContentFlag::with(['flaggable', 'user'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        return view('admin.moderation.flags', compact('flags'));
    }

    /**
     * Auto-moderation settings
     */
    public function autoModeration()
    {
        $settings = [
            'spam_keywords' => explode(',', get_setting('spam_keywords', '')),
            'prohibited_words' => explode(',', get_setting('prohibited_words', '')),
            'auto_ban_threshold' => get_setting('auto_ban_threshold', 5),
            'suspicious_domains' => explode(',', get_setting('suspicious_domains', '')),
            'min_account_age_hours' => get_setting('min_account_age_hours', 24),
            'max_posts_per_hour' => get_setting('max_posts_per_hour', 10),
            'max_links_per_post' => get_setting('max_links_per_post', 3),
        ];

        return view('admin.moderation.auto-moderation', compact('settings'));
    }

    /**
     * Update auto-moderation settings
     */
    public function updateAutoModeration(Request $request)
    {
        $validated = $request->validate([
            'spam_keywords' => 'nullable|string',
            'prohibited_words' => 'nullable|string',
            'auto_ban_threshold' => 'required|integer|min:1|max:20',
            'suspicious_domains' => 'nullable|string',
            'min_account_age_hours' => 'required|integer|min:0|max:720',
            'max_posts_per_hour' => 'required|integer|min:1|max:100',
            'max_links_per_post' => 'required|integer|min:0|max:10',
        ]);

        foreach ($validated as $key => $value) {
            \App\Utility\SettingsUtility::save_settings($key, $value);
        }

        return back()->with('success', 'Auto-moderation settings updated successfully.');
    }

    /**
     * Banned users management
     */
    public function bannedUsers()
    {
        $bannedUsers = User::whereNotNull('banned_at')
            ->with('bans')
            ->latest('banned_at')
            ->paginate(20);

        return view('admin.moderation.banned-users', compact('bannedUsers'));
    }

    /**
     * Unban user
     */
    public function unbanUser(User $user)
    {
        $user->unban();

        ModerationAction::create([
            'user_id' => $user->id,
            'moderator_id' => Auth::id(),
            'action' => 'unban',
            'reason' => 'Manual unban by administrator',
        ]);

        return back()->with('success', 'User unbanned successfully.');
    }

    /**
     * Trusted contributors management
     */
    public function trustedContributors()
    {
        $trustedUsers = User::where('is_trusted_contributor', true)
            ->withCount(['posts', 'comments'])
            ->orderBy('reputation_score', 'desc')
            ->paginate(20);

        return view('admin.moderation.trusted-contributors', compact('trustedUsers'));
    }

    /**
     * Add trusted contributor
     */
    public function addTrustedContributor(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason' => 'required|string|max:500',
        ]);

        $user = User::find($validated['user_id']);
        $user->update(['is_trusted_contributor' => true]);

        DB::table('trusted_contributors')->insert([
            'user_id' => $user->id,
            'added_by' => Auth::id(),
            'reason' => $validated['reason'],
            'created_at' => now(),
        ]);

        return back()->with('success', 'User added as trusted contributor.');
    }

    /**
     * Remove trusted contributor status
     */
    public function removeTrustedContributor(User $user)
    {
        $user->update(['is_trusted_contributor' => false]);

        DB::table('trusted_contributors')
            ->where('user_id', $user->id)
            ->update(['removed_at' => now(), 'removed_by' => Auth::id()]);

        return back()->with('success', 'Trusted contributor status removed.');
    }

    /**
     * Moderation activity log
     */
    public function activityLog()
    {
        $activities = ModerationAction::with(['user', 'moderator'])
            ->latest()
            ->paginate(50);

        $moderatorStats = User::whereHas('moderationActions')
            ->withCount('moderationActions')
            ->orderBy('moderation_actions_count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.moderation.activity-log', compact('activities', 'moderatorStats'));
    }

    /**
     * Warn user
     */
    private function warnUser(User $user, Report $report, bool $contentRemoved = false)
    {
        // Send warning email
        Mail::to($user->email)->send(new \App\Mail\ContentModerationNotice(
            $user,
            'warning',
            $report->reason,
            class_basename($report->reportable_type),
            $contentRemoved ? null : $report->reportable->getContentPreview()
        ));
    }

    /**
     * Remove content
     */
    private function removeContent($content)
    {
        if ($content instanceof Post) {
            $content->update(['status' => 0]);
        } elseif ($content instanceof Comment) {
            $content->delete();
        } elseif ($content instanceof JobPosting) {
            $content->update(['status' => 'removed']);
        }
    }

    /**
     * Ban user
     */
    private function banUser(User $user, int $days, string $reason)
    {
        $user->ban([
            'comment' => $reason,
            'expired_at' => now()->addDays($days),
        ]);

        // Send ban notification
        Mail::to($user->email)->send(new \App\Mail\AccountBannedNotice(
            $user,
            $reason,
            $days
        ));
    }
}
