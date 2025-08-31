@extends('layouts.admin')

@section('title', 'API Keys Statistics')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">API Keys Statistics</h4>
                    <a href="{{ route('admin.api-keys.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Management
                    </a>
                </div>
                <div class="card-body">
                    <!-- Main Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['total_keys'] }}</h3>
                                    <p class="mb-0">Total API Keys</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['active_keys'] }}</h3>
                                    <p class="mb-0">Active Keys</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['pending_requests'] }}</h3>
                                    <p class="mb-0">Pending Requests</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['suspended_keys'] }}</h3>
                                    <p class="mb-0">Suspended Keys</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>This Month</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h4 class="text-primary">{{ $stats['keys_this_month'] }}</h4>
                                                <small class="text-muted">New Keys</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h4 class="text-success">{{ $stats['approvals_this_month'] }}</h4>
                                                <small class="text-muted">Approvals</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Usage Overview</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="text-center">
                                                <h4 class="text-info">{{ number_format($stats['total_impressions'] ?? 0) }}</h4>
                                                <small class="text-muted">Impressions</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-center">
                                                <h4 class="text-warning">{{ number_format($stats['total_clicks'] ?? 0) }}</h4>
                                                <small class="text-muted">Clicks</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-center">
                                                <h4 class="text-success">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</h4>
                                                <small class="text-muted">Revenue</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Users -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Top Users by API Keys</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>User</th>
                                                    <th>Keys</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($topUsers as $user)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('admin.user', $user->username) }}" class="text-decoration-none">
                                                            {{ $user->name }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary">{{ $user->api_keys_count }}</span>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="2" class="text-center text-muted">No users with API keys yet</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Recent Activity</h5>
                                </div>
                                <div class="card-body">
                                    <div class="timeline">
                                        @forelse($recentActivity->take(10) as $activity)
                                        <div class="timeline-item mb-3">
                                            <div class="timeline-marker bg-primary"></div>
                                            <div class="timeline-content">
                                                <small class="text-muted">{{ $activity->updated_at->diffForHumans() }}</small>
                                                <p class="mb-1">
                                                    <strong>{{ $activity->user->name }}</strong>
                                                    @if($activity->status == 'active' && $activity->approved_at)
                                                        <span class="badge bg-success">Key Approved</span>
                                                    @elseif($activity->status == 'pending')
                                                        <span class="badge bg-warning">Key Requested</span>
                                                    @elseif($activity->status == 'suspended')
                                                        <span class="badge bg-danger">Key Suspended</span>
                                                    @endif
                                                </p>
                                                <small class="text-muted">{{ $activity->name }}</small>
                                            </div>
                                        </div>
                                        @empty
                                        <p class="text-center text-muted">No recent activity</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts/Graphs Placeholder -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Usage Analytics</h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Analytics Coming Soon:</strong>
                                        Charts and graphs for API key usage, geographic distribution,
                        rate limiting trends, and revenue analytics will be available here.
                                    </div>

                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <div class="p-3">
                                                <i class="fas fa-chart-line fa-3x text-primary mb-2"></i>
                                                <h6>Usage Trends</h6>
                                                <small class="text-muted">Track API usage over time</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="p-3">
                                                <i class="fas fa-globe fa-3x text-success mb-2"></i>
                                                <h6>Geographic Data</h6>
                                                <small class="text-muted">API usage by location</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="p-3">
                                                <i class="fas fa-shield-alt fa-3x text-warning mb-2"></i>
                                                <h6>Security Metrics</h6>
                                                <small class="text-muted">Rate limiting & security stats</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="p-3">
                                                <i class="fas fa-dollar-sign fa-3x text-info mb-2"></i>
                                                <h6>Revenue Reports</h6>
                                                <small class="text-muted">Monetization analytics</small>
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
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-left: 30px;
}

.timeline-marker {
    position: absolute;
    left: -37px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 10px 15px;
    border-radius: 0.375rem;
    border-left: 3px solid #007bff;
}
</style>
@endsection
