@extends('layouts.front')

@section('content')
<section class="forum-home">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="error-page">
                    <h1 class="error-code">500</h1>
                    <h2 class="error-title">{{ trans('server_error') }}</h2>
                    <p class="error-message">
                        {{ trans('sorry_server_error') }}
                    </p>
                    <div class="error-actions">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="bi bi-house"></i> {{ trans('go_home') }}
                        </a>
                        <a href="javascript:location.reload();" class="btn btn-secondary">
                            <i class="bi bi-arrow-clockwise"></i> {{ trans('try_again') }}
                        </a>
                    </div>
                    <div class="error-info mt-4">
                        <p class="text-muted">
                            {{ trans('error_reported') }}
                        </p>
                        @if(app()->environment('local'))
                            <div class="alert alert-danger text-start mt-3">
                                <strong>Debug Info:</strong><br>
                                {{ $exception->getMessage() ?? 'Unknown error' }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.error-page {
    padding: 60px 0;
}
.error-code {
    font-size: 120px;
    font-weight: 700;
    color: #dc3545;
    margin: 0;
    line-height: 1;
}
.error-title {
    font-size: 32px;
    margin: 20px 0;
}
.error-message {
    font-size: 18px;
    color: #666;
    margin-bottom: 30px;
}
.error-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}
.error-info {
    margin-top: 40px;
}
</style>
@endsection
