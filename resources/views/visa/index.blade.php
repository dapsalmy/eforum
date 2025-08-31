@extends('layouts.front')

@section('content')

<section class="forum-home">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="visa-tracking">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">{{ trans('visa_tracking_center') }}</h4>
                        @auth
                            <a href="{{ route('visa.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> {{ trans('track_new_visa') }}
                            </a>
                        @endauth
                    </div>

                    <!-- Quick Stats -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="mb-0">{{ number_format($stats['total_applications']) }}</h3>
                                    <small class="text-muted">{{ trans('total_applications') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="mb-0 text-success">{{ number_format($stats['approved']) }}</h3>
                                    <small class="text-muted">{{ trans('approved') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="mb-0 text-warning">{{ number_format($stats['pending']) }}</h3>
                                    <small class="text-muted">{{ trans('in_process') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="mb-0">{{ $stats['processing_time'] }} {{ trans('days') }}</h3>
                                    <small class="text-muted">{{ trans('avg_processing') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    @auth
                        @if($myTrackings->count() > 0)
                            <!-- My Visa Trackings -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">{{ trans('my_visa_applications') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>{{ trans('country') }}</th>
                                                    <th>{{ trans('visa_type') }}</th>
                                                    <th>{{ trans('status') }}</th>
                                                    <th>{{ trans('applied_on') }}</th>
                                                    <th>{{ trans('actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($myTrackings as $tracking)
                                                    <tr>
                                                        <td>
                                                            <img src="https://flagcdn.com/w20/{{ strtolower(\Illuminate\Support\Str::substr($tracking->country, 0, 2)) }}.png" 
                                                                 alt="{{ $tracking->country }}" class="me-2">
                                                            {{ $tracking->country }}
                                                        </td>
                                                        <td>{{ $tracking->visa_type }}</td>
                                                        <td>{!! $tracking->getStatusBadge() !!}</td>
                                                        <td>{{ $tracking->application_date?->format('M d, Y') ?: '-' }}</td>
                                                        <td>
                                                            <a href="{{ route('visa.show', $tracking->id) }}" 
                                                               class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-eye"></i> {{ trans('view') }}
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-center">
                                        <a href="{{ route('visa.my') }}" class="btn btn-link">
                                            {{ trans('view_all_my_applications') }} →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endauth

                    <!-- Public Visa Timelines -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ trans('recent_visa_timelines') }}</h5>
                            <a href="{{ route('visa.search') }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-search"></i> {{ trans('search_timelines') }}
                            </a>
                        </div>
                        <div class="card-body">
                            @forelse($publicTimelines as $timeline)
                                <div class="timeline-item mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                <img src="https://flagcdn.com/w20/{{ strtolower(\Illuminate\Support\Str::substr($timeline->country, 0, 2)) }}.png" 
                                                     alt="{{ $timeline->country }}" class="me-2">
                                                {{ $timeline->country }} - {{ $timeline->visa_type }}
                                            </h6>
                                            <p class="mb-1 text-muted">
                                                <i class="bi bi-person"></i> {{ trans('by') }} 
                                                <a href="{{ route('user', $timeline->user->username) }}">
                                                    {{ $timeline->user->name }}
                                                </a>
                                            </p>
                                            <div class="timeline-dates">
                                                @if($timeline->application_date)
                                                    <span class="badge bg-secondary me-2">
                                                        {{ trans('applied') }}: {{ $timeline->application_date->format('M d, Y') }}
                                                    </span>
                                                @endif
                                                @if($timeline->decision_date)
                                                    <span class="badge bg-success">
                                                        {{ trans('decision') }}: {{ $timeline->decision_date->format('M d, Y') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            {!! $timeline->getStatusBadge() !!}
                                            <a href="{{ route('visa.show', $timeline->id) }}" 
                                               class="btn btn-sm btn-outline-primary ms-2">
                                                {{ trans('view_timeline') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-muted">{{ trans('no_public_timelines_yet') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Call to Action -->
                    @guest
                        <div class="alert alert-info">
                            <h5>{{ trans('track_your_visa_journey') }}</h5>
                            <p>{{ trans('join_eforum_to_track_visa') }}</p>
                            <a href="{{ route('register') }}" class="btn btn-primary">
                                {{ trans('register_now') }}
                            </a>
                        </div>
                    @endguest
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-3">
                <!-- Popular Visa Types -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ trans('popular_visa_types') }}</h5>
                    </div>
                    <div class="card-body">
                        @foreach($popularVisaTypes as $type)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <a href="{{ route('visa.search', ['visa_type' => $type->visa_type]) }}" 
                                   class="text-decoration-none">
                                    {{ $type->visa_type }}
                                </a>
                                <span class="badge bg-secondary">{{ $type->count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Popular Countries -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ trans('popular_destinations') }}</h5>
                    </div>
                    <div class="card-body">
                        @foreach($popularCountries as $country)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <a href="{{ route('visa.search', ['country' => $country->country]) }}" 
                                   class="text-decoration-none">
                                    <img src="https://flagcdn.com/w20/{{ strtolower(\Illuminate\Support\Str::substr($country->country, 0, 2)) }}.png" 
                                         alt="{{ $country->country }}" class="me-2">
                                    {{ $country->country }}
                                </a>
                                <span class="badge bg-secondary">{{ $country->count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ trans('visa_resources') }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <a href="{{ route('visa.statistics') }}">
                                    <i class="bi bi-bar-chart"></i> {{ trans('visa_statistics') }}
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="{{ route('forum.category', 'visa-immigration') }}">
                                    <i class="bi bi-chat-dots"></i> {{ trans('visa_forum') }}
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="{{ route('posts.create') }}">
                                    <i class="bi bi-question-circle"></i> {{ trans('ask_visa_question') }}
                                </a>
                            </li>
                            <li>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#documentChecklistModal">
                                    <i class="bi bi-card-checklist"></i> {{ trans('document_checklist') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Document Checklist Modal -->
<div class="modal fade" id="documentChecklistModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('common_visa_documents') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>{{ trans('general_requirements') }}</h6>
                <ul class="checklist">
                    <li>{{ trans('valid_passport') }}</li>
                    <li>{{ trans('passport_photos') }}</li>
                    <li>{{ trans('application_form') }}</li>
                    <li>{{ trans('visa_fee_payment') }}</li>
                    <li>{{ trans('travel_itinerary') }}</li>
                    <li>{{ trans('accommodation_proof') }}</li>
                    <li>{{ trans('financial_statements') }}</li>
                    <li>{{ trans('employment_letter') }}</li>
                    <li>{{ trans('invitation_letter') }}</li>
                    <li>{{ trans('travel_insurance') }}</li>
                </ul>
                <p class="text-muted">
                    <i class="bi bi-info-circle"></i> 
                    {{ trans('requirements_vary_by_country') }}
                </p>
            </div>
        </div>
    </div>
</div>

<style>
.timeline-item:last-child {
    border-bottom: none !important;
}
.timeline-dates .badge {
    font-weight: normal;
}
.checklist {
    list-style: none;
    padding-left: 0;
}
.checklist li {
    padding: 5px 0;
    padding-left: 25px;
    position: relative;
}
.checklist li:before {
    content: "☐";
    position: absolute;
    left: 0;
}
</style>

@endsection
