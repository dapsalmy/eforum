/**
 * eForum Client-side Form Validation
 * Provides real-time validation for all forms
 */

(function() {
    'use strict';

    // Validation rules
    const validationRules = {
        required: (value) => value.trim() !== '',
        email: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
        minLength: (value, length) => value.length >= length,
        maxLength: (value, length) => value.length <= length,
        numeric: (value) => /^\d+$/.test(value),
        decimal: (value) => /^\d+(\.\d{1,2})?$/.test(value),
        url: (value) => {
            try {
                new URL(value);
                return true;
            } catch {
                return false;
            }
        },
        nigerianPhone: (value) => /^(\+?234|0)[789]\d{9}$/.test(value.replace(/\s/g, '')),
        username: (value) => /^[a-zA-Z0-9_-]{3,20}$/.test(value),
        password: (value) => value.length >= 8,
        passwordMatch: (value, matchValue) => value === matchValue,
        date: (value) => !isNaN(Date.parse(value)),
        futureDate: (value) => new Date(value) > new Date(),
        pastDate: (value) => new Date(value) < new Date(),
        fileSize: (file, maxSizeMB) => file.size <= maxSizeMB * 1024 * 1024,
        fileType: (file, allowedTypes) => {
            const extension = file.name.split('.').pop().toLowerCase();
            return allowedTypes.includes(extension);
        }
    };

    // Error messages
    const errorMessages = {
        required: 'This field is required',
        email: 'Please enter a valid email address',
        minLength: 'Must be at least {0} characters',
        maxLength: 'Must not exceed {0} characters',
        numeric: 'Please enter numbers only',
        decimal: 'Please enter a valid amount',
        url: 'Please enter a valid URL',
        nigerianPhone: 'Please enter a valid Nigerian phone number',
        username: 'Username must be 3-20 characters, letters, numbers, - and _ only',
        password: 'Password must be at least 8 characters',
        passwordMatch: 'Passwords do not match',
        date: 'Please enter a valid date',
        futureDate: 'Date must be in the future',
        pastDate: 'Date must be in the past',
        fileSize: 'File size must not exceed {0}MB',
        fileType: 'File type must be: {0}'
    };

    // Form-specific validation configurations
    const formValidations = {
        // Registration form
        'registration-form': {
            'name': ['required', ['minLength', 2], ['maxLength', 255]],
            'username': ['required', 'username'],
            'email': ['required', 'email'],
            'password': ['required', 'password'],
            'password_confirmation': ['required', ['passwordMatch', 'password']],
            'phone_number': ['nigerianPhone'],
            'agree': ['required']
        },

        // Login form
        'login-form': {
            'email': ['required', 'email'],
            'password': ['required']
        },

        // Job posting form
        'job-posting-form': {
            'title': ['required', ['minLength', 10], ['maxLength', 255]],
            'company_name': ['required', ['minLength', 2], ['maxLength', 255]],
            'category_id': ['required'],
            'job_type': ['required'],
            'description': ['required', ['minLength', 50]],
            'salary_min': ['decimal'],
            'salary_max': ['decimal'],
            'application_email': ['email'],
            'application_link': ['url'],
            'deadline': ['required', 'date', 'futureDate'],
            'company_logo': [['fileSize', 2], ['fileType', ['jpg', 'jpeg', 'png']]]
        },

        // Visa tracking form
        'visa-tracking-form': {
            'visa_type': ['required', ['minLength', 3]],
            'country': ['required'],
            'status': ['required'],
            'application_date': ['date', 'pastDate'],
            'interview_date': ['date'],
            'decision_date': ['date'],
            'notes': [['minLength', 10]]
        },

        // Payment form
        'payment-form': {
            'amount': ['required', 'decimal', ['minLength', 3]],
            'gateway': ['required']
        },

        // Profile update form
        'profile-form': {
            'name': ['required', ['minLength', 2], ['maxLength', 255]],
            'email': ['required', 'email'],
            'bio': [['maxLength', 1000]],
            'tagline': [['maxLength', 255]],
            'phone_number': ['nigerianPhone'],
            'website': ['url']
        },

        // Comment form
        'comment-form': {
            'body': ['required', ['minLength', 2]]
        },

        // Post form
        'post-form': {
            'title': ['required', ['minLength', 10], ['maxLength', 255]],
            'body': ['required', ['minLength', 50]],
            'category': ['required']
        }
    };

    // Validate a single field
    function validateField(field, rules, formData) {
        const value = field.type === 'file' ? field.files[0] : field.value;
        const errors = [];

        for (const rule of rules) {
            let ruleName, ruleParam;

            if (Array.isArray(rule)) {
                [ruleName, ruleParam] = rule;
            } else {
                ruleName = rule;
            }

            // Skip validation for empty optional fields
            if (!validationRules.required(field.value) && ruleName !== 'required') {
                continue;
            }

            // Special case for password match
            if (ruleName === 'passwordMatch' && ruleParam) {
                const matchField = formData.get(ruleParam);
                if (!validationRules.passwordMatch(value, matchField)) {
                    errors.push(errorMessages.passwordMatch);
                }
                continue;
            }

            // Apply validation rule
            const validator = validationRules[ruleName];
            if (validator) {
                const isValid = ruleParam !== undefined ? validator(value, ruleParam) : validator(value);
                if (!isValid) {
                    let message = errorMessages[ruleName];
                    if (ruleParam !== undefined) {
                        message = message.replace('{0}', Array.isArray(ruleParam) ? ruleParam.join(', ') : ruleParam);
                    }
                    errors.push(message);
                }
            }
        }

        return errors;
    }

    // Show field error
    function showFieldError(field, errors) {
        clearFieldError(field);
        
        if (errors.length === 0) {
            field.classList.add('is-valid');
            field.classList.remove('is-invalid');
            return;
        }

        field.classList.add('is-invalid');
        field.classList.remove('is-valid');

        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = errors[0]; // Show first error

        // Insert error message
        if (field.parentElement.classList.contains('input-group')) {
            field.parentElement.insertAdjacentElement('afterend', errorDiv);
        } else {
            field.insertAdjacentElement('afterend', errorDiv);
        }
    }

    // Clear field error
    function clearFieldError(field) {
        field.classList.remove('is-invalid', 'is-valid');
        const errorDiv = field.parentElement.querySelector('.invalid-feedback') || 
                         field.parentElement.parentElement.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.remove();
        }
    }

    // Validate entire form
    function validateForm(form) {
        const formId = form.id || form.className.split(' ')[0];
        const validationConfig = formValidations[formId];
        
        if (!validationConfig) {
            return true; // No validation configured
        }

        const formData = new FormData(form);
        let isValid = true;

        // Validate each field
        for (const [fieldName, rules] of Object.entries(validationConfig)) {
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (!field) continue;

            const errors = validateField(field, rules, formData);
            showFieldError(field, errors);

            if (errors.length > 0) {
                isValid = false;
            }
        }

        return isValid;
    }

    // Real-time validation
    function setupRealtimeValidation(form) {
        const formId = form.id || form.className.split(' ')[0];
        const validationConfig = formValidations[formId];
        
        if (!validationConfig) return;

        // Add validation on blur and input
        for (const fieldName of Object.keys(validationConfig)) {
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (!field) continue;

            // Validate on blur
            field.addEventListener('blur', function() {
                const formData = new FormData(form);
                const errors = validateField(this, validationConfig[fieldName], formData);
                showFieldError(this, errors);
            });

            // Clear error on input
            field.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    clearFieldError(this);
                }
            });

            // Special handling for file inputs
            if (field.type === 'file') {
                field.addEventListener('change', function() {
                    const formData = new FormData(form);
                    const errors = validateField(this, validationConfig[fieldName], formData);
                    showFieldError(this, errors);
                });
            }
        }
    }

    // Initialize validation for all forms
    function initializeValidation() {
        // Find all forms that need validation
        const forms = document.querySelectorAll('form[data-validate="true"], .needs-validation');
        
        forms.forEach(form => {
            // Setup real-time validation
            setupRealtimeValidation(form);

            // Validate on submit
            form.addEventListener('submit', function(e) {
                if (!validateForm(this)) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Focus first invalid field
                    const firstInvalid = this.querySelector('.is-invalid');
                    if (firstInvalid) {
                        firstInvalid.focus();
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }

                    // Show alert
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                    alertDiv.innerHTML = `
                        <strong>Validation Error!</strong> Please fix the errors below and try again.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    this.insertAdjacentElement('afterbegin', alertDiv);

                    // Auto-remove alert after 5 seconds
                    setTimeout(() => {
                        alertDiv.remove();
                    }, 5000);
                }
            });
        });

        // Add validation to dynamically added forms
        const observer = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType === 1 && node.tagName === 'FORM') {
                        if (node.dataset.validate === 'true' || node.classList.contains('needs-validation')) {
                            setupRealtimeValidation(node);
                        }
                    }
                });
            });
        });

        observer.observe(document.body, { childList: true, subtree: true });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeValidation);
    } else {
        initializeValidation();
    }

    // Export for use in other scripts
    window.eForumValidation = {
        validateForm,
        validateField,
        addValidation: (formId, rules) => {
            formValidations[formId] = rules;
        }
    };
})();
