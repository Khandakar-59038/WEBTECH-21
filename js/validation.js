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