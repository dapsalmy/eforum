@extends('layouts.front')

@section('content')

<section class="forum-home">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="visa-tracking-detail">
                    <!-- Header Card -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h1 class="h3 mb-3">
                                        <img src="https://flagcdn.com/w40/{{ strtolower(\Illuminate\Support\Str::substr($tracking->country, 0, 2)) }}.png" 
                                             alt="{{ $tracking->country }}" class="me-2">
                                        {{ $tracking->country }} - {{ $tracking->visa_type }}
                                    </h1>
                                    <div class="mb-3">
                                        {!! $tracking->getStatusBadge() !!}
                                        @if($tracking->is_public)
                                            <span class="badge bg-info ms-2">
                                                <i class="bi bi-globe"></i> {{ trans('public_timeline') }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary ms-2">
                                                <i class="bi bi-lock"></i> {{ trans('private') }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-muted">
                                        <i class="bi bi-person"></i> {{ trans('by') }} 
                                        <a href="{{ route('user', $tracking->user->username) }}">
                                            {{ $tracking->user->name }}
                                        </a>
                                        <span class="ms-3">
                                            <i class="bi bi-clock"></i> {{ trans('started') }} {{ $tracking->created_at->diffForHumans() }}
                                        </span>
                                    </p>
                                </div>
                                @if(Auth::id() === $tracking->user_id)
                                    <div>
                                        <a href="{{ route('visa.edit', $tracking->id) }}" class="btn btn-primary">
                                            <i class="bi bi-pencil"></i> {{ trans('edit') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="progress mt-4" style="height: 25px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $tracking->getProgressPercentageAttribute() }}%">
                                    {{ $tracking->getProgressPercentageAttribute() }}%
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Important Dates -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ trans('important_dates') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="date-item">
                                        <i class="bi bi-calendar-plus text-primary"></i>
                                        <div>
                                            <strong>{{ trans('application_date') }}</strong><br>
                                            {{ $tracking->application_date?->format('F d, Y') ?: trans('not_set') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="date-item">
                                        <i class="bi bi-calendar-check text-warning"></i>
                                        <div>
                                            <strong>{{ trans('interview_date') }}</strong><br>
                                            {{ $tracking->interview_date?->format('F d, Y') ?: trans('not_set') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="date-item">
                                        <i class="bi bi-calendar-x text-success"></i>
                                        <div>
                                            <strong>{{ trans('decision_date') }}</strong><br>
                                            {{ $tracking->decision_date?->format('F d, Y') ?: trans('pending') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @if($tracking->application_date && $tracking->decision_date)
                                <div class="alert alert-info mt-3">
                                    <i class="bi bi-info-circle"></i> 
                                    {{ trans('total_processing_time') }}: 
                                    <strong>{{ $tracking->application_date->diffInDays($tracking->decision_date) }} {{ trans('days') }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Timeline -->
                    @if($tracking->timeline && count($tracking->timeline) > 0)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">{{ trans('application_timeline') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    @foreach($tracking->timeline as $event)
                                        <div class="timeline-item">
                                            <div class="timeline-marker"></div>
                                            <div class="timeline-content">
                                                <h6 class="mb-1">{{ $event['event'] ?? '' }}</h6>
                                                @if(isset($event['description']) && $event['description'])
                                                    <p class="mb-1">{{ $event['description'] }}</p>
                                                @endif
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar"></i> {{ $event['date'] ?? '' }}
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Document Checklist -->
                    @if($tracking->documents_checklist && count($tracking->documents_checklist) > 0)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">{{ trans('document_checklist') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($tracking->documents_checklist as $doc)
                                        <div class="col-md-6 mb-2">
                                            <div class="document-check-item">
                                                @if(($doc['status'] ?? '') == 'completed')
                                                    <i class="bi bi-check-circle-fill text-success"></i>
                                                @elseif(($doc['status'] ?? '') == 'not_required')
                                                    <i class="bi bi-dash-circle text-muted"></i>
                                                @else
                                                    <i class="bi bi-circle text-warning"></i>
                                                @endif
                                                <span class="{{ ($doc['status'] ?? '') == 'completed' ? 'text-decoration-line-through' : '' }}">
                                                    {{ $doc['document'] ?? '' }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                @php
                                    $completed = collect($tracking->documents_checklist)->where('status', 'completed')->count();
                                    $total = collect($tracking->documents_checklist)->whereNotIn('status', ['not_required'])->count();
                                    $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;
                                @endphp
                                
                                <div class="progress mt-3" style="height: 20px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%">
                                        {{ $completed }}/{{ $total }} {{ trans('documents_ready') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Notes -->
                    @if($tracking->notes)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">{{ trans('experience_and_notes') }}</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{!! nl2br(e($tracking->notes)) !!}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Similar Applications -->
                    @if($similarApplications->count() > 0)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">{{ trans('similar_visa_applications') }}</h5>
                            </div>
                            <div class="card-body">
                                @foreach($similarApplications as $similar)
                                    <div class="similar-item mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">
                                                    <a href="{{ route('visa.show', $similar->id) }}">
                                                        {{ $similar->visa_type }} - {{ $similar->country }}
                                                    </a>
                                                </h6>
                                                <p class="mb-0 text-muted">
                                                    {{ trans('by') }} {{ $similar->user->name }} • 
                                                    @if($similar->decision_date)
                                                        {{ trans('processed_in') }} {{ $similar->application_date?->diffInDays($similar->decision_date) }} {{ trans('days') }}
                                                    @else
                                                        {{ trans('in_progress') }}
                                                    @endif
                                                </p>
                                            </div>
                                            {!! $similar->getStatusBadge() !!}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Stats -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ trans('application_stats') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="stat-item mb-3">
                            <strong>{{ trans('current_status') }}</strong><br>
                            {!! $tracking->getStatusBadge() !!}
                        </div>
                        <div class="stat-item mb-3">
                            <strong>{{ trans('days_in_process') }}</strong><br>
                            {{ $tracking->created_at->diffInDays() }} {{ trans('days') }}
                        </div>
                        @if($tracking->application_date)
                            <div class="stat-item mb-3">
                                <strong>{{ trans('days_since_application') }}</strong><br>
                                {{ $tracking->application_date->diffInDays() }} {{ trans('days') }}
                            </div>
                        @endif
                        <div class="stat-item">
                            <strong>{{ trans('progress') }}</strong><br>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $tracking->getProgressPercentageAttribute() }}%"></div>
                            </div>
                            <small>{{ $tracking->getProgressPercentageAttribute() }}% {{ trans('complete') }}</small>
                        </div>
                    </div>
                </div>

                <!-- Related Forum Posts -->
                @if($relatedPosts->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ trans('related_discussions') }}</h5>
                        </div>
                        <div class="card-body">
                            @foreach($relatedPosts as $post)
                                <div class="mb-3">
                                    <a href="{{ route('post', $post->slug) }}" class="text-decoration-none">
                                        {{ $post->title }}
                                    </a>
                                    <small class="d-block text-muted">
                                        {{ $post->comments_count }} {{ trans('comments') }} • 
                                        {{ $post->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Share -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ trans('share_timeline') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode('Check out my visa timeline for ' . $tracking->country) }}" 
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-twitter"></i> Twitter
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-facebook"></i> Facebook
                            </a>
                            <a href="https://wa.me/?text={{ urlencode('Check out my visa timeline: ' . request()->url()) }}" 
                               target="_blank" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-whatsapp"></i> WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.date-item {
    display: flex;
    align-items: center;
    gap: 10px;
}
.date-item i {
    font-size: 24px;
}
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 9px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}
.timeline-item {
    position: relative;
    padding-bottom: 30px;
}
.timeline-item:last-child {
    padding-bottom: 0;
}
.timeline-marker {
    position: absolute;
    left: -25px;
    top: 5px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #fff;
    border: 3px solid var(--nigeria-green);
}
.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
}
.document-check-item {
    display: flex;
    align-items: center;
    gap: 8px;
}
.document-check-item i {
    font-size: 18px;
}
.stat-item {
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}
.stat-item:last-child {
    padding-bottom: 0;
    border-bottom: none;
}
</style>

@endsection
