<?php

use function Pest\Laravel\get;

/**
 * The value of a CSS custom property declared in a stylesheet under resources/css.
 */
function cssToken(string $stylesheet, string $token): ?string
{
    preg_match('/'.preg_quote($token, '/').':\s*([^;]+);/', file_get_contents(resource_path("css/{$stylesheet}")), $match);

    return isset($match[1]) ? strtolower(trim($match[1])) : null;
}

it('uses the same palette as the dashboards', function (string $token) {
    $homepageValue = cssToken('homepage.css', $token);

    expect($homepageValue)->not->toBeNull();

    foreach (['student', 'teacher', 'admin'] as $role) {
        expect(cssToken("dashboard/{$role}_dashboard.css", $token))->toBe($homepageValue);
    }
})->with(['--blue', '--blue-dark', '--blue-mid', '--blue-light', '--green', '--orange', '--purple', '--bg', '--border', '--text', '--text-3']);

it('uses the sign-in page navy for the hero, cta band, and footer', function () {
    $css = file_get_contents(resource_path('css/homepage.css'));

    expect(cssToken('homepage.css', '--navy-1'))->toBe(cssToken('app.css', '--auth-bg-start'))
        ->and(cssToken('homepage.css', '--navy-2'))->toBe(cssToken('app.css', '--auth-bg-mid'))
        ->and(cssToken('homepage.css', '--navy-3'))->toBe(cssToken('app.css', '--auth-bg-end'))
        ->and(cssToken('homepage.css', '--navy-4'))->toBe(cssToken('app.css', '--auth-panel-top'))
        ->and(cssToken('homepage.css', '--navy-5'))->toBe(cssToken('app.css', '--auth-panel-bottom'));

    preg_match('/\.hero\s*\{[^}]*\}/', $css, $hero);
    preg_match('/\.cta__panel\s*\{[^}]*\}/', $css, $cta);
    preg_match('/\.footer\s*\{[^}]*\}/', $css, $footer);

    expect($hero[0] ?? '')->toContain('background: var(--navy-grad);')
        ->and($cta[0] ?? '')->toContain('background: var(--navy-grad);')
        ->and($footer[0] ?? '')->toContain('background: var(--navy-5);');
});

it('uses the dashboards font and the ai chat blue for primary buttons', function () {
    $css = file_get_contents(resource_path('css/homepage.css'));

    preg_match('/\.btn--primary\s*\{[^}]*\}/', $css, $primary);

    expect(cssToken('homepage.css', '--font'))->toStartWith("'plus jakarta sans'")
        ->and(cssToken('homepage.css', '--blue-grad'))->toBe('linear-gradient(135deg, var(--blue-mid), var(--blue))')
        ->and($primary[0] ?? '')->toContain('background: var(--blue-grad);');
});

it('leaves no blue-to-green brand gradient on the landing page', function () {
    $html = get('/')->getContent();

    preg_match('/<linearGradient id="brand-grad".*?<\/linearGradient>/s', $html, $brandGradient);

    expect($brandGradient[0] ?? '')
        ->toContain('stop-color="#60a5fa"')
        ->toContain('stop-color="#2563eb"');

    expect(file_get_contents(resource_path('css/homepage.css')))
        ->not->toMatch('/#(015b8a|0f6f36|06767a|16906e|197a86)\b/i')
        ->not->toContain('--accent-');
});

it('previews the student dashboard in the hero', function () {
    $html = get('/')->getContent();

    preg_match('/<section class="hero".*?<\/section>/s', $html, $hero);

    expect($hero[0] ?? '')
        ->toContain('Overall progress')
        ->toContain('Topics done')
        ->toContain('Learning Modules')
        ->toContain('Sequences and Series')
        ->toContain('Polynomials and Polynomial Equations')
        ->toContain('Advanced Equations and Functions')
        ->toContain('Math AI Assistant');
});

it('shows each module in its dashboard color with all twelve topics', function () {
    $html = get('/')->getContent();

    expect($html)
        ->toContain('class="module module--blue lift reveal"')
        ->toContain('class="module module--green lift reveal"')
        ->toContain('class="module module--orange lift reveal"');

    preg_match_all('/<ul class="module__topics"[^>]*>(.*?)<\/ul>/s', $html, $topicLists);

    expect($topicLists[1])->toHaveCount(3)
        ->and(substr_count(implode('', $topicLists[1]), '<li>'))->toBe(12);
});

it('shows the school seal in the call to action like the sign-in page', function () {
    $html = get('/')->getContent();

    preg_match('/<div class="cta__brand">.*?<\/div>\s*<\/div>/s', $html, $brand);

    expect($brand[0] ?? '')
        ->toContain('alt="Bubog National High School seal"')
        ->toContain('loading="lazy"')
        ->toContain('Bubog National High School');
});
