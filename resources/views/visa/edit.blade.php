@extends('layouts.front')

@section('content')

<section class="forum-home">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">{{ trans('edit_visa_tracking') }}</h4>
                        <form action="{{ route('visa.destroy', $tracking->id) }}" method="POST" 
                              onsubmit="return confirm('{{ trans('confirm_delete_tracking') }}');" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i> {{ trans('delete') }}
                            </button>
                        </form>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('visa.update', $tracking->id) }}" method="POST" id="visaTrackingForm">
                            @csrf
                            @method('PUT')
                            
                            <!-- Basic Information -->
                            <h5 class="mb-3">{{ trans('visa_information') }}</h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ trans('destination_country') }} <span class="text-danger">*</span></label>
                                    <select name="country" class="form-select @error('country') is-invalid @enderror" required>
                                        <option value="">{{ trans('select_country') }}</option>
                                        <optgroup label="{{ trans('popular_destinations') }}">
                                            <option value="United States" {{ old('country', $tracking->country) == 'United States' ? 'selected' : '' }}>🇺🇸 United States</option>
                                            <option value="United Kingdom" {{ old('country', $tracking->country) == 'United Kingdom' ? 'selected' : '' }}>🇬🇧 United Kingdom</option>
                                            <option value="Canada" {{ old('country', $tracking->country) == 'Canada' ? 'selected' : '' }}>🇨🇦 Canada</option>
                                            <option value="Australia" {{ old('country', $tracking->country) == 'Australia' ? 'selected' : '' }}>🇦🇺 Australia</option>
                                            <option value="Germany" {{ old('country', $tracking->country) == 'Germany' ? 'selected' : '' }}>🇩🇪 Germany</option>
                                            <option value="France" {{ old('country', $tracking->country) == 'France' ? 'selected' : '' }}>🇫🇷 France</option>
                                        </optgroup>
                                        <optgroup label="{{ trans('other_countries') }}">
                                            <option value="Netherlands" {{ old('country', $tracking->country) == 'Netherlands' ? 'selected' : '' }}>🇳🇱 Netherlands</option>
                                            <option value="Italy" {{ old('country', $tracking->country) == 'Italy' ? 'selected' : '' }}>🇮🇹 Italy</option>
                                            <option value="Spain" {{ old('country', $tracking->country) == 'Spain' ? 'selected' : '' }}>🇪🇸 Spain</option>
                                            <option value="Switzerland" {{ old('country', $tracking->country) == 'Switzerland' ? 'selected' : '' }}>🇨🇭 Switzerland</option>
                                            <option value="Sweden" {{ old('country', $tracking->country) == 'Sweden' ? 'selected' : '' }}>🇸🇪 Sweden</option>
                                            <option value="Norway" {{ old('country', $tracking->country) == 'Norway' ? 'selected' : '' }}>🇳🇴 Norway</option>
                                            <option value="Denmark" {{ old('country', $tracking->country) == 'Denmark' ? 'selected' : '' }}>🇩🇰 Denmark</option>
                                            <option value="Ireland" {{ old('country', $tracking->country) == 'Ireland' ? 'selected' : '' }}>🇮🇪 Ireland</option>
                                            <option value="New Zealand" {{ old('country', $tracking->country) == 'New Zealand' ? 'selected' : '' }}>🇳🇿 New Zealand</option>
                                            <option value="Singapore" {{ old('country', $tracking->country) == 'Singapore' ? 'selected' : '' }}>🇸🇬 Singapore</option>
                                            <option value="United Arab Emirates" {{ old('country', $tracking->country) == 'United Arab Emirates' ? 'selected' : '' }}>🇦🇪 UAE</option>
                                            <option value="South Africa" {{ old('country', $tracking->country) == 'South Africa' ? 'selected' : '' }}>🇿🇦 South Africa</option>
                                        </optgroup>
                                    </select>
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ trans('visa_type') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="visa_type" class="form-control @error('visa_type') is-invalid @enderror" 
                                           value="{{ old('visa_type', $tracking->visa_type) }}" 
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
                                    <option value="planning" {{ old('status', $tracking->status) == 'planning' ? 'selected' : '' }}>
                                        📝 {{ trans('planning') }}
                                    </option>
                                    <option value="preparing" {{ old('status', $tracking->status) == 'preparing' ? 'selected' : '' }}>
                                        📋 {{ trans('preparing_documents') }}
                                    </option>
                                    <option value="submitted" {{ old('status', $tracking->status) == 'submitted' ? 'selected' : '' }}>
                                        ✉️ {{ trans('application_submitted') }}
                                    </option>
                                    <option value="interview_scheduled" {{ old('status', $tracking->status) == 'interview_scheduled' ? 'selected' : '' }}>
                                        📅 {{ trans('interview_scheduled') }}
                                    </option>
                                    <option value="interview_completed" {{ old('status', $tracking->status) == 'interview_completed' ? 'selected' : '' }}>
                                        ✅ {{ trans('interview_completed') }}
                                    </option>
                                    <option value="approved" {{ old('status', $tracking->status) == 'approved' ? 'selected' : '' }}>
                                        🎉 {{ trans('approved') }}
                                    </option>
                                    <option value="rejected" {{ old('status', $tracking->status) == 'rejected' ? 'selected' : '' }}>
                                        ❌ {{ trans('rejected') }}
                                    </option>
                                    <option value="on_hold" {{ old('status', $tracking->status) == 'on_hold' ? 'selected' : '' }}>
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
                                           value="{{ old('application_date', $tracking->application_date?->format('Y-m-d')) }}">
                                    @error('application_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ trans('interview_date') }}</label>
                                    <input type="date" name="interview_date" 
                                           class="form-control @error('interview_date') is-invalid @enderror" 
                                           value="{{ old('interview_date', $tracking->interview_date?->format('Y-m-d')) }}">
                                    @error('interview_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ trans('decision_date') }}</label>
                                    <input type="date" name="decision_date" 
                                           class="form-control @error('decision_date') is-invalid @enderror" 
                                           value="{{ old('decision_date', $tracking->decision_date?->format('Y-m-d')) }}">
                                    @error('decision_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <!-- Timeline Events -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">{{ trans('timeline_events') }}</h5>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addTimelineModal">
                                    <i class="bi bi-plus"></i> {{ trans('add_event') }}
                                </button>
                            </div>
                            
                            <div id="timelineEvents">
                                @if($tracking->timeline && count($tracking->timeline) > 0)
                                    @foreach($tracking->timeline as $event)
                                        <div class="timeline-event mb-3 p-3 border rounded">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h6 class="mb-1">{{ $event['event'] ?? '' }}</h6>
                                                    <p class="mb-1 text-muted">{{ $event['description'] ?? '' }}</p>
                                                    <small class="text-muted">
                                                        <i class="bi bi-calendar"></i> {{ $event['date'] ?? '' }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted">{{ trans('no_timeline_events_yet') }}</p>
                                @endif
                            </div>
                            
                            <hr class="my-4">
                            
                            <!-- Document Checklist -->
                            <h5 class="mb-3">{{ trans('document_checklist') }}</h5>
                            
                            <div id="documentChecklist">
                                @if($tracking->documents_checklist && count($tracking->documents_checklist) > 0)
                                    @foreach($tracking->documents_checklist as $doc)
                                        <div class="document-item mb-2">
                                            <div class="input-group">
                                                <input type="text" class="form-control" 
                                                       placeholder="{{ trans('document_name') }}"
                                                       name="documents[]" value="{{ $doc['document'] ?? '' }}">
                                                <select class="form-select" style="max-width: 150px;" name="document_status[]">
                                                    <option value="pending" {{ ($doc['status'] ?? '') == 'pending' ? 'selected' : '' }}>{{ trans('pending') }}</option>
                                                    <option value="completed" {{ ($doc['status'] ?? '') == 'completed' ? 'selected' : '' }}>{{ trans('completed') }}</option>
                                                    <option value="not_required" {{ ($doc['status'] ?? '') == 'not_required' ? 'selected' : '' }}>{{ trans('not_required') }}</option>
                                                </select>
                                                <button type="button" class="btn btn-outline-danger remove-document">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
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
                                @endif
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
                                          rows="4" placeholder="{{ trans('share_your_experience_tips') }}">{{ old('notes', $tracking->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_public" 
                                           id="isPublic" value="1" {{ old('is_public', $tracking->is_public) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isPublic">
                                        {{ trans('make_timeline_public') }}
                                        <small class="text-muted d-block">
                                            {{ trans('help_others_by_sharing') }}
                                        </small>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('visa.show', $tracking->id) }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> {{ trans('cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> {{ trans('update_tracking') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add Timeline Event Modal -->
<div class="modal fade" id="addTimelineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('add_timeline_event') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="timelineForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ trans('date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ trans('event') }} <span class="text-danger">*</span></label>
                        <input type="text" name="event" class="form-control" 
                               placeholder="{{ trans('e.g. Documents submitted') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ trans('description') }}</label>
                        <textarea name="description" class="form-control" rows="3" 
                                  placeholder="{{ trans('additional_details') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ trans('add_event') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Document checklist management
    const checklistContainer = document.getElementById('documentChecklist');
    const addButton = document.getElementById('addDocument');
    
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
    
    // Timeline event form
    document.getElementById('timelineForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        fetch('{{ route("visa.timeline", $tracking->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                date: this.date.value,
                event: this.event.value,
                description: this.description.value
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
