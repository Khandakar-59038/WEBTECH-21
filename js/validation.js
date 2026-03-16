// js/validation.js --- Client-side form validation for interest registration
// CTEC2712N --- Ushno

document.addEventListener('DOMContentLoaded', function () {

    const form       = document.getElementById('interest-form');
    if (!form) return; // Only run on pages that have the form

    const nameInput  = document.getElementById('student-name');
    const emailInput = document.getElementById('student-email');
    const nameError  = document.getElementById('name-error');
    const emailError = document.getElementById('email-error');
    // ── VALIDATE NAME when user clicks away from the field (blur) ──
    nameInput.addEventListener('blur', function () {
        validateName();
    });

    // ── VALIDATE EMAIL when user clicks away from the field ──
    emailInput.addEventListener('blur', function () {
        validateEmail();
    });
    // ── VALIDATE BOTH on form submit ──
    form.addEventListener('submit', function (e) {
        const nameOk  = validateName();
        const emailOk = validateEmail();

        if (!nameOk || !emailOk) {
            e.preventDefault(); // Stop form submitting

            // Move keyboard focus to first error field
            if (!nameOk) {
                nameInput.focus();
            } else {
                emailInput.focus();
            }
        }
    });
    // ── HELPER: validate name field ──
    function validateName() {
        const val = nameInput.value.trim();
        if (val === '') {
            showError(nameInput, nameError, 'Please enter your full name.');
            return false;
        } else if (val.length > 100) {
            showError(nameInput, nameError, 'Name must be 100 characters or fewer.');
            return false;
        } else {
            clearError(nameInput, nameError);
            return true;
        }
    }

    // ── HELPER: validate email field ──
    function validateEmail() {
        const val     = emailInput.value.trim();
        const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (val === '') {
            showError(emailInput, emailError, 'Please enter your email address.');
            return false;
        } else if (!pattern.test(val)) {
            showError(emailInput, emailError, 'Please enter a valid email address (e.g. name@example.com).');
            return false;
        } else {
            clearError(emailInput, emailError);
            return true;
        }
    }