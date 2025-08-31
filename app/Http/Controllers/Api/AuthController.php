<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends ApiController
{
    /**
     * Register a new user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users|alpha_dash',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'nullable|string',
            'phone_country_code' => 'nullable|string',
            'state_id' => 'nullable|exists:nigerian_states,id',
            'lga_id' => 'nullable|exists:nigerian_lgas,id',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->toArray());
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'phone_country_code' => $request->phone_country_code ?? '+234',
                'state_id' => $request->state_id,
                'lga_id' => $request->lga_id,
                'email_verified_at' => now(),
            ]);

            $token = $user->createToken('mobile-app')->plainTextToken;

            return $this->success([
                'user' => $this->transformUser($user),
                'token' => $token,
                'token_type' => 'Bearer'
            ], 'Registration successful');

        } catch (\Exception $e) {
            return $this->serverError('Registration failed. Please try again.');
        }
    }

    /**
     * Login user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string', // Can be email or username
            'password' => 'required|string',
            'device_name' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->toArray());
        }

        // Determine if login is email or username
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        $credentials = [
            $loginType => $request->login,
            'password' => $request->password
        ];

        if (!Auth::attempt($credentials)) {
            return $this->unauthorized('Invalid credentials');
        }

        $user = Auth::user();
        
        // Check if user is banned
        if ($user->isBanned()) {
            Auth::logout();
            return $this->unauthorized('Your account has been suspended');
        }

        $token = $user->createToken($request->device_name ?? 'mobile-app')->plainTextToken;

        return $this->success([
            'user' => $this->transformUser($user),
            'token' => $token,
            'token_type' => 'Bearer'
        ], 'Login successful');
    }

    /**
     * Logout user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // Revoke the current token
            $request->user()->currentAccessToken()->delete();
            
            return $this->success(null, 'Logged out successfully');
        } catch (\Exception $e) {
            return $this->serverError('Logout failed');
        }
    }

    /**
     * Get authenticated user profile
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load(['state', 'lga', 'badges', 'reputations']);

        return $this->success([
            'user' => $this->transformUser($user, true)
        ]);
    }

    /**
     * Update user profile
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'tagline' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string',
            'phone_country_code' => 'nullable|string',
            'state_id' => 'nullable|exists:nigerian_states,id',
            'lga_id' => 'nullable|exists:nigerian_lgas,id',
            'website' => 'nullable|url',
            'twitter' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'github' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->toArray());
        }

        try {
            $user->update($request->only([
                'name', 'bio', 'tagline', 'phone_number', 'phone_country_code',
                'state_id', 'lga_id', 'website', 'twitter', 'linkedin', 'github'
            ]));

            return $this->success([
                'user' => $this->transformUser($user->fresh())
            ], 'Profile updated successfully');

        } catch (\Exception $e) {
            return $this->serverError('Failed to update profile');
        }
    }

    /**
     * Update user password
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed|different:current_password'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->toArray());
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->validationError(['current_password' => ['Current password is incorrect']]);
        }

        try {
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // Revoke all tokens except current
            $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

            return $this->success(null, 'Password updated successfully');

        } catch (\Exception $e) {
            return $this->serverError('Failed to update password');
        }
    }

    /**
     * Transform user data for API response
     *
     * @param User $user
     * @param bool $detailed
     * @return array
     */
    protected function transformUser(User $user, bool $detailed = false): array
    {
        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'avatar' => $user->avatar,
            'tagline' => $user->tagline,
            'reputation_score' => $user->reputation_score,
            'trust_score' => $user->trust_score,
            'is_verified' => $user->isVerifiedProfessional(),
            'is_trusted_contributor' => $user->is_trusted_contributor,
            'created_at' => $user->created_at->toIso8601String(),
        ];

        if ($detailed) {
            $data = array_merge($data, [
                'bio' => $user->bio,
                'phone_number' => $user->phone_number,
                'phone_country_code' => $user->phone_country_code,
                'location' => $user->full_location,
                'state' => $user->state ? [
                    'id' => $user->state->id,
                    'name' => $user->state->name
                ] : null,
                'lga' => $user->lga ? [
                    'id' => $user->lga->id,
                    'name' => $user->lga->name
                ] : null,
                'website' => $user->website,
                'twitter' => $user->twitter,
                'linkedin' => $user->linkedin,
                'github' => $user->github,
                'wallet_balance' => $user->wallet,
                'points' => $user->points,
                'badges' => $user->badges->map(function ($badge) {
                    return [
                        'id' => $badge->id,
                        'name' => $badge->name,
                        'description' => $badge->description,
                        'icon' => $badge->icon,
                        'earned_at' => $badge->pivot->created_at->toIso8601String()
                    ];
                }),
                'stats' => [
                    'posts_count' => $user->posts()->count(),
                    'comments_count' => $user->comments()->count(),
                    'jobs_posted' => $user->jobPostings()->count(),
                    'visa_trackings' => $user->visaTrackings()->count(),
                ],
                'email_verified' => !is_null($user->email_verified_at),
                'is_banned' => $user->isBanned(),
            ]);
        }

        return $data;
    }
}
