<?php

namespace App\Http\Controllers\Api;

use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApiKeyController extends ApiController
{
    /**
     * Get user's API keys
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $keys = $user->apiKeys()
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return $this->success(
            $this->transformPagination($keys),
            'API keys retrieved successfully'
        );
    }

    /**
     * Create a new API key request
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'permissions' => 'array',
            'permissions.*' => 'string|in:read,write,admin',
            'rate_limit' => 'integer|min:10|max:1000',
            'expires_at' => 'nullable|date|after:now',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = $request->user();

        // Check if user has too many pending requests
        $pendingCount = $user->apiKeys()->where('status', 'pending')->count();
        if ($pendingCount >= 5) {
            return $this->error('You have too many pending API key requests. Please wait for approval or cancel existing requests.');
        }

        $apiKey = ApiKey::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'key' => ApiKey::generateKey(),
            'permissions' => $request->permissions ?? ['read'],
            'status' => 'pending',
            'rate_limit' => $request->rate_limit ?? 120,
            'expires_at' => $request->expires_at,
            'notes' => $request->notes
        ]);

        // Notify admins about new API key request
        $this->notifyAdmins($apiKey);

        return $this->success([
            'api_key' => [
                'id' => $apiKey->id,
                'name' => $apiKey->name,
                'status' => $apiKey->status,
                'created_at' => $apiKey->created_at,
                'notes' => $apiKey->notes
            ]
        ], 'API key request submitted successfully. It will be reviewed by administrators.');
    }

    /**
     * Get API key details
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $apiKey = $user->apiKeys()->find($id);

        if (!$apiKey) {
            return $this->notFound('API key not found');
        }

        return $this->success([
            'api_key' => [
                'id' => $apiKey->id,
                'name' => $apiKey->name,
                'status' => $apiKey->status,
                'permissions' => $apiKey->permissions,
                'rate_limit' => $apiKey->rate_limit,
                'last_used_at' => $apiKey->last_used_at,
                'expires_at' => $apiKey->expires_at,
                'approved_at' => $apiKey->approved_at,
                'notes' => $apiKey->notes,
                'created_at' => $apiKey->created_at
            ]
        ]);
    }

    /**
     * Update API key
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $apiKey = $user->apiKeys()->find($id);

        if (!$apiKey) {
            return $this->notFound('API key not found');
        }

        // Only allow updates to pending keys
        if ($apiKey->status !== 'pending') {
            return $this->error('Only pending API keys can be updated');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'string|in:read,write,admin',
            'rate_limit' => 'sometimes|integer|min:10|max:1000',
            'expires_at' => 'sometimes|nullable|date|after:now',
            'notes' => 'sometimes|nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $apiKey->update($request->only([
            'name', 'permissions', 'rate_limit', 'expires_at', 'notes'
        ]));

        return $this->success([
            'api_key' => [
                'id' => $apiKey->id,
                'name' => $apiKey->name,
                'status' => $apiKey->status
            ]
        ], 'API key updated successfully');
    }

    /**
     * Delete API key
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $apiKey = $user->apiKeys()->find($id);

        if (!$apiKey) {
            return $this->notFound('API key not found');
        }

        $apiKey->delete();

        return $this->success(null, 'API key deleted successfully');
    }

    /**
     * Get the actual API key (only for approved keys)
     */
    public function getKey(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $apiKey = $user->apiKeys()->find($id);

        if (!$apiKey) {
            return $this->notFound('API key not found');
        }

        if (!$apiKey->isActive()) {
            return $this->error('API key is not active. Please wait for approval or check if it has expired.');
        }

        return $this->success([
            'api_key' => [
                'id' => $apiKey->id,
                'name' => $apiKey->name,
                'key' => $apiKey->key,
                'permissions' => $apiKey->permissions,
                'rate_limit' => $apiKey->rate_limit,
                'expires_at' => $apiKey->expires_at
            ]
        ], 'API key retrieved successfully');
    }

    /**
     * Notify admins about new API key request
     */
    private function notifyAdmins(ApiKey $apiKey): void
    {
        $admins = \App\Models\User::whereIn('role', ['Admin', 'Moderator'])->get();

        foreach ($admins as $admin) {
            // In-app notification
            \App\Models\Notifications::create([
                'sender_id' => $apiKey->user_id,
                'recipient_id' => $admin->id,
                'notification_type' => 'api_key_request',
                'seen' => 2,
            ]);

            // Email notification
            try {
                $content = (object) [
                    'subject' => 'New API Key Request',
                    'body' => view('emails.api-key-request', [
                        'apiKey' => $apiKey,
                        'user' => $apiKey->user,
                    ])->render(),
                ];
                \Mail::to($admin->email)->queue(new \App\Mail\GeneralMail($content));
            } catch (\Throwable $e) {
                \Log::error('Failed to send API key request notification: ' . $e->getMessage());
            }
        }
    }
}
