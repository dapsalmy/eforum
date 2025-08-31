@extends('layouts.admin')

@section('title', 'API Keys Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">API Keys Management</h4>
                    <a href="{{ route('admin.api-keys.statistics') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-chart-bar"></i> Statistics
                    </a>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $stats['total'] }}</h5>
                                    <p class="card-text">Total Keys</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $stats['active'] }}</h5>
                                    <p class="card-text">Active Keys</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $stats['pending'] }}</h5>
                                    <p class="card-text">Pending Approval</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $stats['suspended'] }}</h5>
                                    <p class="card-text">Suspended Keys</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" class="d-flex gap-3">
                                <select name="status" class="form-select" style="width: auto;">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                </select>
                                <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ request('search') }}" style="width: auto;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('admin.api-keys.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </form>
                        </div>
                    </div>

                    <!-- Bulk Actions -->
                    <form id="bulkActionForm" method="POST" action="{{ route('admin.api-keys.bulk-action') }}">
                        @csrf
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <select name="action" class="form-select" style="width: auto;" required>
                                    <option value="">Select Action</option>
                                    <option value="approve">Approve Selected</option>
                                    <option value="reject">Reject Selected</option>
                                    <option value="suspend">Suspend Selected</option>
                                    <option value="delete">Delete Selected</option>
                                </select>
                                <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Are you sure you want to perform this bulk action?')">
                                    <i class="fas fa-cogs"></i> Apply
                                </button>
                            </div>
                            <div>
                                <strong>{{ $apiKeys->total() }}</strong> API keys found
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>User</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Permissions</th>
                                        <th>Rate Limit</th>
                                        <th>Created</th>
                                        <th>Last Used</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($apiKeys as $apiKey)
                                    <tr>
                                        <td><input type="checkbox" name="api_keys[]" value="{{ $apiKey->id }}" class="api-key-checkbox"></td>
                                        <td>
                                            <a href="{{ route('admin.user', $apiKey->user->username) }}" class="text-decoration-none">
                                                {{ $apiKey->user->name }}
                                            </a>
                                            <br>
                                            <small class="text-muted">{{ $apiKey->user->email }}</small>
                                        </td>
                                        <td>{{ $apiKey->name }}</td>
                                        <td>
                                            @if($apiKey->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($apiKey->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($apiKey->status == 'suspended')
                                                <span class="badge bg-danger">Suspended</span>
                                            @elseif($apiKey->status == 'expired')
                                                <span class="badge bg-secondary">Expired</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($apiKey->permissions)
                                                <small>{{ implode(', ', $apiKey->permissions) }}</small>
                                            @else
                                                <span class="text-muted">None</span>
                                            @endif
                                        </td>
                                        <td>{{ $apiKey->rate_limit }}/min</td>
                                        <td>{{ $apiKey->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @if($apiKey->last_used_at)
                                                {{ $apiKey->last_used_at->diffForHumans() }}
                                            @else
                                                <span class="text-muted">Never</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.api-keys.show', $apiKey) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if($apiKey->status == 'pending')
                                                    <form method="POST" action="{{ route('admin.api-keys.approve', $apiKey) }}" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.api-keys.reject', $apiKey) }}" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Reject" onclick="return confirm('Are you sure you want to reject this API key request?')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                @elseif($apiKey->status == 'active')
                                                    <form method="POST" action="{{ route('admin.api-keys.suspend', $apiKey) }}" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-warning btn-sm" title="Suspend" onclick="return confirm('Are you sure you want to suspend this API key?')">
                                                            <i class="fas fa-pause"></i>
                                                        </button>
                                                    </form>
                                                @elseif($apiKey->status == 'suspended')
                                                    <form method="POST" action="{{ route('admin.api-keys.reactivate', $apiKey) }}" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-success btn-sm" title="Reactivate">
                                                            <i class="fas fa-play"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form method="POST" action="{{ route('admin.api-keys.destroy', $apiKey) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this API key?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No API keys found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $apiKeys->appends(request()->query())->links() }}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Select all checkboxes
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.api-key-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
@endsection
