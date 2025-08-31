<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserReputation;
use App\Models\Points;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReputationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show user reputation dashboard
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get reputation breakdown by category
        $reputationByCategory = $user->reputations()
            ->select('category', DB::raw('SUM(points) as total_points'))
            ->groupBy('category')
            ->get();

        // Get recent reputation changes
        $recentChanges = $user->reputations()
            ->with('relatedPost', 'relatedComment', 'giver')
            ->latest()
            ->limit(20)
            ->get();

        // Get user's rank
        $userRank = User::where('reputation_score', '>', $user->reputation_score)->count() + 1;
        $totalUsers = User::count();

        // Get milestones
        $milestones = $this->getReputationMilestones($user->reputation_score);
        $nextMilestone = $this->getNextMilestone($user->reputation_score);

        // Get top contributors in each category
        $topContributors = [
            'visa' => $this->getTopContributors('visa', 5),
            'jobs' => $this->getTopContributors('jobs', 5),
            'relationships' => $this->getTopContributors('relationships', 5),
        ];

        return view('reputation.index', compact(
            'user',
            'reputationByCategory',
            'recentChanges',
            'userRank',
            'totalUsers',
            'milestones',
            'nextMilestone',
            'topContributors'
        ));
    }

    /**
     * Show reputation leaderboard
     */
    public function leaderboard(Request $request)
    {
        $period = $request->get('period', 'all'); // all, month, week
        $category = $request->get('category', 'all'); // all, visa, jobs, relationships

        $query = User::query();

        // Filter by time period
        if ($period === 'month') {
            $query->whereHas('reputations', function ($q) {
                $q->where('created_at', '>=', now()->startOfMonth());
            });
        } elseif ($period === 'week') {
            $query->whereHas('reputations', function ($q) {
                $q->where('created_at', '>=', now()->startOfWeek());
            });
        }

        // Filter by category
        if ($category !== 'all') {
            $query->withSum(['reputations as category_reputation' => function ($q) use ($category) {
                $q->where('category', $category);
            }], 'points')
            ->orderBy('category_reputation', 'desc');
        } else {
            $query->orderBy('reputation_score', 'desc');
        }

        $leaders = $query->with('state')
            ->where('reputation_score', '>', 0)
            ->paginate(50);

        return view('reputation.leaderboard', compact('leaders', 'period', 'category'));
    }

    /**
     * Award reputation points (AJAX)
     */
    public function award(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason' => 'required|in:helpful_answer,best_answer,quality_content,expertise',
            'content_type' => 'required|in:post,comment,job,visa',
            'content_id' => 'required|integer',
            'category' => 'required|in:visa,jobs,relationships,general',
        ]);

        $giver = Auth::user();
        $receiver = User::find($validated['user_id']);

        // Check if user is trying to award themselves
        if ($giver->id === $receiver->id) {
            return response()->json(['error' => 'You cannot award reputation to yourself'], 403);
        }

        // Check if already awarded for this content
        $existing = UserReputation::where('user_id', $receiver->id)
            ->where('giver_id', $giver->id)
            ->where('related_type', $validated['content_type'])
            ->where('related_id', $validated['content_id'])
            ->where('reason', $validated['reason'])
            ->first();

        if ($existing) {
            return response()->json(['error' => 'You have already awarded reputation for this content'], 422);
        }

        // Determine points based on reason
        $points = $this->getPointsForReason($validated['reason']);

        // Create reputation entry
        $reputation = UserReputation::create([
            'user_id' => $receiver->id,
            'giver_id' => $giver->id,
            'points' => $points,
            'reason' => $validated['reason'],
            'category' => $validated['category'],
            'related_type' => $validated['content_type'],
            'related_id' => $validated['content_id'],
        ]);

        // Update user's total reputation
        $receiver->increment('reputation_score', $points);

        // Update category-specific reputation
        $this->updateCategoryReputation($receiver, $validated['category'], $points);

        // Check for milestones and badges
        $this->checkMilestones($receiver);

        // Award points to giver for engaging
        $giver->awardPoints('reputation_given', 5, 'reputation', $reputation->id);

        return response()->json([
            'success' => true,
            'message' => 'Reputation awarded successfully',
            'points' => $points,
            'new_total' => $receiver->reputation_score,
        ]);
    }

    /**
     * Endorse user expertise (AJAX)
     */
    public function endorse(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'expertise_area_id' => 'required|exists:expertise_areas,id',
        ]);

        $endorser = Auth::user();
        $user = User::find($validated['user_id']);

        // Check if user is trying to endorse themselves
        if ($endorser->id === $user->id) {
            return response()->json(['error' => 'You cannot endorse yourself'], 403);
        }

        // Check if already endorsed
        $existing = DB::table('expertise_endorsements')
            ->where('expertise_area_id', $validated['expertise_area_id'])
            ->where('endorser_id', $endorser->id)
            ->first();

        if ($existing) {
            return response()->json(['error' => 'You have already endorsed this expertise'], 422);
        }

        // Create endorsement
        DB::table('expertise_endorsements')->insert([
            'expertise_area_id' => $validated['expertise_area_id'],
            'endorser_id' => $endorser->id,
            'created_at' => now(),
        ]);

        // Award reputation for endorsement
        UserReputation::create([
            'user_id' => $user->id,
            'giver_id' => $endorser->id,
            'points' => 10,
            'reason' => 'expertise_endorsed',
            'category' => 'general',
        ]);

        $user->increment('reputation_score', 10);

        // Update expertise area endorsement count
        DB::table('expertise_areas')
            ->where('id', $validated['expertise_area_id'])
            ->increment('endorsements_count');

        return response()->json([
            'success' => true,
            'message' => 'Expertise endorsed successfully',
        ]);
    }

    /**
     * Get user reputation history
     */
    public function history(User $user)
    {
        $history = $user->reputations()
            ->with('giver', 'relatedPost', 'relatedComment')
            ->latest()
            ->paginate(50);

        $stats = [
            'total' => $user->reputation_score,
            'this_month' => $user->reputations()->where('created_at', '>=', now()->startOfMonth())->sum('points'),
            'this_week' => $user->reputations()->where('created_at', '>=', now()->startOfWeek())->sum('points'),
            'categories' => $user->reputations()
                ->select('category', DB::raw('SUM(points) as total'))
                ->groupBy('category')
                ->pluck('total', 'category'),
        ];

        return view('reputation.history', compact('user', 'history', 'stats'));
    }

    /**
     * Get points for reputation reason
     */
    private function getPointsForReason(string $reason): int
    {
        $points = [
            'helpful_answer' => 10,
            'best_answer' => 25,
            'quality_content' => 15,
            'expertise' => 20,
        ];

        return $points[$reason] ?? 10;
    }

    /**
     * Update category-specific reputation
     */
    private function updateCategoryReputation(User $user, string $category, int $points): void
    {
        $field = "reputation_{$category}";
        if (in_array($category, ['visa', 'jobs', 'relationships'])) {
            $user->increment($field, $points);
        }
    }

    /**
     * Check and award milestones
     */
    private function checkMilestones(User $user): void
    {
        $milestones = [
            100 => 'Rising Star',
            500 => 'Active Contributor',
            1000 => 'Trusted Member',
            2500 => 'Expert',
            5000 => 'Master',
            10000 => 'Legend',
        ];

        foreach ($milestones as $threshold => $badge) {
            if ($user->reputation_score >= $threshold && !$user->hasBadge($badge)) {
                $user->awardBadge($badge);
                
                // Send notification
                // TODO: Send milestone achievement notification
            }
        }

        // Check for trusted contributor status
        if ($user->reputation_score >= 1000 && !$user->is_trusted_contributor) {
            $user->update(['is_trusted_contributor' => true]);
        }
    }

    /**
     * Get reputation milestones
     */
    private function getReputationMilestones(int $currentScore): array
    {
        $allMilestones = [
            ['threshold' => 100, 'name' => 'Rising Star', 'reward' => 'Profile Badge'],
            ['threshold' => 500, 'name' => 'Active Contributor', 'reward' => 'Highlighted Posts'],
            ['threshold' => 1000, 'name' => 'Trusted Member', 'reward' => 'Trusted Badge + Priority Support'],
            ['threshold' => 2500, 'name' => 'Expert', 'reward' => 'Expert Badge + Moderator Access'],
            ['threshold' => 5000, 'name' => 'Master', 'reward' => 'Master Badge + Special Privileges'],
            ['threshold' => 10000, 'name' => 'Legend', 'reward' => 'Legend Badge + VIP Status'],
        ];

        return array_filter($allMilestones, function ($milestone) use ($currentScore) {
            return $currentScore >= $milestone['threshold'];
        });
    }

    /**
     * Get next milestone
     */
    private function getNextMilestone(int $currentScore): ?array
    {
        $allMilestones = [
            ['threshold' => 100, 'name' => 'Rising Star', 'reward' => 'Profile Badge'],
            ['threshold' => 500, 'name' => 'Active Contributor', 'reward' => 'Highlighted Posts'],
            ['threshold' => 1000, 'name' => 'Trusted Member', 'reward' => 'Trusted Badge + Priority Support'],
            ['threshold' => 2500, 'name' => 'Expert', 'reward' => 'Expert Badge + Moderator Access'],
            ['threshold' => 5000, 'name' => 'Master', 'reward' => 'Master Badge + Special Privileges'],
            ['threshold' => 10000, 'name' => 'Legend', 'reward' => 'Legend Badge + VIP Status'],
        ];

        foreach ($allMilestones as $milestone) {
            if ($currentScore < $milestone['threshold']) {
                $milestone['points_needed'] = $milestone['threshold'] - $currentScore;
                $milestone['progress_percentage'] = ($currentScore / $milestone['threshold']) * 100;
                return $milestone;
            }
        }

        return null;
    }

    /**
     * Get top contributors by category
     */
    private function getTopContributors(string $category, int $limit = 10): \Illuminate\Support\Collection
    {
        return User::whereHas('reputations', function ($q) use ($category) {
                $q->where('category', $category);
            })
            ->withSum(['reputations as category_score' => function ($q) use ($category) {
                $q->where('category', $category);
            }], 'points')
            ->orderBy('category_score', 'desc')
            ->limit($limit)
            ->get();
    }
}
