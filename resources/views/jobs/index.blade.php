@extends('layouts.front')

@section('content')

<section class="forum-home">
    <div class="container">
        <div class="row">
            
            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="forum-category">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">{{ trans('job_opportunities') }}</h4>
                        <a href="{{ route('jobs.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> {{ trans('post_job') }}
                        </a>
                    </div>

                    <!-- Filters -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" action="{{ route('jobs.index') }}" id="jobFilterForm">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <input type="text" name="search" class="form-control" 
                                               placeholder="{{ trans('search_jobs') }}" 
                                               value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <select name="category" class="form-select">
                                            <option value="">{{ trans('all_categories') }}</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" 
                                                    {{ request('category') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="job_type" class="form-select">
                                            <option value="">{{ trans('all_types') }}</option>
                                            <option value="remote" {{ request('job_type') == 'remote' ? 'selected' : '' }}>
                                                {{ trans('remote') }}
                                            </option>
                                            <option value="hybrid" {{ request('job_type') == 'hybrid' ? 'selected' : '' }}>
                                                {{ trans('hybrid') }}
                                            </option>
                                            <option value="onsite" {{ request('job_type') == 'onsite' ? 'selected' : '' }}>
                                                {{ trans('onsite') }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="visa_sponsorship" 
                                                   id="visaSponsorship" value="1"
                                                   {{ request('visa_sponsorship') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="visaSponsorship">
                                                {{ trans('visa_sponsorship') }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="location" class="form-control" 
                                               placeholder="{{ trans('location') }}" 
                                               value="{{ request('location') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" name="salary_min" class="form-control" 
                                               placeholder="{{ trans('min_salary') }}" 
                                               value="{{ request('salary_min') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <select name="sort" class="form-select">
                                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>
                                                {{ trans('latest_first') }}
                                            </option>
                                            <option value="salary_high" {{ request('sort') == 'salary_high' ? 'selected' : '' }}>
                                                {{ trans('highest_salary') }}
                                            </option>
                                            <option value="salary_low" {{ request('sort') == 'salary_low' ? 'selected' : '' }}>
                                                {{ trans('lowest_salary') }}
                                            </option>
                                            <option value="featured" {{ request('sort') == 'featured' ? 'selected' : '' }}>
                                                {{ trans('featured') }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="bi bi-search"></i> {{ trans('filter') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Job Listings -->
                    <div class="job-listings">
                        @forelse($jobs as $job)
                            <div class="card mb-3 job-card {{ $job->is_featured ? 'featured-job' : '' }}">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-start">
                                                @if($job->company_logo)
                                                    <img src="{{ StorageService::url($job->company_logo) }}" 
                                                         alt="{{ $job->company_name }}" 
                                                         class="company-logo me-3">
                                                @else
                                                    <div class="company-logo-placeholder me-3">
                                                        <i class="bi bi-building"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h5 class="mb-1">
                                                        <a href="{{ route('jobs.show', $job->slug) }}" class="text-decoration-none">
                                                            {{ $job->title }}
                                                        </a>
                                                        @if($job->is_featured)
                                                            <span class="badge bg-warning text-dark ms-2">
                                                                <i class="bi bi-star-fill"></i> {{ trans('featured') }}
                                                            </span>
                                                        @endif
                                                    </h5>
                                                    <p class="mb-2 text-muted">
                                                        <strong>{{ $job->company_name }}</strong>
                                                        @if($job->location)
                                                            <span class="ms-2">
                                                                <i class="bi bi-geo-alt"></i> {{ $job->location }}
                                                            </span>
                                                        @endif
                                                    </p>
                                                    <div class="job-meta">
                                                        <span class="badge bg-secondary">{{ ucfirst($job->job_type) }}</span>
                                                        <span class="badge bg-info">{{ ucfirst($job->employment_type) }}</span>
                                                        @if($job->visa_sponsorship)
                                                            <span class="badge bg-success">
                                                                <i class="bi bi-check-circle"></i> {{ trans('visa_sponsorship') }}
                                                            </span>
                                                        @endif
                                                        <span class="text-muted ms-2">
                                                            <i class="bi bi-clock"></i> {{ $job->created_at->diffForHumans() }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            @if($job->salary_min || $job->salary_max)
                                                <div class="salary-range mb-2">
                                                    <strong>
                                                        @if($job->salary_currency == 'NGN')₦@else{{ $job->salary_currency }}@endif
                                                        @if($job->salary_min && $job->salary_max)
                                                            {{ number_format($job->salary_min) }} - {{ number_format($job->salary_max) }}
                                                        @elseif($job->salary_min)
                                                            {{ number_format($job->salary_min) }}+
                                                        @else
                                                            Up to {{ number_format($job->salary_max) }}
                                                        @endif
                                                    </strong>
                                                    <small class="text-muted">/ {{ $job->salary_period }}</small>
                                                </div>
                                            @endif
                                            <a href="{{ route('jobs.show', $job->slug) }}" class="btn btn-primary btn-sm">
                                                {{ trans('view_details') }} <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> {{ trans('no_jobs_found') }}
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $jobs->withQueryString()->links() }}
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-3">
                <!-- Quick Stats -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ trans('job_stats') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="stat-item mb-2">
                            <i class="bi bi-briefcase"></i>
                            <span>{{ $jobs->total() }} {{ trans('active_jobs') }}</span>
                        </div>
                        <div class="stat-item mb-2">
                            <i class="bi bi-building"></i>
                            <span>{{ \App\Models\JobPosting::distinct('company_name')->count() }} {{ trans('companies') }}</span>
                        </div>
                        <div class="stat-item">
                            <i class="bi bi-airplane"></i>
                            <span>{{ \App\Models\JobPosting::where('visa_sponsorship', 1)->count() }} {{ trans('visa_sponsor_jobs') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Popular Categories -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ trans('popular_categories') }}</h5>
                    </div>
                    <div class="card-body">
                        @foreach($categories as $category)
                            <a href="{{ route('jobs.index', ['category' => $category->id]) }}" 
                               class="d-block mb-2 text-decoration-none">
                                {{ $category->name }}
                                <span class="float-end badge bg-secondary">
                                    {{ $category->job_postings_count ?? 0 }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Location Filter -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ trans('locations') }}</h5>
                    </div>
                    <div class="card-body">
                        <select class="form-select" onchange="filterByLocation(this.value)">
                            <option value="">{{ trans('all_locations') }}</option>
                            @foreach($states as $state)
                                <option value="{{ $state->name }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.job-card {
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}
.job-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-left-color: var(--nigeria-green);
}
.featured-job {
    background-color: #fffbf0;
    border-left-color: #ffc107;
}
.company-logo {
    width: 60px;
    height: 60px;
    object-fit: contain;
    border-radius: 8px;
}
.company-logo-placeholder {
    width: 60px;
    height: 60px;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-size: 24px;
    color: #999;
}
.job-meta .badge {
    font-size: 0.8rem;
    font-weight: normal;
}
.salary-range {
    color: var(--nigeria-green);
}
.stat-item {
    display: flex;
    align-items: center;
    gap: 10px;
}
.stat-item i {
    color: var(--nigeria-green);
}
</style>

<script>
function filterByLocation(location) {
    const currentUrl = new URL(window.location.href);
    if (location) {
        currentUrl.searchParams.set('location', location);
    } else {
        currentUrl.searchParams.delete('location');
    }
    window.location.href = currentUrl.toString();
}
</script>

@endsection
