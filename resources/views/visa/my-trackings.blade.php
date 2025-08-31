@extends('layouts.user')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ trans('my_visa_trackings') }}</h4>
                <a href="{{ route('visa.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> {{ trans('track_new_visa') }}
                </a>
            </div>
            <div class="card-body">
                @if($trackings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ trans('country') }}</th>
                                    <th>{{ trans('visa_type') }}</th>
                                    <th>{{ trans('status') }}</th>
                                    <th>{{ trans('application_date') }}</th>
                                    <th>{{ trans('decision_date') }}</th>
                                    <th>{{ trans('visibility') }}</th>
                                    <th>{{ trans('progress') }}</th>
                                    <th>{{ trans('actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trackings as $tracking)
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
                                            @if($tracking->decision_date)
                                                {{ $tracking->decision_date->format('M d, Y') }}
                                                <small class="d-block text-muted">
                                                    ({{ $tracking->application_date?->diffInDays($tracking->decision_date) }} {{ trans('days') }})
                                                </small>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($tracking->is_public)
                                                <span class="badge bg-info">
                                                    <i class="bi bi-globe"></i> {{ trans('public') }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-lock"></i> {{ trans('private') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 15px; width: 100px;">
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: {{ $tracking->getProgressPercentageAttribute() }}%">
                                                    {{ $tracking->getProgressPercentageAttribute() }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('visa.show', $tracking->id) }}" 
                                                   class="btn btn-outline-primary" 
                                                   title="{{ trans('view') }}">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('visa.edit', $tracking->id) }}" 
                                                   class="btn btn-outline-primary" 
                                                   title="{{ trans('edit') }}">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('visa.destroy', $tracking->id) }}" method="POST" 
                                                      onsubmit="return confirm('{{ trans('confirm_delete') }}');" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" 
                                                            title="{{ trans('delete') }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $trackings->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-passport" style="font-size: 48px; color: #ccc;"></i>
                        <h5 class="mt-3">{{ trans('no_visa_trackings_yet') }}</h5>
                        <p class="text-muted">{{ trans('start_tracking_your_visa_journey') }}</p>
                        <a href="{{ route('visa.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> {{ trans('track_new_visa') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Quick Tips -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">{{ trans('visa_tracking_benefits') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center mb-3">
                            <i class="bi bi-clock-history" style="font-size: 32px; color: var(--nigeria-green);"></i>
                            <h6 class="mt-2">{{ trans('track_progress') }}</h6>
                            <p class="text-muted small">{{ trans('monitor_application_status') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center mb-3">
                            <i class="bi bi-card-checklist" style="font-size: 32px; color: var(--nigeria-green);"></i>
                            <h6 class="mt-2">{{ trans('document_checklist') }}</h6>
                            <p class="text-muted small">{{ trans('never_miss_document') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center mb-3">
                            <i class="bi bi-people" style="font-size: 32px; color: var(--nigeria-green);"></i>
                            <h6 class="mt-2">{{ trans('help_community') }}</h6>
                            <p class="text-muted small">{{ trans('share_experience_help_others') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
