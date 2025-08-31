@extends('layouts.front')

@section('content')

<section class="forum-home">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">{{ trans('post_new_job') }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('jobs.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Basic Information -->
                            <h5 class="mb-3">{{ trans('basic_information') }}</h5>
                            
                            <div class="mb-3">
                                <label class="form-label">{{ trans('job_title') }} <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                       value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">{{ trans('category') }} <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                    <option value="">{{ trans('select_category') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ trans('job_type') }} <span class="text-danger">*</span></label>
                                    <select name="job_type" class="form-select @error('job_type') is-invalid @enderror" required>
                                        <option value="">{{ trans('select_type') }}</option>
                                        <option value="remote" {{ old('job_type') == 'remote' ? 'selected' : '' }}>{{ trans('remote') }}</option>
                                        <option value="hybrid" {{ old('job_type') == 'hybrid' ? 'selected' : '' }}>{{ trans('hybrid') }}</option>
                                        <option value="onsite" {{ old('job_type') == 'onsite' ? 'selected' : '' }}>{{ trans('onsite') }}</option>
                                    </select>
                                    @error('job_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ trans('employment_type') }} <span class="text-danger">*</span></label>
                                    <select name="employment_type" class="form-select @error('employment_type') is-invalid @enderror" required>
                                        <option value="">{{ trans('select_employment_type') }}</option>
                                        <option value="full-time" {{ old('employment_type') == 'full-time' ? 'selected' : '' }}>{{ trans('full_time') }}</option>
                                        <option value="part-time" {{ old('employment_type') == 'part-time' ? 'selected' : '' }}>{{ trans('part_time') }}</option>
                                        <option value="contract" {{ old('employment_type') == 'contract' ? 'selected' : '' }}>{{ trans('contract') }}</option>
                                        <option value="internship" {{ old('employment_type') == 'internship' ? 'selected' : '' }}>{{ trans('internship') }}</option>
                                        <option value="freelance" {{ old('employment_type') == 'freelance' ? 'selected' : '' }}>{{ trans('freelance') }}</option>
                                    </select>
                                    @error('employment_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">{{ trans('location') }}</label>
                                <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" 
                                       value="{{ old('location') }}" placeholder="{{ trans('e.g. Lagos, Nigeria or Remote') }}">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <hr class="my-4">
                            
                            <!-- Company Information -->
                            <h5 class="mb-3">{{ trans('company_information') }}</h5>
                            
                            <div class="mb-3">
                                <label class="form-label">{{ trans('company_name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" 
                                       value="{{ old('company_name') }}" required>
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">{{ trans('company_website') }}</label>
                                <input type="url" name="company_website" class="form-control @error('company_website') is-invalid @enderror" 
                                       value="{{ old('company_website') }}" placeholder="https://">
                                @error('company_website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">{{ trans('company_logo') }}</label>
                                <input type="file" name="company_logo" class="form-control @error('company_logo') is-invalid @enderror" 
                                       accept="image/*">
                                <small class="text-muted">{{ trans('max_file_size_2mb') }}</small>
                                @error('company_logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <hr class="my-4">
                            
                            <!-- Salary Information -->
                            <h5 class="mb-3">{{ trans('salary_information') }}</h5>
                            
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">{{ trans('currency') }} <span class="text-danger">*</span></label>
                                    <select name="salary_currency" class="form-select @error('salary_currency') is-invalid @enderror" required>
                                        <option value="NGN" {{ old('salary_currency', 'NGN') == 'NGN' ? 'selected' : '' }}>₦ NGN</option>
                                        <option value="USD" {{ old('salary_currency') == 'USD' ? 'selected' : '' }}>$ USD</option>
                                        <option value="EUR" {{ old('salary_currency') == 'EUR' ? 'selected' : '' }}>€ EUR</option>
                                        <option value="GBP" {{ old('salary_currency') == 'GBP' ? 'selected' : '' }}>£ GBP</option>
                                    </select>
                                    @error('salary_currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">{{ trans('min_salary') }}</label>
                                    <input type="number" name="salary_min" class="form-control @error('salary_min') is-invalid @enderror" 
                                           value="{{ old('salary_min') }}" min="0">
                                    @error('salary_min')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">{{ trans('max_salary') }}</label>
                                    <input type="number" name="salary_max" class="form-control @error('salary_max') is-invalid @enderror" 
                                           value="{{ old('salary_max') }}" min="0">
                                    @error('salary_max')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">{{ trans('salary_period') }} <span class="text-danger">*</span></label>
                                    <select name="salary_period" class="form-select @error('salary_period') is-invalid @enderror" required>
                                        <option value="monthly" {{ old('salary_period', 'monthly') == 'monthly' ? 'selected' : '' }}>{{ trans('monthly') }}</option>
                                        <option value="yearly" {{ old('salary_period') == 'yearly' ? 'selected' : '' }}>{{ trans('yearly') }}</option>
                                        <option value="weekly" {{ old('salary_period') == 'weekly' ? 'selected' : '' }}>{{ trans('weekly') }}</option>
                                        <option value="daily" {{ old('salary_period') == 'daily' ? 'selected' : '' }}>{{ trans('daily') }}</option>
                                        <option value="hourly" {{ old('salary_period') == 'hourly' ? 'selected' : '' }}>{{ trans('hourly') }}</option>
                                    </select>
                                    @error('salary_period')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <!-- Job Details -->
                            <h5 class="mb-3">{{ trans('job_details') }}</h5>
                            
                            <div class="mb-3">
                                <label class="form-label">{{ trans('job_description') }} <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                          rows="6" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">{{ trans('requirements') }} <span class="text-danger">*</span></label>
                                <textarea name="requirements" class="form-control @error('requirements') is-invalid @enderror" 
                                          rows="6" required placeholder="{{ trans('list_job_requirements') }}">{{ old('requirements') }}</textarea>
                                @error('requirements')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">{{ trans('benefits') }}</label>
                                <textarea name="benefits" class="form-control @error('benefits') is-invalid @enderror" 
                                          rows="4" placeholder="{{ trans('list_job_benefits') }}">{{ old('benefits') }}</textarea>
                                @error('benefits')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">{{ trans('required_skills') }}</label>
                                <input type="text" name="required_skills_input" class="form-control" 
                                       placeholder="{{ trans('type_and_press_enter') }}" id="requiredSkillsInput">
                                <div id="requiredSkillsList" class="mt-2"></div>
                                <input type="hidden" name="required_skills" id="requiredSkillsHidden">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">{{ trans('preferred_skills') }}</label>
                                <input type="text" name="preferred_skills_input" class="form-control" 
                                       placeholder="{{ trans('type_and_press_enter') }}" id="preferredSkillsInput">
                                <div id="preferredSkillsList" class="mt-2"></div>
                                <input type="hidden" name="preferred_skills" id="preferredSkillsHidden">
                            </div>
                            
                            <hr class="my-4">
                            
                            <!-- Visa Sponsorship -->
                            <h5 class="mb-3">{{ trans('visa_sponsorship') }}</h5>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="visa_sponsorship" 
                                           id="visaSponsorship" value="1" {{ old('visa_sponsorship') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="visaSponsorship">
                                        {{ trans('visa_sponsorship_available') }}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3" id="visaTypesDiv" style="display: none;">
                                <label class="form-label">{{ trans('visa_types_sponsored') }}</label>
                                <input type="text" name="visa_types" class="form-control @error('visa_types') is-invalid @enderror" 
                                       value="{{ old('visa_types') }}" placeholder="{{ trans('e.g. H1B, Green Card, etc.') }}">
                                @error('visa_types')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <hr class="my-4">
                            
                            <!-- Application Details -->
                            <h5 class="mb-3">{{ trans('application_details') }}</h5>
                            
                            <div class="mb-3">
                                <label class="form-label">{{ trans('how_to_apply') }} <span class="text-danger">*</span></label>
                                <textarea name="how_to_apply" class="form-control @error('how_to_apply') is-invalid @enderror" 
                                          rows="4" required placeholder="{{ trans('explain_application_process') }}">{{ old('how_to_apply') }}</textarea>
                                @error('how_to_apply')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">{{ trans('application_url') }}</label>
                                <input type="url" name="application_url" class="form-control @error('application_url') is-invalid @enderror" 
                                       value="{{ old('application_url') }}" placeholder="https://">
                                @error('application_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">{{ trans('application_email') }}</label>
                                <input type="email" name="application_email" class="form-control @error('application_email') is-invalid @enderror" 
                                       value="{{ old('application_email') }}">
                                @error('application_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">{{ trans('application_deadline') }}</label>
                                <input type="date" name="deadline" class="form-control @error('deadline') is-invalid @enderror" 
                                       value="{{ old('deadline') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                @error('deadline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('jobs.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> {{ trans('cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> {{ trans('post_job') }}
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
.skill-tag {
    display: inline-block;
    background: #e9ecef;
    padding: 5px 10px;
    margin: 2px;
    border-radius: 20px;
    font-size: 14px;
}
.skill-tag .remove {
    margin-left: 5px;
    cursor: pointer;
    color: #dc3545;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Visa sponsorship toggle
    const visaCheckbox = document.getElementById('visaSponsorship');
    const visaTypesDiv = document.getElementById('visaTypesDiv');
    
    visaCheckbox.addEventListener('change', function() {
        visaTypesDiv.style.display = this.checked ? 'block' : 'none';
    });
    
    // Skills management
    function setupSkillsInput(inputId, listId, hiddenId) {
        const input = document.getElementById(inputId);
        const list = document.getElementById(listId);
        const hidden = document.getElementById(hiddenId);
        let skills = [];
        
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const skill = this.value.trim();
                if (skill && !skills.includes(skill)) {
                    skills.push(skill);
                    updateSkillsList();
                    this.value = '';
                }
            }
        });
        
        function updateSkillsList() {
            list.innerHTML = skills.map(skill => 
                `<span class="skill-tag">${skill} <span class="remove" data-skill="${skill}">&times;</span></span>`
            ).join('');
            
            hidden.value = JSON.stringify(skills);
            
            // Add remove handlers
            list.querySelectorAll('.remove').forEach(btn => {
                btn.addEventListener('click', function() {
                    const skillToRemove = this.dataset.skill;
                    skills = skills.filter(s => s !== skillToRemove);
                    updateSkillsList();
                });
            });
        }
    }
    
    setupSkillsInput('requiredSkillsInput', 'requiredSkillsList', 'requiredSkillsHidden');
    setupSkillsInput('preferredSkillsInput', 'preferredSkillsList', 'preferredSkillsHidden');
});
</script>

@endsection
