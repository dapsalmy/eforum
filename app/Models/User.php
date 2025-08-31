<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Admin\Country;
use App\Models\Chats;
use App\Models\Plans;
use App\Models\Posts;
use App\Models\Points;
use App\Models\Replies;
use App\Models\Comments;
use App\Models\Messages;
use App\Models\Reactions;
use App\Models\Subscription;
use App\Models\Notifications;
use App\Models\NigerianState;
use App\Models\NigerianLga;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Overtrue\LaravelLike\Traits\Liker;
use Illuminate\Notifications\Notifiable;
use Overtrue\LaravelFollow\Traits\Follower;
use Qirolab\Laravel\Reactions\Traits\Reacts;
use Overtrue\LaravelFollow\Traits\Followable;
use Overtrue\LaravelFavorite\Traits\Favoriter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Qirolab\Laravel\Reactions\Contracts\ReactsInterface;
use Cog\Contracts\Ban\Bannable as BannableInterface;
use Cog\Laravel\Ban\Models\Ban;
use Cog\Laravel\Ban\Traits\Bannable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements ReactsInterface, BannableInterface, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;
    use Liker;
    use Favoriter;
    use Reacts;
    use Follower;
    use Followable;
    use Bannable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'email',
        'password',
        'image',
        'username',
        'profession',
        'gender',
        'bio',
        'location',
        'country',
        'website',
        'twitter',
        'facebook',
        'instagram',
        'linkedin',
        'last_seen',
        'wallet',
        'earnings',
        'plan_id',
        'verified',
        'paypal_email',
        'banned_at',
        'google_id',
        'facebook_id',
        'state_id',
        'lga_id',
        'phone_number',
        'phone_country_code',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'two_factor_enabled' => 'boolean',
        'two_factor_confirmed_at' => 'datetime',
    ];


    public function posts()
    {
        return $this->hasMany(Posts::class);
    }

    public function posts_count(){
        return $this->hasMany(Posts::class)->count();
    }

    public function comments()
    {
        return $this->hasMany(Comments::class);
    }

    public function replies()
    {
        return $this->hasMany(Replies::class);
    }

    public function all_notifications()
    {
        return $this->hasMany(Notifications::class, 'recipient_id')
            ->orderByDesc('created_at')
            ->paginate(10);
    }

    public function notifications(){
        return $this->hasMany(Notifications::class, 'recipient_id')
            ->orderByDesc('created_at')
            ->take(15)
            ->get();
    }

    public function notification_count(){
        return $this->hasMany(Notifications::class, 'recipient_id')
            ->where('seen', 2)
            ->orderByDesc('created_at')
            ->count();
    }

    public function mark_as_read(){
        return $this->hasMany(Notifications::class, 'recipient_id')->update(['seen' => 1]);
    }

    public function messages_count(){

        $chats = Chats::where('sender_id', Auth::user()->id)->orWhere('receiver_id', Auth::user()->id)->orderByDesc('created_at')->get();
        $count = [];
        foreach($chats as $chat){
            $count[] = Messages::where('chat_id', $chat->id)->where('sender_id', '!=', Auth::user()->id)->where("seen", 2)->count();
        }

        return array_sum($count);
    }

    public function chats_nav(){

        $chats = Chats::where('sender_id', Auth::user()->id)->orWhere('receiver_id', Auth::user()->id)->orderByDesc('created_at')->get();
        return $chats;
    }

    public function reactions()
    {
        return $this->belongsTo(Reactions::class);
    }

    public function total_points(){
        return $this->hasMany(Points::class)->sum('score');
    }

    public function chats_sender()
    {
        return $this->belongsTo(Chats::class, 'sender_id');
    }

    public function chats_receiver()
    {
        return $this->belongsTo(Chats::class, 'receiver_id');
    }

    public function messages()
    {
        return $this->hasMany(Messages::class, 'sender_id');
    }

    public function search_views(){
        return $this->hasMany(UserViews::class, 'user_id');
    }

    public function views(){
        return $this->hasMany(UserViews::class, 'user_id')->count();
    }

    public function plan()
    {
        return $this->belongsTo(Plans::class);
    }

    public function subscription()
    {
        $sub = Subscription::where('user_id', Auth::user()->id)->where('status',1)->first();
        return $sub;
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class,'user_id')->orderBy('created_at','desc')->get();
    }

    public function ban_details()
    {
        $sub = Ban::where('bannable_id', Auth::user()->id)->where('deleted_at', NULL)->first();
        return $sub;
    }

    public function user_ban($id)
    {
        $sub = Ban::where('bannable_id', $id)->where('deleted_at', NULL)->first();
        return $sub;
    }

    public function role()
    {
        $sub = Role::where('name', Auth::user()->role)->first();
        return $sub;
    }

    /**
     * Get the state the user belongs to.
     */
    public function state()
    {
        return $this->belongsTo(NigerianState::class, 'state_id');
    }

    /**
     * Get the LGA the user belongs to.
     */
    public function lga()
    {
        return $this->belongsTo(NigerianLga::class, 'lga_id');
    }

    /**
     * Get formatted phone number with country code.
     */
    public function getFormattedPhoneAttribute()
    {
        if (!$this->phone_number) {
            return null;
        }
        return $this->phone_country_code . ' ' . $this->phone_number;
    }

    /**
     * Get full location including LGA and State.
     */
    public function getFullLocationAttribute()
    {
        if ($this->lga && $this->state) {
            return $this->lga->name . ', ' . $this->state->name;
        } elseif ($this->state) {
            return $this->state->name;
        }
        return $this->location; // fallback to original location field
    }

    public function user_sub($id)
    {
        $sub = Subscription::where('user_id', $id)->where('status',1)->first();
        return $sub;
    }

    /**
     * Get user's reputation records.
     */
    public function reputations()
    {
        return $this->hasMany(UserReputation::class);
    }

    /**
     * Get user's verification requests.
     */
    public function verificationRequests()
    {
        return $this->hasMany(VerificationRequest::class);
    }

    /**
     * Get user's job postings.
     */
    public function jobPostings()
    {
        return $this->hasMany(JobPosting::class);
    }

    /**
     * Get saved jobs.
     */
    public function savedJobs()
    {
        return $this->belongsToMany(JobPosting::class, 'job_saved')
            ->withTimestamps();
    }

    /**
     * Check if user is verified professional.
     */
    public function isVerifiedProfessional()
    {
        return $this->verified && !empty($this->verification_type);
    }

    /**
     * Get verification badge details.
     */
    public function getVerificationBadgeAttribute()
    {
        if (!$this->isVerifiedProfessional()) {
            return null;
        }

        return VerificationRequest::TYPES[$this->verification_type] ?? null;
    }

    /**
     * Get reputation in specific category.
     */
    public function getReputationInCategory($category)
    {
        return $this->reputations()
            ->where('category', $category)
            ->first();
    }

    /**
     * Check if user is trusted contributor.
     */
    public function isTrustedContributor()
    {
        return $this->is_trusted_contributor || $this->trust_score >= 80;
    }

    /**
     * Get user's expertise areas.
     */
    public function expertiseAreas()
    {
        return $this->belongsToMany(ExpertiseArea::class, 'user_expertise')
            ->withPivot('endorsement_count', 'is_verified', 'verified_at')
            ->withTimestamps();
    }
}
