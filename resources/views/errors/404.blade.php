@extends('layouts.front')

@section('content')
<section class="forum-home">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="error-page">
                    <h1 class="error-code">404</h1>
                    <h2 class="error-title">{{ trans('page_not_found') }}</h2>
                    <p class="error-message">
                        {{ trans('sorry_page_not_found') }}
                    </p>
                    <div class="error-actions">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="bi bi-house"></i> {{ trans('go_home') }}
                        </a>
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> {{ trans('go_back') }}
                        </a>
                    </div>
                    <div class="error-search mt-4">
                        <p>{{ trans('try_searching') }}</p>
                        <form action="{{ route('search') }}" method="GET" class="d-flex justify-content-center">
                            <div class="input-group" style="max-width: 500px;">
                                <input type="text" name="q" class="form-control" placeholder="{{ trans('search_placeholder') }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
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
    color: var(--nigeria-green, #008751);
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
.error-search {
    margin-top: 40px;
}
</style>
@endsection