@extends('layouts.admin')

@section('title', 'Advertisements Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Advertisements Management</h4>
                    <a href="{{ route('admin.advertisements.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Create Ad
                    </a>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $stats['total_ads'] ?? 0 }}</h5>
                                    <p class="card-text">Total Ads</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $stats['active_ads'] ?? 0 }}</h5>
                                    <p class="card-text">Active Ads</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $stats['pending_ads'] ?? 0 }}</h5>
                                    <p class="card-text">Pending Review</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</h5>
                                    <p class="card-text">Total Revenue</p>
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
                                    <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>Paused</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                </select>
                                <select name="position" class="form-select" style="width: auto;">
                                    <option value="">All Positions</option>
                                    <option value="top" {{ request('position') == 'top' ? 'selected' : '' }}>Top Banner</option>
                                    <option value="footer" {{ request('position') == 'footer' ? 'selected' : '' }}>Footer</option>
                                    <option value="sidebar" {{ request('position') == 'sidebar' ? 'selected' : '' }}>Sidebar</option>
                                    <option value="in_content" {{ request('position') == 'in_content' ? 'selected' : '' }}>In Content</option>
                                </select>
                                <input type="text" name="search" class="form-control" placeholder="Search by title..." value="{{ request('search') }}" style="width: auto;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('admin.advertisements.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>User</th>
                                    <th>Title</th>
                                    <th>Position</th>
                                    <th>Status</th>
                                    <th>Budget</th>
                                    <th>Spent</th>
                                    <th>Impressions</th>
                                    <th>Clicks</th>
                                    <th>CTR</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($advertisements as $ad)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.user', $ad->user->username) }}" class="text-decoration-none">
                                            {{ $ad->user->name }}
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ $ad->user->email }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $ad->title }}</strong>
                                        @if($ad->is_featured)
                                            <span class="badge bg-warning ms-1">Featured</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst($ad->position) }}</span>
                                    </td>
                                    <td>
                                        @if($ad->status == 'active')
                                            <span class="badge bg-success">Active</span>
                                        @elseif($ad->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($ad->status == 'paused')
                                            <span class="badge bg-secondary">Paused</span>
                                        @elseif($ad->status == 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @elseif($ad->status == 'expired')
                                            <span class="badge bg-dark">Expired</span>
                                        @endif
                                    </td>
                                    <td>${{ number_format($ad->budget, 2) }}</td>
                                    <td>${{ number_format($ad->spent, 2) }}</td>
                                    <td>{{ number_format($ad->impressions) }}</td>
                                    <td>{{ number_format($ad->clicks) }}</td>
                                    <td>{{ $ad->ctr }}%</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.advertisements.show', $ad) }}" class="btn btn-info btn-sm" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if($ad->status == 'pending')
                                                <form method="POST" action="{{ route('admin.advertisements.approve', $ad) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.advertisements.reject', $ad) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Reject" onclick="return confirm('Are you sure you want to reject this advertisement?')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @elseif($ad->status == 'active')
                                                <form method="POST" action="{{ route('admin.advertisements.pause', $ad) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-warning btn-sm" title="Pause" onclick="return confirm('Are you sure you want to pause this advertisement?')">
                                                        <i class="fas fa-pause"></i>
                                                    </button>
                                                </form>
                                            @elseif($ad->status == 'paused')
                                                <form method="POST" action="{{ route('admin.advertisements.resume', $ad) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-success btn-sm" title="Resume">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <a href="{{ route('admin.advertisements.edit', $ad) }}" class="btn btn-primary btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form method="POST" action="{{ route('admin.advertisements.destroy', $ad) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this advertisement?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">No advertisements found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $advertisements->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
