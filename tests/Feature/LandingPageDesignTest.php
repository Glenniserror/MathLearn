<?php

use function Pest\Laravel\get;

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

it('introduces each role dashboard with its own sign-in link', function () {
    $html = get('/')->getContent();

    preg_match('/<section[^>]+id="dashboards".*?<\/section>/s', $html, $section);
    $dashboards = $section[0] ?? '';

    expect($dashboards)
        ->toContain('class="role role--blue lift reveal"')
        ->toContain('class="role role--green lift reveal"')
        ->toContain('class="role role--purple lift reveal"')
        ->toContain('href="'.route('student.login').'"')
        ->toContain('href="'.route('teacher.login').'"')
        ->toContain('href="'.route('admin.login').'"');

    expect(substr_count($dashboards, '<li>'))->toBe(12);
});

it('answers common questions in an accordion that matches how sign-up works', function () {
    $html = get('/')->getContent();

    preg_match('/<section[^>]+id="faq".*?<\/section>/s', $html, $section);
    $faq = $section[0] ?? '';

    expect(substr_count($faq, '<details class="faq__item">'))->toBe(6)
        ->and(substr_count($faq, '<summary>'))->toBe(6)
        ->and($faq)
        ->toContain('The teacher of the section you picked approves student accounts, and an administrator approves teacher accounts.')
        ->toContain('Continue with Google')
        ->toContain('href="#privacy"');
});

it('highlights the nav link for the section in view', function () {
    $html = get('/')->getContent();

    preg_match('/<ul class="nav__links">.*?<\/ul>/s', $html, $links);

    expect(substr_count($links[0] ?? '', 'data-spy'))->toBe(4)
        ->and($links[0] ?? '')->toContain('href="#dashboards"')->toContain('href="#faq"');

    expect(file_get_contents(resource_path('js/homepage.js')))
        ->toContain("document.querySelectorAll('[data-spy]')")
        ->toContain("link.setAttribute('aria-current', 'location')");

    expect(file_get_contents(resource_path('css/homepage.css')))
        ->toContain('.nav.is-scrolled .nav__links a.is-active');
});

it('floats the hero cards on wrappers so the entrance and float never share an element', function () {
    $html = get('/')->getContent();

    expect($html)
        ->toMatch('/<div class="hero__float hero__float--chat" data-enter="6"[^>]*>\s*<div class="chat-card">/')
        ->toMatch('/<div class="hero__float hero__float--chip" data-enter="7"[^>]*>\s*<div class="chip-card">/');

    expect(file_get_contents(resource_path('css/homepage.css')))
        ->toMatch('/@keyframes float\s*\{[^@]*translateY\(-8px\)/');
});

it('keeps the stats cards below the hero instead of overlapping it', function () {
    preg_match('/\.stats\s*\{[^}]*\}/', file_get_contents(resource_path('css/homepage.css')), $stats);

    expect($stats[0] ?? '')
        ->toContain('padding-top:')
        ->not->toContain('margin-top: calc(-1');
});

it('keeps the hero column from growing wider than a phone screen', function () {
    preg_match('/\.hero__inner\s*\{[^}]*\}/', file_get_contents(resource_path('css/homepage.css')), $inner);

    expect($inner[0] ?? '')->toContain('grid-template-columns: minmax(0, 1fr);');
});

it('shows the school seal in the call to action like the sign-in page', function () {
    $html = get('/')->getContent();

    preg_match('/<div class="cta__brand">.*?<\/div>\s*<\/div>/s', $html, $brand);

    expect($brand[0] ?? '')
        ->toContain('alt="Bubog National High School seal"')
        ->toContain('loading="lazy"')
        ->toContain('Bubog National High School');
});
