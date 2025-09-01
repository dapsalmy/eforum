/**
 * eForum Form Validation Library
 * Provides comprehensive client-side validation for better UX
 */

class EForumValidator {
    constructor() {
        this.rules = {};
        this.messages = {};
        this.init();
    }

    init() {
        this.setupValidationRules();
        this.bindEvents();
    }

    setupValidationRules() {
        this.addRule('email', (value) => {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(value);
        }, 'Please enter a valid email address');

        this.addRule('password', (value) => {
            return value.length >= 4;
        }, 'Password must be at least 4 characters long');

        this.addRule('password_confirmation', (value, element) => {
            const passwordField = document.querySelector('input[name="password"]');
            return passwordField && value === passwordField.value;
        }, 'Password confirmation does not match');

        this.addRule('required', (value) => {
            return value.trim().length > 0;
        }, 'This field is required');

        this.addRule('username', (value) => {
            const usernameRegex = /^[a-zA-Z0-9_]{3,20}$/;
            return usernameRegex.test(value);
        }, 'Username must be 3-20 characters and contain only letters, numbers, and underscores');

        this.addRule('nigerian_phone', (value) => {
            const phoneRegex = /^(\+234|234|0)?[789][01]\d{8}$/;
            return phoneRegex.test(value.replace(/\s+/g, ''));
        }, 'Please enter a valid Nigerian phone number');

        this.addRule('amount', (value) => {
            const amount = parseFloat(value);
            return !isNaN(amount) && amount >= 100;
        }, 'Amount must be at least ₦100');

        this.addRule('account_number', (value) => {
            const accountRegex = /^\d{10}$/;
            return accountRegex.test(value);
        }, 'Account number must be exactly 10 digits');

        this.addRule('name', (value) => {
            const nameRegex = /^[a-zA-Z\s]{2,50}$/;
            return nameRegex.test(value);
        }, 'Name must be 2-50 characters and contain only letters and spaces');

        this.addRule('nigerian_account', (value) => {
            const accountRegex = /^\d{10}$/;
            return accountRegex.test(value);
        }, 'Please enter a valid 10-digit account number');
    }

    addRule(name, validator, message) {
        this.rules[name] = validator;
        this.messages[name] = message;
    }

    bindEvents() {
        document.addEventListener('input', (e) => {
            if (e.target.hasAttribute('data-validate')) {
                this.validateField(e.target);
            }
        });

        document.addEventListener('blur', (e) => {
            if (e.target.hasAttribute('data-validate')) {
                this.validateField(e.target);
            }
        }, true);

        document.addEventListener('submit', (e) => {
            if (e.target.hasAttribute('data-validate-form')) {
                if (!this.validateForm(e.target)) {
                    e.preventDefault();
                }
            }
        });
    }

    validateField(field) {
        const rules = field.getAttribute('data-validate').split('|');
        const value = field.value;
        let isValid = true;
        let errorMessage = '';

        for (let rule of rules) {
            const [ruleName, ...params] = rule.split(':');
            
            if (this.rules[ruleName]) {
                if (!this.rules[ruleName](value, field, params)) {
                    isValid = false;
                    errorMessage = this.messages[ruleName];
                    break;
                }
            }
        }

        this.showFieldValidation(field, isValid, errorMessage);
        return isValid;
    }

    validateForm(form) {
        const fields = form.querySelectorAll('[data-validate]');
        let isFormValid = true;

        fields.forEach(field => {
            if (!this.validateField(field)) {
                isFormValid = false;
            }
        });

        return isFormValid;
    }

    showFieldValidation(field, isValid, message) {
        field.classList.remove('is-valid', 'is-invalid');
        
        let feedback = field.parentNode.querySelector('.invalid-feedback');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            field.parentNode.appendChild(feedback);
        }

        if (isValid) {
            field.classList.add('is-valid');
            feedback.textContent = '';
            feedback.style.display = 'none';
        } else {
            field.classList.add('is-invalid');
            feedback.textContent = message;
            feedback.style.display = 'block';
        }
    }

    static validateNigerianPhone(phone) {
        const phoneRegex = /^(\+234|234|0)?[789][01]\d{8}$/;
        return phoneRegex.test(phone.replace(/\s+/g, ''));
    }

    static formatNigerianPhone(phone) {
        let cleaned = phone.replace(/\D/g, '');
        
        if (cleaned.startsWith('234')) {
            cleaned = '+' + cleaned;
        } else if (cleaned.startsWith('0')) {
            cleaned = '+234' + cleaned.substring(1);
        } else if (cleaned.length === 10) {
            cleaned = '+234' + cleaned;
        }
        
        return cleaned;
    }

    static validateAmount(amount, min = 100) {
        const numAmount = parseFloat(amount);
        return !isNaN(numAmount) && numAmount >= min;
    }

    static formatCurrency(amount) {
        return new Intl.NumberFormat('en-NG', {
            style: 'currency',
            currency: 'NGN'
        }).format(amount);
    }
}

class PaymentValidator extends EForumValidator {
    constructor() {
        super();
        this.setupPaymentRules();
    }

    setupPaymentRules() {
        this.addRule('card_number', (value) => {
            const cardRegex = /^\d{13,19}$/;
            return cardRegex.test(value.replace(/\s+/g, ''));
        }, 'Please enter a valid card number');

        this.addRule('cvv', (value) => {
            const cvvRegex = /^\d{3,4}$/;
            return cvvRegex.test(value);
        }, 'CVV must be 3 or 4 digits');

        this.addRule('expiry', (value) => {
            const expiryRegex = /^(0[1-9]|1[0-2])\/\d{2}$/;
            if (!expiryRegex.test(value)) return false;
            
            const [month, year] = value.split('/');
            const expiry = new Date(2000 + parseInt(year), parseInt(month) - 1);
            const now = new Date();
            
            return expiry > now;
        }, 'Please enter a valid expiry date (MM/YY)');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    window.eforumValidator = new EForumValidator();
    
    if (document.querySelector('.payment-form')) {
        window.paymentValidator = new PaymentValidator();
    }
});

if (typeof module !== 'undefined' && module.exports) {
    module.exports = { EForumValidator, PaymentValidator };
}
