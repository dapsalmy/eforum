@extends('layouts.front')

@section('content')

<section class="forum-home">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="visa-search">
                    <h4 class="mb-4">{{ trans('search_visa_timelines') }}</h4>
                    
                    <!-- Search Form -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" action="{{ route('visa.search') }}">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">{{ trans('country') }}</label>
                                        <select name="country" class="form-select">
                                            <option value="">{{ trans('all_countries') }}</option>
                                            <option value="United States" {{ request('country') == 'United States' ? 'selected' : '' }}>🇺🇸 United States</option>
                                            <option value="United Kingdom" {{ request('country') == 'United Kingdom' ? 'selected' : '' }}>🇬🇧 United Kingdom</option>
                                            <option value="Canada" {{ request('country') == 'Canada' ? 'selected' : '' }}>🇨🇦 Canada</option>
                                            <option value="Australia" {{ request('country') == 'Australia' ? 'selected' : '' }}>🇦🇺 Australia</option>
                                            <option value="Germany" {{ request('country') == 'Germany' ? 'selected' : '' }}>🇩🇪 Germany</option>
                                            <option value="France" {{ request('country') == 'France' ? 'selected' : '' }}>🇫🇷 France</option>
                                            <option value="Netherlands" {{ request('country') == 'Netherlands' ? 'selected' : '' }}>🇳🇱 Netherlands</option>
                                            <option value="Italy" {{ request('country') == 'Italy' ? 'selected' : '' }}>🇮🇹 Italy</option>
                                            <option value="Spain" {{ request('country') == 'Spain' ? 'selected' : '' }}>🇪🇸 Spain</option>
                                            <option value="Switzerland" {{ request('country') == 'Switzerland' ? 'selected' : '' }}>🇨🇭 Switzerland</option>
                                            <option value="Sweden" {{ request('country') == 'Sweden' ? 'selected' : '' }}>🇸🇪 Sweden</option>
                                            <option value="Norway" {{ request('country') == 'Norway' ? 'selected' : '' }}>🇳🇴 Norway</option>
                                            <option value="Denmark" {{ request('country') == 'Denmark' ? 'selected' : '' }}>🇩🇰 Denmark</option>
                                            <option value="Ireland" {{ request('country') == 'Ireland' ? 'selected' : '' }}>🇮🇪 Ireland</option>
                                            <option value="New Zealand" {{ request('country') == 'New Zealand' ? 'selected' : '' }}>🇳🇿 New Zealand</option>
                                            <option value="Singapore" {{ request('country') == 'Singapore' ? 'selected' : '' }}>🇸🇬 Singapore</option>
                                            <option value="United Arab Emirates" {{ request('country') == 'United Arab Emirates' ? 'selected' : '' }}>🇦🇪 UAE</option>
                                            <option value="South Africa" {{ request('country') == 'South Africa' ? 'selected' : '' }}>🇿🇦 South Africa</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <label class="form-label">{{ trans('visa_type') }}</label>
                                        <input type="text" name="visa_type" class="form-control" 
                                               value="{{ request('visa_type') }}" 
                                               placeholder="{{ trans('e.g. Student, Work, Tourist') }}">
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <label class="form-label">{{ trans('status') }}</label>
                                        <select name="status" class="form-select">
                                            <option value="">{{ trans('all_statuses') }}</option>
                                            <option value="planning" {{ request('status') == 'planning' ? 'selected' : '' }}>{{ trans('planning') }}</option>
                                            <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>{{ trans('preparing') }}</option>
                                            <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>{{ trans('submitted') }}</option>
                                            <option value="interview_scheduled" {{ request('status') == 'interview_scheduled' ? 'selected' : '' }}>{{ trans('interview_scheduled') }}</option>
                                            <option value="interview_completed" {{ request('status') == 'interview_completed' ? 'selected' : '' }}>{{ trans('interview_completed') }}</option>
                                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ trans('approved') }}</option>
                                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{ trans('rejected') }}</option>
                                            <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>{{ trans('on_hold') }}</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <label class="form-label">{{ trans('year') }}</label>
                                        <select name="year" class="form-select">
                                            <option value="">{{ trans('all_years') }}</option>
                                            @for($year = date('Y'); $year >= date('Y') - 5; $year--)
                                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-8 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="bi bi-search"></i> {{ trans('search') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Results Summary -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>
                            {{ trans('found_x_timelines', ['count' => $trackings->total()]) }}
                        </h5>
                        @if(request()->hasAny(['country', 'visa_type', 'status', 'year']))
                            <a href="{{ route('visa.search') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> {{ trans('clear_filters') }}
                            </a>
                        @endif
                    </div>
                    
                    <!-- Search Results -->
                    <div class="visa-timelines">
                        @forelse($trackings as $tracking)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h5 class="mb-2">
                                                <img src="https://flagcdn.com/w20/{{ strtolower(\Illuminate\Support\Str::substr($tracking->country, 0, 2)) }}.png" 
                                                     alt="{{ $tracking->country }}" class="me-2">
                                                <a href="{{ route('visa.show', $tracking->id) }}" class="text-decoration-none">
                                                    {{ $tracking->country }} - {{ $tracking->visa_type }}
                                                </a>
                                            </h5>
                                            <p class="mb-2 text-muted">
                                                <i class="bi bi-person"></i> {{ trans('by') }} 
                                                <a href="{{ route('user', $tracking->user->username) }}">
                                                    {{ $tracking->user->name }}
                                                </a>
                                                @if($tracking->user->isVerifiedProfessional())
                                                    <i class="bi bi-patch-check-fill text-primary" 
                                                       title="{{ trans('verified_professional') }}"></i>
                                                @endif
                                            </p>
                                            <div class="timeline-info">
                                                @if($tracking->application_date)
                                                    <span class="me-3">
                                                        <i class="bi bi-calendar-plus"></i> 
                                                        {{ trans('applied') }}: {{ $tracking->application_date->format('M Y') }}
                                                    </span>
                                                @endif
                                                @if($tracking->decision_date)
                                                    <span class="me-3">
                                                        <i class="bi bi-calendar-check"></i> 
                                                        {{ trans('decision') }}: {{ $tracking->decision_date->format('M Y') }}
                                                    </span>
                                                @endif
                                                @if($tracking->application_date && $tracking->decision_date)
                                                    <span class="text-success">
                                                        <i class="bi bi-clock"></i> 
                                                        {{ $tracking->application_date->diffInDays($tracking->decision_date) }} {{ trans('days') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            {!! $tracking->getStatusBadge() !!}
                                            <div class="mt-2">
                                                <a href="{{ route('visa.show', $tracking->id) }}" class="btn btn-sm btn-outline-primary">
                                                    {{ trans('view_timeline') }} <i class="bi bi-arrow-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> {{ trans('no_timelines_found') }}
                                @if(request()->hasAny(['country', 'visa_type', 'status', 'year']))
                                    <a href="{{ route('visa.search') }}" class="alert-link">
                                        {{ trans('try_clearing_filters') }}
                                    </a>
                                @endif
                            </div>
                        @endforelse
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $trackings->withQueryString()->links() }}
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-3">
                <!-- Popular Countries -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ trans('popular_countries') }}</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $popularCountries = \App\Models\VisaTracking::select('country')
                                ->selectRaw('COUNT(*) as count')
                                ->where('is_public', true)
                                ->groupBy('country')
                                ->orderBy('count', 'desc')
                                ->limit(10)
                                ->get();
                        @endphp
                        @foreach($popularCountries as $country)
                            <a href="{{ route('visa.search', ['country' => $country->country]) }}" 
                               class="d-block mb-2 text-decoration-none">
                                <img src="https://flagcdn.com/w20/{{ strtolower(\Illuminate\Support\Str::substr($country->country, 0, 2)) }}.png" 
                                     alt="{{ $country->country }}" class="me-2">
                                {{ $country->country }}
                                <span class="float-end badge bg-secondary">{{ $country->count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
                
                <!-- Visa Types -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ trans('visa_types') }}</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $visaTypes = \App\Models\VisaTracking::select('visa_type')
                                ->selectRaw('COUNT(*) as count')
                                ->where('is_public', true)
                                ->groupBy('visa_type')
                                ->orderBy('count', 'desc')
                                ->limit(8)
                                ->get();
                        @endphp
                        @foreach($visaTypes as $type)
                            <a href="{{ route('visa.search', ['visa_type' => $type->visa_type]) }}" 
                               class="d-block mb-2 text-decoration-none">
                                {{ $type->visa_type }}
                                <span class="float-end badge bg-secondary">{{ $type->count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
                
                <!-- Success Stats -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ trans('success_rate') }}</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $total = \App\Models\VisaTracking::whereIn('status', ['approved', 'rejected'])->count();
                            $approved = \App\Models\VisaTracking::where('status', 'approved')->count();
                            $successRate = $total > 0 ? round(($approved / $total) * 100, 1) : 0;
                        @endphp
                        <div class="text-center">
                            <h2 class="text-success mb-1">{{ $successRate }}%</h2>
                            <p class="text-muted mb-0">
                                {{ trans('of_x_applications_approved', ['total' => $total]) }}
                            </p>
                        </div>
                        <hr>
                        <div class="small text-muted">
                            <i class="bi bi-check-circle text-success"></i> {{ trans('approved') }}: {{ $approved }}<br>
                            <i class="bi bi-x-circle text-danger"></i> {{ trans('rejected') }}: {{ $total - $approved }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.timeline-info {
    font-size: 0.9rem;
}
.timeline-info span {
    display: inline-block;
}
@media (max-width: 768px) {
    .timeline-info span {
        display: block;
        margin-bottom: 5px;
    }
}
</style>

@endsection
