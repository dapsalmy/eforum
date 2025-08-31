@extends('layouts.front')

@section('content')
<section class="forum-home">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header text-center">
                        <h4 class="mb-0">{{ trans('two_factor_verification') }}</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-center text-muted mb-4">
                            {{ trans('enter_2fa_code_or_recovery') }}
                        </p>
                        
                        <form method="POST" action="{{ route('two-factor.verify.post') }}" data-validate="true">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="code" class="form-label">{{ trans('verification_code') }}</label>
                                <input type="text" 
                                    class="form-control form-control-lg text-center @error('code') is-invalid @enderror" 
                                    id="code" 
                                    name="code" 
                                    placeholder="000000 or recovery-code"
                                    autocomplete="off"
                                    autofocus
                                    required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    {{ trans('enter_6_digit_or_recovery') }}
                                </small>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="bi bi-shield-check"></i> {{ trans('verify') }}
                            </button>
                            
                            <div class="text-center">
                                <a href="{{ route('logout') }}" class="text-muted" 
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    {{ trans('cancel_and_logout') }}
                                </a>
                            </div>
                        </form>
                        
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <p class="text-muted">
                        {{ trans('lost_device_question') }} 
                        <a href="{{ route('password.request') }}">{{ trans('reset_password') }}</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Auto-format code input for 6-digit codes
document.getElementById('code').addEventListener('input', function(e) {
    let value = e.target.value.replace(/[^0-9a-zA-Z-]/g, '');
    
    // If it looks like a 6-digit code, format it
    if (/^\d+$/.test(value) && value.length <= 6) {
        e.target.value = value;
    } else {
        // Otherwise, leave it as is (might be a recovery code)
        e.target.value = value;
    }
});
</script>
@endsection
