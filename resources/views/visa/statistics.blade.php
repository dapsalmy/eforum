@extends('layouts.front')

@section('content')

<section class="forum-home">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h4 class="mb-4">{{ trans('visa_statistics_analytics') }}</h4>
                
                <!-- Overall Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h2 class="mb-0">{{ $stats['success_rate'] }}%</h2>
                                <p class="text-muted mb-0">{{ trans('success_rate') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h2 class="mb-0">{{ $stats['avg_processing_time'] }}</h2>
                                <p class="text-muted mb-0">{{ trans('avg_days') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h2 class="mb-0">{{ \App\Models\VisaTracking::count() }}</h2>
                                <p class="text-muted mb-0">{{ trans('total_applications') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h2 class="mb-0">{{ \App\Models\VisaTracking::where('is_public', true)->count() }}</h2>
                                <p class="text-muted mb-0">{{ trans('public_timelines') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Country Statistics -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ trans('statistics_by_country') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ trans('country') }}</th>
                                        <th>{{ trans('applications') }}</th>
                                        <th>{{ trans('approved') }}</th>
                                        <th>{{ trans('success_rate') }}</th>
                                        <th>{{ trans('avg_processing_days') }}</th>
                                        <th>{{ trans('action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['by_country'] as $country)
                                        <tr>
                                            <td>
                                                <img src="https://flagcdn.com/w20/{{ strtolower(\Illuminate\Support\Str::substr($country->country, 0, 2)) }}.png" 
                                                     alt="{{ $country->country }}" class="me-2">
                                                {{ $country->country }}
                                            </td>
                                            <td>{{ $country->total }}</td>
                                            <td>{{ $country->approved }}</td>
                                            <td>
                                                @php
                                                    $rate = $country->total > 0 ? round(($country->approved / $country->total) * 100, 1) : 0;
                                                @endphp
                                                <div class="progress" style="height: 20px; min-width: 100px;">
                                                    <div class="progress-bar {{ $rate >= 70 ? 'bg-success' : ($rate >= 40 ? 'bg-warning' : 'bg-danger') }}" 
                                                         role="progressbar" style="width: {{ $rate }}%">
                                                        {{ $rate }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ round($country->avg_days) ?: '-' }}</td>
                                            <td>
                                                <a href="{{ route('visa.search', ['country' => $country->country]) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    {{ trans('view_timelines') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Visa Type Statistics -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">{{ trans('statistics_by_visa_type') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="visaTypeChart"></canvas>
                                </div>
                                <hr>
                                <div class="small">
                                    @foreach($stats['by_visa_type'] as $type)
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>{{ $type->visa_type }}</span>
                                            <span>
                                                <strong>{{ $type->total }}</strong> 
                                                ({{ $type->total > 0 ? round(($type->approved / $type->total) * 100) : 0 }}% {{ trans('approved') }})
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">{{ trans('monthly_trends') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="monthlyTrendsChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Success Stories -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ trans('recent_approvals') }}</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $recentApprovals = \App\Models\VisaTracking::where('status', 'approved')
                                ->where('is_public', true)
                                ->with('user')
                                ->orderBy('decision_date', 'desc')
                                ->limit(10)
                                ->get();
                        @endphp
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ trans('applicant') }}</th>
                                        <th>{{ trans('destination') }}</th>
                                        <th>{{ trans('visa_type') }}</th>
                                        <th>{{ trans('processing_time') }}</th>
                                        <th>{{ trans('decision_date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentApprovals as $approval)
                                        <tr>
                                            <td>
                                                <a href="{{ route('user', $approval->user->username) }}">
                                                    {{ $approval->user->name }}
                                                </a>
                                            </td>
                                            <td>
                                                <img src="https://flagcdn.com/w20/{{ strtolower(\Illuminate\Support\Str::substr($approval->country, 0, 2)) }}.png" 
                                                     alt="{{ $approval->country }}" class="me-2">
                                                {{ $approval->country }}
                                            </td>
                                            <td>{{ $approval->visa_type }}</td>
                                            <td>
                                                @if($approval->application_date)
                                                    {{ $approval->application_date->diffInDays($approval->decision_date) }} {{ trans('days') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $approval->decision_date?->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Visa Type Chart
    const visaTypeData = @json($stats['by_visa_type']);
    const visaTypeChart = new Chart(document.getElementById('visaTypeChart'), {
        type: 'doughnut',
        data: {
            labels: visaTypeData.map(item => item.visa_type),
            datasets: [{
                data: visaTypeData.map(item => item.total),
                backgroundColor: [
                    '#008751', '#00a65d', '#28a745', '#20c997', 
                    '#17a2b8', '#007bff', '#6610f2', '#6f42c1',
                    '#e83e8c', '#dc3545', '#fd7e14', '#ffc107'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
    
    // Monthly Trends Chart
    const monthlyData = @json($stats['by_month']);
    const monthlyTrendsChart = new Chart(document.getElementById('monthlyTrendsChart'), {
        type: 'line',
        data: {
            labels: monthlyData.map(item => {
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                return months[item.month - 1] + ' ' + item.year;
            }),
            datasets: [{
                label: '{{ trans("applications") }}',
                data: monthlyData.map(item => item.total),
                borderColor: '#008751',
                backgroundColor: 'rgba(0, 135, 81, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>

<style>
.chart-container {
    position: relative;
    height: 300px;
}
.progress {
    background-color: #f0f0f0;
}
</style>

@endsection
