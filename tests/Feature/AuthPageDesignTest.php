<?php

dataset('auth pages', [
    'student sign in' => 'student.login',
    'teacher sign in' => 'teacher.login',
    'admin sign in' => 'admin.login',
    'student sign up' => 'student.register.form',
    'teacher sign up' => 'teacher.register.form',
]);

it('renders the auth pages on the blue backdrop with a readable back link', function (string $routeName) {
    $this->get(route($routeName))
        ->assertOk()
        ->assertSee('<body class="auth-gradient-bg', false)
        ->assertSee('auth-brand-panel', false)
        ->assertSee('font-semibold text-white/85', false)
        ->assertDontSee('font-semibold text-white/70', false);
})->with('auth pages');

it('uses only blue in the sign-in and sign-up palette', function () {
    $css = file_get_contents(resource_path('css/app.css'));
    preg_match('/\.auth-gradient-bg\s*\{[^}]*\}/', $css, $backdrop);
    preg_match('/\.auth-brand-panel\s*\{[^}]*\}/', $css, $panel);

    expect($backdrop[0] ?? '')
        ->toContain('--auth-bg-start: #3b82f6;')
        ->toContain('--auth-bg-mid: #2563eb;')
        ->toContain('--auth-bg-end: #1d4ed8;')
        ->toContain('--auth-signin: #2563eb;')
        ->toContain('--auth-signup: var(--auth-signin);')
        ->toContain('background: linear-gradient(135deg, var(--auth-bg-start) 0%, var(--auth-bg-mid) 50%, var(--auth-bg-end) 100%);');

    expect($panel[0] ?? '')
        ->toContain('background: linear-gradient(160deg, var(--auth-panel-top) 0%, var(--auth-panel-bottom) 100%);');

    expect($css)->not->toMatch('/#(16906e|197a86|1e4e7f|0f5f52|0f7355|0b5c44)\b/i');
});

it('puts the google sign-up completion page on the shared blue tokens', function () {
    expect(file_get_contents(resource_path('views/login/google-signup-completion.blade.php')))
        ->toContain('rounded-md bg-primary text-[15px] font-bold text-white transition-colors duration-150 hover:bg-primary-hover')
        ->toContain('border-l-4 border-primary bg-primary-tint')
        ->not->toMatch('/#(1b5384|164468|eaf1f7|0f7355|0b5c44)\b/i');
});
