@extends('layouts.user')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">{{ trans('applicants_for') }}: {{ $job->title }}</h4>
                <p class="text-muted mb-0">{{ $job->company_name }} • {{ $job->location ?: 'Remote' }}</p>
            </div>
            <div class="card-body">
                @if($applicants->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ trans('applicant') }}</th>
                                    <th>{{ trans('location') }}</th>
                                    <th>{{ trans('applied_on') }}</th>
                                    <th>{{ trans('status') }}</th>
                                    <th>{{ trans('actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($applicants as $applicant)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ my_asset('uploads/users/'.$applicant->image) }}" 
                                                     alt="{{ $applicant->name }}" 
                                                     class="rounded-circle me-2" 
                                                     width="40" height="40">
                                                <div>
                                                    <a href="{{ route('user', $applicant->username) }}" 
                                                       class="text-decoration-none">
                                                        {{ $applicant->name }}
                                                    </a>
                                                    @if($applicant->isVerifiedProfessional())
                                                        <i class="bi bi-patch-check-fill text-primary" 
                                                           title="{{ trans('verified_professional') }}"></i>
                                                    @endif
                                                    <br>
                                                    <small class="text-muted">{{ $applicant->tagline }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($applicant->state)
                                                {{ $applicant->state->name }}@if($applicant->lga), {{ $applicant->lga->name }}@endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $applicant->pivot->applied_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            @php
                                                $status = $applicant->pivot->status ?? 'pending';
                                            @endphp
                                            @if($status == 'pending')
                                                <span class="badge bg-warning">{{ trans('pending') }}</span>
                                            @elseif($status == 'reviewed')
                                                <span class="badge bg-info">{{ trans('reviewed') }}</span>
                                            @elseif($status == 'shortlisted')
                                                <span class="badge bg-success">{{ trans('shortlisted') }}</span>
                                            @elseif($status == 'rejected')
                                                <span class="badge bg-danger">{{ trans('rejected') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('user', $applicant->username) }}" 
                                                   class="btn btn-outline-primary" 
                                                   target="_blank"
                                                   title="{{ trans('view_profile') }}">
                                                    <i class="bi bi-person"></i>
                                                </a>
                                                @if($applicant->email)
                                                    <a href="mailto:{{ $applicant->email }}" 
                                                       class="btn btn-outline-info" 
                                                       title="{{ trans('send_email') }}">
                                                        <i class="bi bi-envelope"></i>
                                                    </a>
                                                @endif
                                                <button class="btn btn-outline-success" 
                                                        onclick="updateStatus({{ $applicant->id }}, 'shortlisted')"
                                                        title="{{ trans('shortlist') }}">
                                                    <i class="bi bi-star"></i>
                                                </button>
                                                <button class="btn btn-outline-danger" 
                                                        onclick="updateStatus({{ $applicant->id }}, 'rejected')"
                                                        title="{{ trans('reject') }}">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $applicants->links() }}
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ trans('total_applicants') }}</h5>
                                    <h2>{{ $applicants->total() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ trans('pending_review') }}</h5>
                                    <h2>{{ $job->applicants()->wherePivot('status', 'pending')->count() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ trans('shortlisted') }}</h5>
                                    <h2>{{ $job->applicants()->wherePivot('status', 'shortlisted')->count() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ trans('rejected') }}</h5>
                                    <h2>{{ $job->applicants()->wherePivot('status', 'rejected')->count() }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-people" style="font-size: 48px; color: #ccc;"></i>
                        <h5 class="mt-3">{{ trans('no_applicants_yet') }}</h5>
                        <p class="text-muted">{{ trans('applicants_will_appear_here') }}</p>
                        <a href="{{ route('jobs.show', $job->slug) }}" class="btn btn-primary">
                            <i class="bi bi-eye"></i> {{ trans('view_job_posting') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function updateStatus(applicantId, status) {
    if (!confirm('{{ trans("confirm_status_change") }}')) {
        return;
    }
    
    fetch('{{ route("jobs.applicant.status") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            job_id: {{ $job->id }},
            applicant_id: applicantId,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || '{{ trans("error_occurred") }}');
        }
    });
}
</script>

@endsection
