@extends('layouts.front')

@section('content')

<section class="forum-home">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">{{ trans('track_new_visa_application') }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('visa.store') }}" method="POST" id="visaTrackingForm">
                            @csrf
                            
                            <!-- Basic Information -->
                            <h5 class="mb-3">{{ trans('visa_information') }}</h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ trans('destination_country') }} <span class="text-danger">*</span></label>
                                    <select name="country" class="form-select @error('country') is-invalid @enderror" required>
                                        <option value="">{{ trans('select_country') }}</option>
                                        <optgroup label="{{ trans('popular_destinations') }}">
                                            <option value="United States" {{ old('country') == 'United States' ? 'selected' : '' }}>🇺🇸 United States</option>
                                            <option value="United Kingdom" {{ old('country') == 'United Kingdom' ? 'selected' : '' }}>🇬🇧 United Kingdom</option>
                                            <option value="Canada" {{ old('country') == 'Canada' ? 'selected' : '' }}>🇨🇦 Canada</option>
                                            <option value="Australia" {{ old('country') == 'Australia' ? 'selected' : '' }}>🇦🇺 Australia</option>
                                            <option value="Germany" {{ old('country') == 'Germany' ? 'selected' : '' }}>🇩🇪 Germany</option>
                                            <option value="France" {{ old('country') == 'France' ? 'selected' : '' }}>🇫🇷 France</option>
                                        </optgroup>
                                        <optgroup label="{{ trans('other_countries') }}">
                                            <option value="Netherlands" {{ old('country') == 'Netherlands' ? 'selected' : '' }}>🇳🇱 Netherlands</option>
                                            <option value="Italy" {{ old('country') == 'Italy' ? 'selected' : '' }}>🇮🇹 Italy</option>
                                            <option value="Spain" {{ old('country') == 'Spain' ? 'selected' : '' }}>🇪🇸 Spain</option>
                                            <option value="Switzerland" {{ old('country') == 'Switzerland' ? 'selected' : '' }}>🇨🇭 Switzerland</option>
                                            <option value="Sweden" {{ old('country') == 'Sweden' ? 'selected' : '' }}>🇸🇪 Sweden</option>
                                            <option value="Norway" {{ old('country') == 'Norway' ? 'selected' : '' }}>🇳🇴 Norway</option>
                                            <option value="Denmark" {{ old('country') == 'Denmark' ? 'selected' : '' }}>🇩🇰 Denmark</option>
                                            <option value="Ireland" {{ old('country') == 'Ireland' ? 'selected' : '' }}>🇮🇪 Ireland</option>
                                            <option value="New Zealand" {{ old('country') == 'New Zealand' ? 'selected' : '' }}>🇳🇿 New Zealand</option>
                                            <option value="Singapore" {{ old('country') == 'Singapore' ? 'selected' : '' }}>🇸🇬 Singapore</option>
                                            <option value="United Arab Emirates" {{ old('country') == 'United Arab Emirates' ? 'selected' : '' }}>🇦🇪 UAE</option>
                                            <option value="South Africa" {{ old('country') == 'South Africa' ? 'selected' : '' }}>🇿🇦 South Africa</option>
                                        </optgroup>
                                    </select>
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ trans('visa_type') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="visa_type" class="form-control @error('visa_type') is-invalid @enderror" 
                                           value="{{ old('visa_type') }}" 
                                           placeholder="{{ trans('e.g. Tourist, Student, Work') }}" 
                                           list="visaTypes" required>
                                    <datalist id="visaTypes">
                                        <option value="Tourist/Visit">
                                        <option value="Business">
                                        <option value="Student">
                                        <option value="Work">
                                        <option value="Family/Spouse">
                                        <option value="Permanent Residence">
                                        <option value="Transit">
                                        <option value="Medical">
                                        <option value="Conference/Event">
                                    </datalist>
                                    @error('visa_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">{{ trans('current_status') }} <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="">{{ trans('select_status') }}</option>
                                    <option value="planning" {{ old('status') == 'planning' ? 'selected' : '' }}>
                                        📝 {{ trans('planning') }}
                                    </option>
                                    <option value="preparing" {{ old('status') == 'preparing' ? 'selected' : '' }}>
                                        📋 {{ trans('preparing_documents') }}
                                    </option>
                                    <option value="submitted" {{ old('status') == 'submitted' ? 'selected' : '' }}>
                                        ✉️ {{ trans('application_submitted') }}
                                    </option>
                                    <option value="interview_scheduled" {{ old('status') == 'interview_scheduled' ? 'selected' : '' }}>
                                        📅 {{ trans('interview_scheduled') }}
                                    </option>
                                    <option value="interview_completed" {{ old('status') == 'interview_completed' ? 'selected' : '' }}>
                                        ✅ {{ trans('interview_completed') }}
                                    </option>
                                    <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>
                                        🎉 {{ trans('approved') }}
                                    </option>
                                    <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>
                                        ❌ {{ trans('rejected') }}
                                    </option>
                                    <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>
                                        ⏸️ {{ trans('on_hold') }}
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <hr class="my-4">
                            
                            <!-- Important Dates -->
                            <h5 class="mb-3">{{ trans('important_dates') }}</h5>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ trans('application_date') }}</label>
                                    <input type="date" name="application_date" 
                                           class="form-control @error('application_date') is-invalid @enderror" 
                                           value="{{ old('application_date') }}">
                                    @error('application_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ trans('interview_date') }}</label>
                                    <input type="date" name="interview_date" 
                                           class="form-control @error('interview_date') is-invalid @enderror" 
                                           value="{{ old('interview_date') }}">
                                    @error('interview_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ trans('decision_date') }}</label>
                                    <input type="date" name="decision_date" 
                                           class="form-control @error('decision_date') is-invalid @enderror" 
                                           value="{{ old('decision_date') }}">
                                    @error('decision_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <!-- Document Checklist -->
                            <h5 class="mb-3">{{ trans('document_checklist') }}</h5>
                            
                            <div id="documentChecklist">
                                <div class="document-item mb-2">
                                    <div class="input-group">
                                        <input type="text" class="form-control" 
                                               placeholder="{{ trans('document_name') }}"
                                               name="documents[]">
                                        <select class="form-select" style="max-width: 150px;" name="document_status[]">
                                            <option value="pending">{{ trans('pending') }}</option>
                                            <option value="completed">{{ trans('completed') }}</option>
                                            <option value="not_required">{{ trans('not_required') }}</option>
                                        </select>
                                        <button type="button" class="btn btn-outline-danger remove-document">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addDocument">
                                <i class="bi bi-plus"></i> {{ trans('add_document') }}
                            </button>
                            
                            <hr class="my-4">
                            
                            <!-- Notes -->
                            <h5 class="mb-3">{{ trans('notes_and_experience') }}</h5>
                            
                            <div class="mb-3">
                                <label class="form-label">{{ trans('notes') }}</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                                          rows="4" placeholder="{{ trans('share_your_experience_tips') }}">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_public" 
                                           id="isPublic" value="1" {{ old('is_public') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isPublic">
                                        {{ trans('make_timeline_public') }}
                                        <small class="text-muted d-block">
                                            {{ trans('help_others_by_sharing') }}
                                        </small>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('visa.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> {{ trans('cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> {{ trans('start_tracking') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Tips Card -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ trans('visa_tracking_tips') }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li>{{ trans('update_status_regularly') }}</li>
                            <li>{{ trans('add_timeline_events') }}</li>
                            <li>{{ trans('track_documents_carefully') }}</li>
                            <li>{{ trans('share_to_help_others') }}</li>
                            <li>{{ trans('learn_from_similar_applications') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.document-item {
    animation: slideIn 0.3s ease-out;
}
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Document checklist management
    const checklistContainer = document.getElementById('documentChecklist');
    const addButton = document.getElementById('addDocument');
    
    // Common documents template
    const commonDocuments = [
        '{{ trans("passport") }}',
        '{{ trans("passport_photos") }}',
        '{{ trans("application_form") }}',
        '{{ trans("bank_statement") }}',
        '{{ trans("employment_letter") }}',
        '{{ trans("invitation_letter") }}',
        '{{ trans("travel_insurance") }}',
        '{{ trans("hotel_reservation") }}',
        '{{ trans("flight_itinerary") }}',
        '{{ trans("visa_fee_receipt") }}'
    ];
    
    // Add initial common documents
    if (checklistContainer.children.length === 1 && !checklistContainer.querySelector('input').value) {
        checklistContainer.innerHTML = '';
        commonDocuments.forEach(doc => {
            addDocumentItem(doc);
        });
    }
    
    function addDocumentItem(documentName = '') {
        const template = `
            <div class="document-item mb-2">
                <div class="input-group">
                    <input type="text" class="form-control" 
                           placeholder="{{ trans('document_name') }}"
                           name="documents[]" value="${documentName}">
                    <select class="form-select" style="max-width: 150px;" name="document_status[]">
                        <option value="pending">{{ trans('pending') }}</option>
                        <option value="completed">{{ trans('completed') }}</option>
                        <option value="not_required">{{ trans('not_required') }}</option>
                    </select>
                    <button type="button" class="btn btn-outline-danger remove-document">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
        checklistContainer.insertAdjacentHTML('beforeend', template);
    }
    
    addButton.addEventListener('click', () => addDocumentItem());
    
    checklistContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-document')) {
            e.target.closest('.document-item').remove();
        }
    });
    
    // Form submission preparation
    document.getElementById('visaTrackingForm').addEventListener('submit', function(e) {
        // Prepare documents checklist
        const documents = [];
        const documentInputs = checklistContainer.querySelectorAll('.document-item');
        
        documentInputs.forEach(item => {
            const name = item.querySelector('input[name="documents[]"]').value;
            const status = item.querySelector('select[name="document_status[]"]').value;
            if (name) {
                documents.push({ document: name, status: status });
            }
        });
        
        // Add hidden input with JSON data
        const checklistInput = document.createElement('input');
        checklistInput.type = 'hidden';
        checklistInput.name = 'documents_checklist';
        checklistInput.value = JSON.stringify(documents);
        this.appendChild(checklistInput);
    });
});
</script>

@endsection
