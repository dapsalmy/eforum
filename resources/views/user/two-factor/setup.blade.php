@extends('layouts.user')

@section('content')
<div class="dashboard">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">{{ trans('setup_two_factor_auth') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            {{ trans('two_factor_info') }}
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5>{{ trans('step_1_scan_qr') }}</h5>
                                <p>{{ trans('scan_qr_with_app') }}</p>
                                <div class="text-center mb-4">
                                    {!! $qrCode !!}
                                </div>
                                
                                <p class="text-muted small">
                                    {{ trans('cant_scan_qr') }}
                                    <button type="button" class="btn btn-link btn-sm" onclick="document.getElementById('manual-code').classList.toggle('d-none')">
                                        {{ trans('enter_code_manually') }}
                                    </button>
                                </p>
                                
                                <div id="manual-code" class="d-none">
                                    <div class="alert alert-secondary">
                                        <code>{{ $secret }}</code>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5>{{ trans('step_2_verify') }}</h5>
                                <p>{{ trans('enter_6_digit_code') }}</p>
                                
                                <form method="POST" action="{{ route('two-factor.enable') }}" data-validate="true">
                                    @csrf
                                    
                                    <div class="mb-3">
                                        <label for="code" class="form-label">{{ trans('verification_code') }}</label>
                                        <input type="text" 
                                            class="form-control form-control-lg text-center @error('code') is-invalid @enderror" 
                                            id="code" 
                                            name="code" 
                                            placeholder="000000"
                                            maxlength="6"
                                            pattern="[0-9]{6}"
                                            autocomplete="off"
                                            required>
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-shield-check"></i> {{ trans('enable_2fa') }}
                                    </button>
                                </form>
                                
                                <hr class="my-4">
                                
                                <h6>{{ trans('recommended_apps') }}</h6>
                                <ul class="list-unstyled">
                                    <li><i class="bi bi-check-circle text-success"></i> Google Authenticator</li>
                                    <li><i class="bi bi-check-circle text-success"></i> Microsoft Authenticator</li>
                                    <li><i class="bi bi-check-circle text-success"></i> Authy</li>
                                    <li><i class="bi bi-check-circle text-success"></i> 1Password</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
#manual-code code {
    font-size: 1.2em;
    letter-spacing: 0.1em;
}
</style>
@endsection
