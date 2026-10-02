{{--
    The Change Password card on every dashboard's settings page. An account
    made through Google sign-in starts with a random password its owner never
    saw, so it gets a "Set a Password" form without Current Password until it
    has one of its own (password-form.js then switches the card back).
--}}
<div class="settings-section" data-password-form="{{ auth()->user()->hasOwnPassword() ? 'change' : 'set' }}">
    <div data-password-mode="change">
        <h3>Change Password</h3>
        <p class="desc">{{ $description ?? 'Keep your account secure' }}</p>
    </div>
    <div data-password-mode="set">
        <h3>Set a Password</h3>
        <p class="desc">Your account signs in with Google. Set a password if you also want to sign in with your email.</p>
    </div>
    {{-- Lets the browser's password manager match this account, so it can fill the saved current password and update it afterwards. --}}
    <input type="email" autocomplete="username" value="{{ auth()->user()->email }}" hidden>
    @foreach (['pw-current' => ['Current Password', 'current-password'], 'pw-new' => ['New Password', 'new-password'], 'pw-confirm' => ['Confirm New Password', 'new-password']] as $fieldId => [$fieldLabel, $autocomplete])
    <div class="field-row" @if ($fieldId === 'pw-current') data-password-mode="change" @endif>
        <label for="{{ $fieldId }}">{{ $fieldLabel }}</label>
        <div class="pw-field">
            <input type="password" id="{{ $fieldId }}" placeholder="••••••••" autocomplete="{{ $autocomplete }}">
            <button type="button" class="pw-toggle" data-action="toggle-password" aria-label="Show password" aria-pressed="false">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
        </div>
    </div>
    @endforeach
    <div class="save-row">
        <button class="btn-cancel" id="cancel-password-btn">Cancel</button>
        <button class="btn-save" id="save-password-btn"><span data-password-mode="change">Update Password</span><span data-password-mode="set">Set Password</span></button>
    </div>
</div>
