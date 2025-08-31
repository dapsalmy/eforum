@extends('layouts.user')

@section('content')
<div class="dashboard">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">{{ trans('recovery_codes') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>{{ trans('important') }}</strong>
                            {{ trans('save_recovery_codes_info') }}
                        </div>

                        <h5>{{ trans('your_recovery_codes') }}</h5>
                        <p>{{ trans('recovery_codes_usage') }}</p>
                        
                        <div class="bg-light p-3 rounded mb-4">
                            <div class="row">
                                @foreach($recoveryCodes as $code)
                                    <div class="col-md-6 mb-2">
                                        <code class="recovery-code">{{ $code }}</code>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-primary" onclick="downloadCodes()">
                                <i class="bi bi-download"></i> {{ trans('download_codes') }}
                            </button>
                            
                            <button type="button" class="btn btn-outline-primary" onclick="printCodes()">
                                <i class="bi bi-printer"></i> {{ trans('print_codes') }}
                            </button>
                            
                            <button type="button" class="btn btn-outline-primary" onclick="copyCodes()">
                                <i class="bi bi-clipboard"></i> {{ trans('copy_codes') }}
                            </button>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6>{{ trans('regenerate_codes') }}</h6>
                                <p class="text-muted mb-0">{{ trans('regenerate_codes_info') }}</p>
                            </div>
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#regenerateModal">
                                <i class="bi bi-arrow-clockwise"></i> {{ trans('regenerate') }}
                            </button>
                        </div>
                        
                        <hr class="my-4">
                        
                        <a href="{{ route('user.settings') }}" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> {{ trans('done_go_to_settings') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Regenerate Modal -->
<div class="modal fade" id="regenerateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('regenerate_recovery_codes') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('two-factor.regenerate-codes') }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        {{ trans('regenerate_warning') }}
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">{{ trans('confirm_password') }}</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                            id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('cancel') }}</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-arrow-clockwise"></i> {{ trans('regenerate_codes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.recovery-code {
    font-size: 1.1em;
    font-family: monospace;
    background: #f8f9fa;
    padding: 0.5rem;
    border-radius: 0.25rem;
    display: block;
    text-align: center;
}
</style>

<script>
function downloadCodes() {
    const codes = Array.from(document.querySelectorAll('.recovery-code')).map(el => el.textContent).join('\n');
    const blob = new Blob([`eForum Two-Factor Authentication Recovery Codes\n\n${codes}\n\nKeep these codes in a safe place.`], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'eforum-2fa-recovery-codes.txt';
    a.click();
    URL.revokeObjectURL(url);
}

function printCodes() {
    window.print();
}

function copyCodes() {
    const codes = Array.from(document.querySelectorAll('.recovery-code')).map(el => el.textContent).join('\n');
    navigator.clipboard.writeText(codes).then(() => {
        tata.success('Success', 'Recovery codes copied to clipboard');
    });
}
</script>
@endsection
