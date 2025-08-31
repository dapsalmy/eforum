@extends('layouts.user')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ trans('my_job_postings') }}</h4>
                <a href="{{ route('jobs.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> {{ trans('post_new_job') }}
                </a>
            </div>
            <div class="card-body">
                @if($jobs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ trans('job_title') }}</th>
                                    <th>{{ trans('company') }}</th>
                                    <th>{{ trans('type') }}</th>
                                    <th>{{ trans('posted') }}</th>
                                    <th>{{ trans('expires') }}</th>
                                    <th>{{ trans('views') }}</th>
                                    <th>{{ trans('applicants') }}</th>
                                    <th>{{ trans('status') }}</th>
                                    <th>{{ trans('actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jobs as $job)
                                    <tr>
                                        <td>
                                            <a href="{{ route('jobs.show', $job->slug) }}" class="text-decoration-none">
                                                {{ $job->title }}
                                            </a>
                                        </td>
                                        <td>{{ $job->company_name }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ ucfirst($job->job_type) }}</span>
                                        </td>
                                        <td>{{ $job->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @if($job->expires_at)
                                                {{ $job->expires_at->format('M d, Y') }}
                                                @if($job->expires_at->isPast())
                                                    <span class="badge bg-danger">{{ trans('expired') }}</span>
                                                @elseif($job->expires_at->diffInDays() <= 7)
                                                    <span class="badge bg-warning">{{ trans('expiring_soon') }}</span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ number_format($job->views) }}</td>
                                        <td>
                                            <a href="{{ route('jobs.applicants', $job->id) }}" class="text-decoration-none">
                                                {{ $job->applicants_count }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($job->status == 'active')
                                                <span class="badge bg-success">{{ trans('active') }}</span>
                                            @elseif($job->status == 'closed')
                                                <span class="badge bg-secondary">{{ trans('closed') }}</span>
                                            @else
                                                <span class="badge bg-warning">{{ trans($job->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('jobs.edit', $job->id) }}" class="btn btn-outline-primary" 
                                                   title="{{ trans('edit') }}">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="{{ route('jobs.applicants', $job->id) }}" class="btn btn-outline-info" 
                                                   title="{{ trans('view_applicants') }}">
                                                    <i class="bi bi-people"></i>
                                                </a>
                                                <form action="{{ route('jobs.destroy', $job->id) }}" method="POST" 
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
                        {{ $jobs->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-briefcase" style="font-size: 48px; color: #ccc;"></i>
                        <h5 class="mt-3">{{ trans('no_jobs_posted_yet') }}</h5>
                        <p class="text-muted">{{ trans('start_by_posting_your_first_job') }}</p>
                        <a href="{{ route('jobs.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> {{ trans('post_job') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
