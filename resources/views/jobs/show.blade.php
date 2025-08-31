@extends('layouts.front')

@section('content')

<section class="forum-home">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="job-detail">
                    <!-- Job Header -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h1 class="h3 mb-3">{{ $job->title }}</h1>
                                    <div class="d-flex align-items-center mb-3">
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
                                            <h5 class="mb-1">{{ $job->company_name }}</h5>
                                            @if($job->company_website)
                                                <a href="{{ $job->company_website }}" target="_blank" class="text-muted">
                                                    <i class="bi bi-link-45deg"></i> {{ trans('company_website') }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="job-meta mb-3">
                                        <span class="me-3">
                                            <i class="bi bi-geo-alt"></i> 
                                            {{ $job->location ?: 'Remote' }}
                                        </span>
                                        <span class="me-3">
                                            <i class="bi bi-briefcase"></i> 
                                            {{ ucfirst($job->employment_type) }}
                                        </span>
                                        <span class="me-3">
                                            <i class="bi bi-laptop"></i> 
                                            {{ ucfirst($job->job_type) }}
                                        </span>
                                        @if($job->visa_sponsorship)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> {{ trans('visa_sponsorship_available') }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($job->salary_min || $job->salary_max)
                                        <div class="salary-display">
                                            <h4 class="text-success">
                                                @if($job->salary_currency == 'NGN')₦@else{{ $job->salary_currency }}@endif
                                                @if($job->salary_min && $job->salary_max)
                                                    {{ number_format($job->salary_min) }} - {{ number_format($job->salary_max) }}
                                                @elseif($job->salary_min)
                                                    {{ number_format($job->salary_min) }}+
                                                @else
                                                    Up to {{ number_format($job->salary_max) }}
                                                @endif
                                                <small class="text-muted">/ {{ $job->salary_period }}</small>
                                            </h4>
                                        </div>
                                    @endif
                                </div>
                                <div class="text-end">
                                    @auth
                                        <button class="btn btn-outline-primary mb-2 save-job-btn" 
                                                data-job-id="{{ $job->id }}"
                                                data-saved="{{ $isSaved ? 'true' : 'false' }}">
                                            <i class="bi {{ $isSaved ? 'bi-bookmark-fill' : 'bi-bookmark' }}"></i>
                                            {{ $isSaved ? trans('saved') : trans('save') }}
                                        </button>
                                        <br>
                                        @if($job->user_id == auth()->id())
                                            <a href="{{ route('jobs.edit', $job->id) }}" class="btn btn-warning">
                                                <i class="bi bi-pencil"></i> {{ trans('edit') }}
                                            </a>
                                            <a href="{{ route('jobs.applicants', $job->id) }}" class="btn btn-info">
                                                <i class="bi bi-people"></i> {{ trans('view_applicants') }}
                                            </a>
                                        @endif
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Job Details -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="mb-3">{{ trans('job_description') }}</h5>
                            <div class="job-content">
                                {!! nl2br(e($job->description)) !!}
                            </div>

                            @if($job->requirements)
                                <h5 class="mt-4 mb-3">{{ trans('requirements') }}</h5>
                                <div class="job-content">
                                    {!! nl2br(e($job->requirements)) !!}
                                </div>
                            @endif

                            @if($job->benefits)
                                <h5 class="mt-4 mb-3">{{ trans('benefits') }}</h5>
                                <div class="job-content">
                                    {!! nl2br(e($job->benefits)) !!}
                                </div>
                            @endif

                            @if($job->required_skills)
                                <h5 class="mt-4 mb-3">{{ trans('required_skills') }}</h5>
                                <div class="skills-list">
                                    @foreach(json_decode($job->required_skills) as $skill)
                                        <span class="badge bg-primary me-2 mb-2">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            @endif

                            @if($job->preferred_skills)
                                <h5 class="mt-4 mb-3">{{ trans('preferred_skills') }}</h5>
                                <div class="skills-list">
                                    @foreach(json_decode($job->preferred_skills) as $skill)
                                        <span class="badge bg-secondary me-2 mb-2">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            @endif

                            @if($job->visa_sponsorship && $job->visa_types)
                                <h5 class="mt-4 mb-3">{{ trans('visa_information') }}</h5>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> 
                                    {{ trans('visa_types_sponsored') }}: {{ $job->visa_types }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- How to Apply -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="mb-3">{{ trans('how_to_apply') }}</h5>
                            <div class="job-content mb-3">
                                {!! nl2br(e($job->how_to_apply)) !!}
                            </div>
                            
                            @auth
                                <div class="d-flex gap-2">
                                    @if($job->application_url)
                                        <a href="{{ $job->application_url }}" target="_blank" 
                                           class="btn btn-primary btn-lg">
                                            <i class="bi bi-box-arrow-up-right"></i> {{ trans('apply_external') }}
                                        </a>
                                    @endif
                                    @if($job->application_email)
                                        <a href="mailto:{{ $job->application_email }}?subject=Application for {{ $job->title }}" 
                                           class="btn btn-primary btn-lg">
                                            <i class="bi bi-envelope"></i> {{ trans('apply_email') }}
                                        </a>
                                    @endif
                                    @if(!$job->application_url && !$job->application_email)
                                        <form action="{{ route('jobs.apply', $job->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="bi bi-send"></i> {{ trans('apply_now') }}
                                            </button>
                                        </form>
                                    @endif
                                    <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#reportModal">
                                        <i class="bi bi-flag"></i> {{ trans('report') }}
                                    </button>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i> 
                                    {{ trans('login_to_apply') }}
                                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm ms-2">
                                        {{ trans('login') }}
                                    </a>
                                </div>
                            @endauth

                            @if($job->deadline)
                                <div class="mt-3 text-muted">
                                    <i class="bi bi-calendar-event"></i> 
                                    {{ trans('application_deadline') }}: 
                                    <strong>{{ $job->deadline->format('F d, Y') }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Related Jobs -->
                    @if($relatedJobs->count() > 0)
                        <h5 class="mb-3">{{ trans('similar_jobs') }}</h5>
                        <div class="related-jobs">
                            @foreach($relatedJobs as $relatedJob)
                                <div class="card mb-2">
                                    <div class="card-body">
                                        <h6 class="mb-1">
                                            <a href="{{ route('jobs.show', $relatedJob->slug) }}">
                                                {{ $relatedJob->title }}
                                            </a>
                                        </h6>
                                        <p class="mb-1 text-muted">{{ $relatedJob->company_name }}</p>
                                        <small class="text-muted">
                                            {{ $relatedJob->location ?: 'Remote' }} • 
                                            {{ ucfirst($relatedJob->employment_type) }}
                                            @if($relatedJob->visa_sponsorship)
                                                • <span class="text-success">{{ trans('visa_sponsorship') }}</span>
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-3">
                <!-- Job Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ trans('job_information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-item mb-3">
                            <strong>{{ trans('posted_by') }}</strong><br>
                            <a href="{{ route('user', $job->user->username) }}">
                                <img src="{{ my_asset('uploads/users/'.$job->user->image) }}" 
                                     alt="{{ $job->user->name }}" 
                                     class="rounded-circle me-2" 
                                     width="30" height="30">
                                {{ $job->user->name }}
                            </a>
                        </div>
                        <div class="info-item mb-3">
                            <strong>{{ trans('posted_on') }}</strong><br>
                            {{ $job->created_at->format('F d, Y') }}
                        </div>
                        <div class="info-item mb-3">
                            <strong>{{ trans('category') }}</strong><br>
                            <a href="{{ route('jobs.index', ['category' => $job->category->id]) }}">
                                {{ $job->category->name }}
                            </a>
                        </div>
                        <div class="info-item mb-3">
                            <strong>{{ trans('views') }}</strong><br>
                            {{ number_format($job->views) }}
                        </div>
                        <div class="info-item">
                            <strong>{{ trans('applications') }}</strong><br>
                            {{ number_format($job->applications) }}
                        </div>
                    </div>
                </div>

                <!-- Share Job -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ trans('share_job') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-2">
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($job->title) }}" 
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-twitter"></i>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?url={{ urlencode(request()->url()) }}&title={{ urlencode($job->title) }}" 
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-linkedin"></i>
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($job->title . ' - ' . request()->url()) }}" 
                               target="_blank" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('report_job') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="reportForm">
                @csrf
                <input type="hidden" name="job_id" value="{{ $job->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ trans('reason_for_reporting') }}</label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ trans('submit_report') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.company-logo {
    width: 80px;
    height: 80px;
    object-fit: contain;
    border-radius: 8px;
}
.company-logo-placeholder {
    width: 80px;
    height: 80px;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-size: 32px;
    color: #999;
}
.job-meta {
    color: #6c757d;
}
.job-content {
    white-space: pre-wrap;
    line-height: 1.6;
}
.skills-list .badge {
    font-weight: normal;
}
.info-item {
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}
.info-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Save/Unsave job
    document.querySelector('.save-job-btn')?.addEventListener('click', function() {
        const btn = this;
        const jobId = btn.dataset.jobId;
        const isSaved = btn.dataset.saved === 'true';
        
        fetch('{{ route("jobs.save") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ job_id: jobId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                btn.dataset.saved = data.saved ? 'true' : 'false';
                btn.innerHTML = data.saved 
                    ? '<i class="bi bi-bookmark-fill"></i> {{ trans("saved") }}'
                    : '<i class="bi bi-bookmark"></i> {{ trans("save") }}';
            }
        });
    });
    
    // Report form
    document.getElementById('reportForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        fetch('{{ route("jobs.report") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                job_id: this.job_id.value,
                reason: this.reason.value
            })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('reportModal')).hide();
                this.reset();
            }
        });
    });
});
</script>

@endsection
