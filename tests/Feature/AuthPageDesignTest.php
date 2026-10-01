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
        ->toContain('--auth-signin: #2563eb;')
        ->toContain('--auth-signup: var(--auth-signin);')
        ->toContain('background: linear-gradient(135deg, var(--auth-bg-start) 0%, var(--auth-bg-mid) 50%, var(--auth-bg-end) 100%);');

    expect($panel[0] ?? '')
        ->toContain('background: linear-gradient(160deg, var(--auth-panel-top) 0%, var(--auth-panel-bottom) 100%);');

    expect($css)->not->toMatch('/#(16906e|197a86|0f5f52|0f7355|0b5c44)\b/i');
});

it('keeps the auth backdrop and panel dark, muted blues that are easy on the eyes', function (string $token) {
    preg_match('/'.preg_quote($token, '/').':\s*#([0-9a-f]{6});/i', file_get_contents(resource_path('css/app.css')), $match);
    expect($match)->not->toBeEmpty();

    $toLinear = function (string $channel): float {
        $value = hexdec($channel) / 255;

        return $value <= 0.03928 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
    };
    [$red, $green, $blue] = array_map($toLinear, str_split($match[1], 2));

    expect(0.2126 * $red + 0.7152 * $green + 0.0722 * $blue)->toBeLessThan(0.1)
        ->and($blue)->toBeGreaterThan($green)
        ->and($green)->toBeGreaterThan($red);
})->with(['--auth-bg-start', '--auth-bg-mid', '--auth-bg-end', '--auth-panel-top', '--auth-panel-bottom']);

it('puts the google sign-up completion page on the shared blue tokens', function () {
    expect(file_get_contents(resource_path('views/login/google-signup-completion.blade.php')))
        ->toContain('<button type="submit" class="auth-btn ')
        ->toContain('border-l-4 border-primary bg-primary-tint')
        ->not->toMatch('/#(1b5384|164468|eaf1f7|0f7355|0b5c44)\b/i');
});

it('gives every auth form the landing page gradient button', function (string $routeName) {
    preg_match('/<button[^>]*type="submit"[^>]*>/', $this->get(route($routeName))->assertOk()->getContent(), $submit);

    expect($submit[0] ?? '')->toContain('auth-btn');
})->with(['student.login', 'teacher.login', 'admin.login', 'student.register.form', 'teacher.register.form', 'student.password.request']);

it('styles the auth button exactly like the landing page primary button', function () {
    preg_match('/\.auth-btn\s*\{[^}]*\}/', file_get_contents(resource_path('css/app.css')), $authButton);

    // homepage.css: .btn--primary uses --blue-grad = linear-gradient(135deg, var(--blue-mid), var(--blue)).
    expect(cssToken('homepage.css', '--blue-mid'))->toBe('#60a5fa')
        ->and(cssToken('homepage.css', '--blue'))->toBe('#2563eb')
        ->and($authButton[0] ?? '')
        ->toContain('background: linear-gradient(135deg, #60a5fa, #2563eb);')
        ->toContain('box-shadow: 0 6px 16px rgb(37 99 235 / 0.3);');

    expect(file_get_contents(resource_path('views/components/button.blade.php')))
        ->toContain("'auth' => 'auth-btn'")
        ->not->toContain("'signin' =>")
        ->not->toContain("'signup' =>");
});
