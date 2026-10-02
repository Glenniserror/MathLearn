/* =====================================================================
   password-toggle.js
   Show/hide button for password fields on the dashboards' Change
   Password forms. Same data-action, icons, and labels as the sign-in
   page's <x-input type="password"> toggle, so it behaves the same way.

   Markup: <div class="pw-field"><input type="password" …><button
   type="button" class="pw-toggle" data-action="toggle-password" …></div>
   ===================================================================== */

const EYE = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
const EYE_OFF = '<path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a18.5 18.5 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';

/** Show or hide one field's password and keep its button's label and icon in sync. */
function setPasswordVisible(button, visible) {
    const input = button.previousElementSibling;
    if (!input) return;

    input.type = visible ? 'text' : 'password';
    button.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
    button.setAttribute('aria-pressed', visible ? 'true' : 'false');

    const svg = button.querySelector('svg');
    if (svg) svg.innerHTML = visible ? EYE_OFF : EYE;
}

/** Hide every password in a form again, e.g. after Cancel or a successful update. */
export function hidePasswords(root = document) {
    root.querySelectorAll('[data-action="toggle-password"]').forEach((button) => setPasswordVisible(button, false));
}

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-action="toggle-password"]');
    if (!button) return;

    setPasswordVisible(button, button.previousElementSibling?.type === 'password');
});
