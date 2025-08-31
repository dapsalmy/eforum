@extends('layouts.admin')

@section('title', 'API Key Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">API Key Details</h4>
                    <a href="{{ route('admin.api-keys.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- API Key Information -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Key Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Name:</strong> {{ $apiKey->name }}</p>
                                            <p><strong>Status:</strong>
                                                @if($apiKey->status == 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @elseif($apiKey->status == 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($apiKey->status == 'suspended')
                                                    <span class="badge bg-danger">Suspended</span>
                                                @elseif($apiKey->status == 'expired')
                                                    <span class="badge bg-secondary">Expired</span>
                                                @endif
                                            </p>
                                            <p><strong>Rate Limit:</strong> {{ $apiKey->rate_limit }} requests/minute</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Created:</strong> {{ $apiKey->created_at->format('M d, Y H:i') }}</p>
                                            <p><strong>Last Used:</strong>
                                                @if($apiKey->last_used_at)
                                                    {{ $apiKey->last_used_at->format('M d, Y H:i') }}
                                                    <small class="text-muted">({{ $apiKey->last_used_at->diffForHumans() }})</small>
                                                @else
                                                    <span class="text-muted">Never used</span>
                                                @endif
                                            </p>
                                            @if($apiKey->expires_at)
                                                <p><strong>Expires:</strong> {{ $apiKey->expires_at->format('M d, Y H:i') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    @if($apiKey->permissions)
                                        <div class="mt-3">
                                            <strong>Permissions:</strong>
                                            <div class="mt-2">
                                                @foreach($apiKey->permissions as $permission)
                                                    <span class="badge bg-info me-1">{{ $permission }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if($apiKey->notes)
                                        <div class="mt-3">
                                            <strong>Notes:</strong>
                                            <p class="text-muted">{{ $apiKey->notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- User Information -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>User Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <img src="{{ $apiKey->user->avatar ?? asset('assets/images/default-avatar.png') }}" alt="Avatar" class="rounded-circle" style="width: 80px; height: 80px;">
                                    </div>
                                    <p><strong>Name:</strong> {{ $apiKey->user->name }}</p>
                                    <p><strong>Username:</strong> {{ $apiKey->user->username }}</p>
                                    <p><strong>Email:</strong> {{ $apiKey->user->email }}</p>
                                    <p><strong>Member Since:</strong> {{ $apiKey->user->created_at->format('M d, Y') }}</p>
                                    <p><strong>Reputation:</strong> {{ $apiKey->user->reputation_score }}</p>
                                    <a href="{{ route('admin.user', $apiKey->user->username) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-user"></i> View User
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex gap-2 flex-wrap">
                                        @if($apiKey->status == 'pending')
                                            <form method="POST" action="{{ route('admin.api-keys.approve', $apiKey) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <div class="mb-3">
                                                    <label for="rate_limit" class="form-label">Rate Limit (requests/minute)</label>
                                                    <input type="number" name="rate_limit" id="rate_limit" class="form-control" value="{{ $apiKey->rate_limit }}" min="10" max="1000">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="expires_at" class="form-label">Expiration Date (optional)</label>
                                                    <input type="datetime-local" name="expires_at" id="expires_at" class="form-control">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="notes" class="form-label">Approval Notes</label>
                                                    <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-check"></i> Approve Key
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.api-keys.reject', $apiKey) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <div class="mb-3">
                                                    <label for="rejection_reason" class="form-label">Rejection Reason</label>
                                                    <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3" required></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to reject this API key request?')">
                                                    <i class="fas fa-times"></i> Reject Key
                                                </button>
                                            </form>
                                        @elseif($apiKey->status == 'active')
                                            <form method="POST" action="{{ route('admin.api-keys.suspend', $apiKey) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <div class="mb-3">
                                                    <label for="suspension_reason" class="form-label">Suspension Reason</label>
                                                    <textarea name="suspension_reason" id="suspension_reason" class="form-control" rows="3" required></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to suspend this API key?')">
                                                    <i class="fas fa-pause"></i> Suspend Key
                                                </button>
                                            </form>
                                        @elseif($apiKey->status == 'suspended')
                                            <form method="POST" action="{{ route('admin.api-keys.reactivate', $apiKey) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to reactivate this API key?')">
                                                    <i class="fas fa-play"></i> Reactivate Key
                                                </button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('admin.api-keys.destroy', $apiKey) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this API key? This action cannot be undone.')">
                                                <i class="fas fa-trash"></i> Delete Key
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
