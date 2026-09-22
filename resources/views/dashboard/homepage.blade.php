<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">document.documentElement.classList.add('js')</script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Math Learning is an AI-powered math learning platform for junior high school students, featuring interactive modules, quizzes, and progress tracking.">
    <meta name="theme-color" content="#f8fafc">

    <title>Math Learning Assistant</title>

    <link rel="icon" type="image/png" href="{{ asset('image/587572187-777024998723535-6772324307557000990-n-fotor-20260519155328.png') }}">

    <!-- ================= SELF-HOSTED FONT (Inter, same as the app) ================= -->
    <link rel="preload" href="/fonts/inter-latin-400-800.woff2" as="font" type="font/woff2" crossorigin>

    <!-- ================= CSS / JS =================
         The hero no longer uses a photo, so the old image preload is gone.
         homepage.css stays inlined to keep it off the critical request path. -->
    {{-- Falls back to a linked tag while the Vite dev server is running. --}}
    @if (app(\Illuminate\Foundation\Vite::class)->isRunningHot())
        @vite('resources/css/homepage.css')
    @else
        <style nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">{!! \Illuminate\Support\Facades\Vite::content('resources/css/homepage.css') !!}</style>
    @endif

    @vite([
        'resources/js/homepage.js',
        'resources/js/nav-progress.js'
    ])

</head>

<body>

@php
    // Pass $stats from the controller to show real numbers. Until then these
    // defaults only state things that are true of the platform itself.
    // 'students' stays null (shows "24/7 AI tutor help" instead) until you
    // have a real student count to show.
    $stats = array_merge(['modules' => 3, 'topics' => 12, 'students' => null], $stats ?? []);

    // Social links: set real URLs here (or pass $socials from the controller).
    // Entries without a URL are not rendered.
    $socials = $socials ?? [
        ['label' => 'Facebook', 'icon' => 'i-facebook', 'url' => null],
        ['label' => 'YouTube',  'icon' => 'i-youtube',  'url' => null],
    ];
    // Teacher-dashboard preview. Pass $overview from the controller with
    // AGGREGATE numbers only (no student names or personal progress, this
    // page is public):
    //   $overview = [
    //       'students'         => 15,
    //       'avg_progress'     => 1,     // percent, 0-100
    //       'pending_feedback' => 0,
    //       'modules'          => [['name' => 'Sequences and Series', 'avg' => 20], ...],
    //   ];
    // Without $overview the preview falls back to clearly labelled sample data.
    $isLive = isset($overview);
    $overview = $overview ?? [
        'students' => 38,
        'avg_progress' => 82,
        'pending_feedback' => 4,
        'modules' => [
            ['name' => 'Sequences and Series', 'avg' => 88],
            ['name' => 'Polynomials and Polynomial Equations', 'avg' => 74],
            ['name' => 'Advanced Equations and Functions', 'avg' => 61],
        ],
    ];
@endphp

<!-- ================= ICON SPRITE ================= -->
<svg class="sprite" aria-hidden="true" focusable="false">
    <defs>
        <linearGradient id="brand-grad" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#015b8a"/>
            <stop offset="1" stop-color="#0f6f36"/>
        </linearGradient>
    </defs>
    <symbol id="logo-mark" viewBox="0 0 32 32">
        <rect width="32" height="32" rx="8" fill="url(#brand-grad)"/>
        <rect x="10" y="7.5" width="12" height="17" rx="2.5" fill="none" stroke="#fff" stroke-width="2"/>
        <path d="M14.5 21h3" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
    </symbol>
    <symbol id="i-menu" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></symbol>
    <symbol id="i-close" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18"/></symbol>
    <symbol id="i-arrow" viewBox="0 0 24 24"><path d="M5 12h14m0 0-6-6m6 6-6 6"/></symbol>
    <symbol id="i-check" viewBox="0 0 24 24"><path d="m5 12.5 4.5 4.5L19 7.5"/></symbol>
    <symbol id="i-chevrons" viewBox="0 0 24 24"><path d="M7 6l5 5 5-5"/><path d="M7 13l5 5 5-5"/></symbol>
    <symbol id="i-book" viewBox="0 0 24 24"><path d="M2 5.5C4.5 4 8 4 12 6.5c4-2.5 7.5-2.5 10-1v13c-2.5-1.5-6-1.5-10 1-4-2.5-7.5-2.5-10-1z"/><path d="M12 6.5v13"/></symbol>
    <symbol id="i-chat" viewBox="0 0 24 24"><path d="M21 12a8 8 0 0 1-11.6 7.1L4 20.5l1.4-4.6A8 8 0 1 1 21 12z"/></symbol>
    <symbol id="i-quiz" viewBox="0 0 24 24"><path d="M10 6h10M10 12h10M10 18h10"/><path d="m3.5 6 1.5 1.5L7.5 5M3.5 12 5 13.5 7.5 11M3.5 18 5 19.5 7.5 17"/></symbol>
    <symbol id="i-chart" viewBox="0 0 24 24"><path d="M4 20V4M4 20h16"/><path d="M8 16v-4M12 16V8M16 16v-6"/></symbol>
    <symbol id="i-users" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.2"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><path d="M16 5.2a3.2 3.2 0 0 1 0 5.6M18 14.3c2 .8 3.5 2.8 3.5 5.7"/></symbol>
    <symbol id="i-user-plus" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6"/><path d="M19 8v6M16 11h6"/></symbol>
    <symbol id="i-chevron" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></symbol>
    <symbol id="i-download" viewBox="0 0 24 24"><path d="M12 4v11m0 0-4-4m4 4 4-4"/><path d="M5 19h14"/></symbol>
    <symbol id="i-facebook" viewBox="0 0 24 24"><path d="M14.5 21v-8h2.7l.5-3.2h-3.2V7.9c0-.9.4-1.6 1.7-1.6h1.6V3.4c-.3 0-1.3-.2-2.3-.2-2.4 0-4 1.5-4 4.1v2.5H8.8V13h2.7v8z"/></symbol>
    <symbol id="i-youtube" viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="12" rx="3.5"/><path d="m10.5 9.5 4 2.5-4 2.5z"/></symbol>
</svg>

<a class="skip" href="#main">Skip to content</a>

<!-- ================= NAV ================= -->
<header class="nav" data-nav>
    <div class="container nav__inner">

        <a href="#top" class="logo" aria-label="Math Learning, back to top">
            <svg class="logo__mark" viewBox="0 0 32 32" aria-hidden="true"><use href="#logo-mark"/></svg>
            <span>Math Learning</span>
        </a>

        <button class="nav__toggle" type="button" data-nav-toggle
                aria-expanded="false" aria-controls="nav-panel" aria-label="Open menu">
            <svg class="icon i-open" aria-hidden="true"><use href="#i-menu"/></svg>
            <svg class="icon i-close" aria-hidden="true"><use href="#i-close"/></svg>
        </button>

        <div class="nav__panel" id="nav-panel" data-nav-panel>
            <nav aria-label="Primary">
                <ul class="nav__links">
                    <li><a href="#features">Features</a></li>
                    <li><a href="#how-it-works">How it works</a></li>
                    <li><a href="#modules">Modules</a></li>
                    <li><a href="#teachers">For teachers</a></li>
                </ul>
            </nav>

            <div class="nav__actions">
                <a href="{{ route('signin-signin') }}" class="btn btn--ghost btn--sm">Sign In</a>
                <a href="{{ route('signin-signup') }}" class="btn btn--primary btn--sm">
                    <svg class="icon" aria-hidden="true"><use href="#i-user-plus"/></svg>
                    Sign Up
                </a>
            </div>
        </div>

    </div>
</header>

<main id="main">

<!-- ================= HERO ================= -->
<section class="hero" id="top" aria-labelledby="hero-title">

    <div class="hero__blob" aria-hidden="true"></div>

    <div class="container hero__inner">

        <div class="hero__copy">

            <h1 class="hero__title" id="hero-title">
                <span data-enter="1">Every problem</span>
                <span data-enter="2">has a next step.</span>
            </h1>

            <p class="hero__sub">
                {{ $platformDescription ?? 'Interactive lessons, quizzes with instant feedback, and an AI tutor that explains each step. Made for Bubog National High School students and the teachers who guide them.' }}
            </p>

            <div class="hero__cta" data-enter="3">
                <a href="{{ route('signin-signup') }}" class="btn btn--primary">
                    Create your account
                    <svg class="icon icon--go" aria-hidden="true"><use href="#i-arrow"/></svg>
                </a>
                <a href="{{ route('signin-signin') }}" class="btn btn--ghost">Sign in</a>
            </div>

            <p class="hero__note" data-enter="4">
                <svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>
                Sign in as a student, teacher, or admin, with your school email or Google.
            </p>

        </div>

        <!-- Hero visual: a lesson graph that draws itself while the AI tutor explains -->
        <div class="hero__visual" data-parallax>

            <div class="solver" data-enter="5" role="img"
                 aria-label="Preview of a Math Learning lesson: a parabola is graphed while the AI tutor explains how to factor x squared minus 4x plus 3.">

                <div class="solver__bar" aria-hidden="true">
                    <span class="solver__title">Quadratic functions</span>
                    <span class="tag">Algebra</span>
                </div>

                <svg class="plane" viewBox="0 0 360 260" aria-hidden="true" focusable="false">
                    <defs>
                        <pattern id="plane-grid" width="25" height="25" patternUnits="userSpaceOnUse" x="5" y="15">
                            <path class="plane__grid" d="M25 0H0V25"/>
                        </pattern>
                    </defs>
                    <rect width="360" height="260" fill="url(#plane-grid)"/>

                    <line class="plane__axis" x1="16" y1="190" x2="344" y2="190"/>
                    <line class="plane__axis" x1="80" y1="16" x2="80" y2="244"/>
                    <text class="plane__txt" x="334" y="182">x</text>
                    <text class="plane__txt" x="90" y="28">y</text>

                    <line class="plane__sym" x1="180" y1="34" x2="180" y2="215"/>

                    <!-- y = x^2 - 4x + 3 (exact parabola as a quadratic Bezier) -->
                    <path class="plane__curve" pathLength="1" d="M55 59Q180 371 305 59"/>

                    <circle class="plane__dot plane__dot--r1" cx="130" cy="190" r="5.5"/>
                    <circle class="plane__dot plane__dot--r2" cx="230" cy="190" r="5.5"/>
                    <circle class="plane__dot plane__dot--vertex" cx="180" cy="215" r="5.5"/>
                    <text class="plane__lbl plane__lbl--r1" x="118" y="209" text-anchor="end">(1, 0)</text>
                    <text class="plane__lbl plane__lbl--r2" x="242" y="209">(3, 0)</text>
                    <text class="plane__lbl plane__lbl--vertex" x="180" y="240" text-anchor="middle">(2, −1)</text>
                </svg>

                <div class="solver__chat" aria-hidden="true">
                    <p class="bubble bubble--user">Paano hanapin ang roots ng x² − 4x + 3?</p>
                    <p class="bubble bubble--ai">Kaya natin 'to! I-factor muna: <b>(x − 1)(x − 3) = 0</b>, kaya x = 1 o x = 3.</p>
                </div>

            </div>

            <div class="chip-card chip-card--quiz" data-enter="6" aria-hidden="true">
                <span class="chip-card__icon"><svg class="icon" aria-hidden="true"><use href="#i-quiz"/></svg></span>
                <span><strong>Summative Test: 9 of 10</strong><span class="sub">Instant feedback</span></span>
            </div>

            <div class="chip-card chip-card--offline" data-enter="7" aria-hidden="true">
                <span class="chip-card__icon"><svg class="icon" aria-hidden="true"><use href="#i-download"/></svg></span>
                <span><strong>Offline Materials</strong><span class="sub">Study without data</span></span>
            </div>

        </div>

    </div>

    <a href="#features" class="scroll-cue" aria-label="Scroll to features">
        <svg class="icon" aria-hidden="true"><use href="#i-chevrons"/></svg>
    </a>

</section>

<!-- ================= TRUST STRIP ================= -->
<section class="stats" aria-label="Math Learning at a glance">
    <div class="container stats__grid">

        <div class="stat">
            <p class="stat__num" data-count="{{ $stats['modules'] }}">{{ $stats['modules'] }}</p>
            <p class="stat__label">learning modules</p>
        </div>

        <div class="stat">
            <p class="stat__num" data-count="{{ $stats['topics'] }}">{{ $stats['topics'] }}</p>
            <p class="stat__label">topics to master</p>
        </div>

        <div class="stat">
            @if (! empty($stats['students']))
                <p class="stat__num" data-count="{{ $stats['students'] }}">{{ number_format($stats['students']) }}</p>
                <p class="stat__label">students learning</p>
            @else
                <p class="stat__num">24/7</p>
                <p class="stat__label">AI tutor help</p>
            @endif
        </div>

    </div>
</section>

<!-- ================= FEATURES (bento) ================= -->
<section class="section" id="features" aria-labelledby="features-title">
    <div class="container">

        <div class="section__head reveal">
            <h2 id="features-title">Everything for learning math, in one place</h2>
            <p>From the first lesson to the last quiz, Math Learning keeps students practicing and teachers informed.</p>
        </div>

        <div class="bento">

            <!-- Big tile: AI chatbot -->
            <article class="tile tile--big lift reveal">
                <div>
                    <span class="tile__icon"><svg class="icon" aria-hidden="true"><use href="#i-chat"/></svg></span>
                    <h3>An AI tutor that shows its work</h3>
                    <p>Stuck on a problem at home? Ask the chatbot and get the steps, not only the answer, any time of day.</p>
                </div>
                <div class="tile__visual mini-chat" aria-hidden="true">
                    <p class="bubble bubble--user">Give me a hint for 3, 7, 11, …</p>
                    <p class="bubble bubble--ai">Look at the gap between each term. What do you notice?</p>
                    <ul class="chips">
                        <li class="chip">Explain factoring</li>
                        <li class="chip">Check my answer</li>
                        <li class="chip">Show another example</li>
                    </ul>
                </div>
            </article>

            <!-- Big tile: assessments -->
            <article class="tile tile--big lift reveal">
                <div>
                    <span class="tile__icon tile__icon--purple"><svg class="icon" aria-hidden="true"><use href="#i-quiz"/></svg></span>
                    <h3>Quizzes that tell you why</h3>
                    <p>Take a summative test and see your result right away, so you know exactly what to review next.</p>
                </div>
                <div class="tile__visual mini-quiz" aria-hidden="true">
                    <p class="mini-quiz__q">Which number comes next? 3, 7, 11, …</p>
                    <div class="opt"><span>13</span></div>
                    <div class="opt"><span>14</span></div>
                    <div class="opt opt--right">
                        <span>15</span>
                        <svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>
                    </div>
                    <p class="mini-quiz__fb"><b>Tama!</b> The pattern adds 4 each time.</p>
                </div>
            </article>

            <article class="tile lift reveal">
                <div>
                    <span class="tile__icon tile__icon--orange"><svg class="icon" aria-hidden="true"><use href="#i-book"/></svg></span>
                    <h3>Interactive modules</h3>
                    <p>Three modules of Junior High School math, split into short topics that unlock as you go.</p>
                </div>
            </article>

            <article class="tile lift reveal">
                <div>
                    <span class="tile__icon tile__icon--green"><svg class="icon" aria-hidden="true"><use href="#i-chart"/></svg></span>
                    <h3>Progress tracking</h3>
                    <p>Follow your overall progress, topics finished, and daily streak.</p>
                </div>
                <div class="tile__visual" aria-hidden="true">
                    <svg class="spark" viewBox="0 0 120 48" focusable="false">
                        <rect x="0" y="34" width="16" height="14" rx="4"/>
                        <rect x="26" y="26" width="16" height="22" rx="4"/>
                        <rect x="52" y="30" width="16" height="18" rx="4"/>
                        <rect x="78" y="16" width="16" height="32" rx="4"/>
                        <rect x="104" y="4" width="16" height="44" rx="4"/>
                    </svg>
                </div>
            </article>

            <article class="tile lift reveal">
                <div>
                    <span class="tile__icon"><svg class="icon" aria-hidden="true"><use href="#i-users"/></svg></span>
                    <h3>Teacher dashboard</h3>
                    <p>Follow student progress, send feedback, and generate reports and quizzes.</p>
                </div>
                <div class="tile__visual avatars" aria-hidden="true">
                    <span class="avatar">MJ</span><span class="avatar">AR</span><span class="avatar">KB</span><span class="avatar">JD</span>
                </div>
            </article>

            <article class="tile lift reveal">
                <div>
                    <span class="tile__icon tile__icon--green"><svg class="icon" aria-hidden="true"><use href="#i-download"/></svg></span>
                    <h3>Offline access</h3>
                    <p>Download assessments and study offline when data or Wi-Fi runs out.</p>
                </div>
                <div class="tile__visual" aria-hidden="true">
                    <span class="saved"><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Saved to this device</span>
                </div>
            </article>

        </div>
    </div>
</section>

<!-- ================= HOW IT WORKS ================= -->
<section class="section section--white" id="how-it-works" aria-labelledby="how-title">
    <div class="container">

        <div class="section__head reveal">
            <h2 id="how-title">Get started in three steps</h2>
            <p>No setup, no long tutorials. Create an account and open your first lesson.</p>
        </div>

        <div class="steps-panel reveal">
            <ol class="steps">
                <li class="step">
                    <span class="step__num" aria-hidden="true">1</span>
                    <h3>Create your account</h3>
                    <p>Sign up as a student or a teacher, then sign in from any device.</p>
                </li>
                <li class="step">
                    <span class="step__num" aria-hidden="true">2</span>
                    <h3>Learn and practice</h3>
                    <p>Open a module, work through the lessons, and ask the AI tutor when you get stuck.</p>
                </li>
                <li class="step">
                    <span class="step__num" aria-hidden="true">3</span>
                    <h3>Check your progress</h3>
                    <p>Take quizzes, get instant feedback, and watch your scores grow. Teachers see it too.</p>
                </li>
            </ol>
        </div>

    </div>
</section>

<!-- ================= MODULES =================
     The three modules in the app. Topics inside each module unlock in order. -->
<section class="section" id="modules" aria-labelledby="modules-title">
    <div class="container">

        <div class="section__head reveal">
            <h2 id="modules-title">Three modules, one step at a time</h2>
            <p>Each module is split into topics that unlock as you finish the one before it.</p>
        </div>

        <div class="modules">

            <a href="{{ route('signin-signin') }}" class="module lift reveal">
                <div class="module__art">
                    <svg viewBox="0 0 200 125" aria-hidden="true" focusable="false">
                        <line class="art-axis" x1="16" y1="88" x2="184" y2="88"/>
                        <path class="art-soft" d="M30 80Q50 42 70 80"/>
                        <path class="art-soft" d="M70 80Q90 42 110 80"/>
                        <path class="art-soft" d="M110 80Q130 42 150 80"/>
                        <circle class="art-dot" cx="30" cy="88" r="5"/>
                        <circle class="art-dot" cx="70" cy="88" r="5"/>
                        <circle class="art-dot" cx="110" cy="88" r="5"/>
                        <circle class="art-dot" cx="150" cy="88" r="5"/>
                        <text class="art-txt" x="50" y="54" text-anchor="middle">+4</text>
                        <text class="art-txt" x="90" y="54" text-anchor="middle">+4</text>
                        <text class="art-txt" x="130" y="54" text-anchor="middle">+4</text>
                        <text class="art-txt" x="30" y="110" text-anchor="middle">3</text>
                        <text class="art-txt" x="70" y="110" text-anchor="middle">7</text>
                        <text class="art-txt" x="110" y="110" text-anchor="middle">11</text>
                        <text class="art-txt" x="150" y="110" text-anchor="middle">15</text>
                    </svg>
                </div>
                <div class="module__body">
                    <h3>Sequences and Series</h3>
                    <p>Spot the pattern, find the next term, and add up a series.</p>
                    <span class="module__go">Sign in to start <svg class="icon" aria-hidden="true"><use href="#i-arrow"/></svg></span>
                </div>
            </a>

            <a href="{{ route('signin-signin') }}" class="module lift reveal">
                <div class="module__art">
                    <svg viewBox="0 0 200 125" aria-hidden="true" focusable="false">
                        <line class="art-axis" x1="16" y1="68" x2="184" y2="68"/>
                        <line class="art-axis" x1="100" y1="14" x2="100" y2="112"/>
                        <path class="art-line" d="M28 98C68 98 78 22 100 66S138 112 172 30"/>
                        <text class="art-txt" x="150" y="24">f(x)</text>
                    </svg>
                </div>
                <div class="module__body">
                    <h3>Polynomials and Polynomial Equations</h3>
                    <p>Work with polynomial expressions and solve polynomial equations step by step.</p>
                    <span class="module__go">Sign in to start <svg class="icon" aria-hidden="true"><use href="#i-arrow"/></svg></span>
                </div>
            </a>

            <a href="{{ route('signin-signin') }}" class="module lift reveal">
                <div class="module__art">
                    <svg viewBox="0 0 200 125" aria-hidden="true" focusable="false">
                        <line class="art-axis" x1="16" y1="90" x2="184" y2="90"/>
                        <line class="art-axis" x1="100" y1="14" x2="100" y2="112"/>
                        <path class="art-line" d="M46 20Q100 152 154 20"/>
                        <path class="art-soft" d="M28 104L172 40"/>
                        <text class="art-txt" x="150" y="24">y</text>
                    </svg>
                </div>
                <div class="module__body">
                    <h3>Advanced Equations and Functions</h3>
                    <p>Take on harder equations and see how functions behave on a graph.</p>
                    <span class="module__go">Sign in to start <svg class="icon" aria-hidden="true"><use href="#i-arrow"/></svg></span>
                </div>
            </a>

        </div>
    </div>
</section>

<!-- ================= TEACHERS ================= -->
<section class="section section--tint" id="teachers" aria-labelledby="teachers-title">
    <div class="container split">

        <div class="reveal">
            <h2 id="teachers-title">Spend less time checking, more time teaching</h2>
            <p class="lead">Track progress, send feedback, and generate quizzes with AI, all from one dashboard.</p>

            <ul class="checklist">
                <li><span class="check"><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg></span>See results as soon as students submit</li>
                <li><span class="check"><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg></span>See each student's progress by module</li>
                <li><span class="check"><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg></span>Send personalized feedback to students</li>
                <li><span class="check"><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg></span>Generate reports, keep a class record, and create AI-powered pre-tests and post-tests</li>
            </ul>

            <a href="{{ route('signin-signup') }}" class="btn btn--primary">
                Sign up as a teacher
                <svg class="icon icon--go" aria-hidden="true"><use href="#i-arrow"/></svg>
            </a>
        </div>

        <!-- Dashboard preview: real aggregates when $overview is passed, sample data otherwise -->
        <div class="dash reveal" role="img"
             aria-label="Preview of the teacher dashboard showing total students, average progress, pending feedback, and average progress by module.">

            <div class="dash__head" aria-hidden="true">
                <div>
                    <strong>Class overview</strong>
                    <span class="sub">{{ $isLive ? 'Live from the platform' : 'Grade 10 · Rizal' }}</span>
                </div>
                <span class="tag">{{ $isLive ? 'Live data' : 'Sample data' }}</span>
            </div>

            <div class="dash__stats" aria-hidden="true">
                <div class="dash__stat"><b>{{ number_format($overview['students']) }}</b><span>TOTAL STUDENTS</span></div>
                <div class="dash__stat"><b>{{ (int) $overview['avg_progress'] }}%</b><span>AVG. PROGRESS</span></div>
                <div class="dash__stat"><b>{{ number_format($overview['pending_feedback']) }}</b><span>PENDING FEEDBACK</span></div>
            </div>

            @if (! empty($overview['modules']))
                <p class="dash__title" aria-hidden="true">Average progress by module</p>
                <div class="rows" aria-hidden="true">
                    @foreach ($overview['modules'] as $module)
                        @php $avg = max(0, min(100, (int) $module['avg'])); @endphp
                        <div>
                            <div class="row__top"><span>{{ $module['name'] }}</span><span>{{ $avg }}%</span></div>
                            {{-- Width is set by homepage.js from data-width (no inline style, CSP-safe). --}}
                            <div class="bar"><span class="bar__fill{{ $avg < 65 ? ' bar__fill--low' : '' }}" data-width="{{ $avg }}"></span></div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>

    </div>
</section>

<!-- ================= FINAL CTA =================
     Full-bleed gradient band, flush on every side (edge to edge, no gap above
     or below). Only the content sits in the container. -->
<section class="cta" aria-labelledby="cta-title">
    <div class="cta__panel">
        <div class="container cta__inner reveal">

            <div class="cta__copy">
                <h2 id="cta-title">Ready for your first lesson?</h2>
                <p>Join Math Learning and take math one step at a time.</p>

                <div class="cta__actions">
                    <a href="{{ route('signin-signup') }}" class="btn btn--white">
                        Create your account
                        <svg class="icon icon--go" aria-hidden="true"><use href="#i-arrow"/></svg>
                    </a>
                    <a href="{{ route('signin-signin') }}" class="btn btn--outline-light">Sign in</a>
                </div>

                <p class="cta__terms">By creating an account you agree to our <a href="#terms">Terms and Conditions</a> and <a href="#privacy">Privacy Policy</a>.</p>
            </div>

            <div class="cta__art" aria-hidden="true">
                <svg viewBox="0 0 360 260" focusable="false">
                    <defs>
                        <pattern id="ctaGrid" width="30" height="30" x="0" y="20" patternUnits="userSpaceOnUse">
                            <path d="M30 0V30M0 30H30"/>
                        </pattern>
                    </defs>
                    <rect class="cta__grid" width="360" height="260" fill="url(#ctaGrid)"/>
                    <path class="cta__axis" d="M180 20V250M20 230H340"/>
                    <path class="cta__curve" d="M28 36Q180 424 332 36"/>
                    <circle class="cta__dot" cx="104" cy="181.5" r="6"/>
                    <circle class="cta__dot" cx="256" cy="181.5" r="6"/>
                    <circle class="cta__dot" cx="180" cy="230" r="6"/>
                </svg>
            </div>

        </div>
    </div>
</section>

<!-- ================= LEGAL =================
     Privacy Policy and Terms live on the homepage for now. #privacy and #terms
     (footer, CTA, sign-up page) open the matching panel via homepage.js.
     -->
@php
    // Optional: create config/legal.php (or set these keys) to show a real DPO contact.
    // Without it the text says "the school office".
    $legal = config('legal', []);
    $effective = $legal['effective_date'] ?? 'September 21, 2026';
    $legalEmail = $legal['contact_email'] ?? null;
    $contact = $legalEmail ? '<a href="mailto:'.e($legalEmail).'">'.e($legalEmail).'</a>' : 'the school office';
    $dpo = $legal['dpo_name'] ?? null;
@endphp
<section class="section legal-home" id="legal" aria-labelledby="legal-title">
    <div class="container">

        <div class="section__head reveal">
            <h2 id="legal-title">Privacy Policy and Terms and Conditions</h2>
            <p>How we handle your data, and the rules for using Math Learning. Select a heading to read it.</p>
        </div>

        <div class="legal-home__list">

            <details class="legal-doc" id="privacy" data-legal>
                <summary>
                    <span>Privacy Policy</span>
                    <svg class="icon" aria-hidden="true"><use href="#i-chevron"/></svg>
                </summary>
                <div class="legal legal--inline">
                    <p class="legal__meta">Effective {{ $effective }}. Applies to the Math Learning platform of Bubog National High School.</p>
                    <div class="legal__summary">
                        <strong>In short</strong>
                        We collect only what we need to run your account and help you learn math. Your teachers and the school's administrators can see your learning progress. We do not sell your personal data. You can ask us to show, correct, or delete your data at any time.
                    </div>
                    <h3 id="pp-who-we-are">1. Who we are</h3>
                    <p>Math Learning is an online math learning platform run by Bubog National High School in San Jose, Occidental Mindoro (the "school", "we", "us"). The school decides why and how personal data on this platform is used. Under the Data Privacy Act of 2012 (Republic Act No. 10173), the school is the personal information controller.</p>
                    <p>Our Data Protection Officer{{ $dpo ? ' ('.$dpo.')' : '' }} can be reached through {!! $contact !!}.</p>

                    <h3 id="pp-what-we-collect">2. What we collect</h3>
                    <ul class="legal__list">
                        <li><strong>Account details:</strong> your name, email address, role (student, teacher, or admin), and a password, which we store in a protected (hashed) form. If you sign in with Google, we receive your name and email address from Google.</li>
                        <li><strong>Learning data:</strong> the modules and topics you open or finish, your progress, your quiz and test answers and scores, and your learning streak.</li>
                        <li><strong>AI chatbot conversations:</strong> the questions you type and the answers you receive.</li>
                        <li><strong>Teacher feedback and class records:</strong> feedback your teachers send you, and the reports and class records they create.</li>
                        <li><strong>Activity and technical data:</strong> sign-in and activity records (for example, when you log in), basic technical data such as your browser type, and the cookies described below.</li>
                    </ul>
                    <p>We do not need sensitive information such as your home address, government ID numbers, or health details. Please do not type them into your profile or the chatbot.</p>

                    <h3 id="pp-why-we-use-it">3. Why we use your data</h3>
                    <ul class="legal__list">
                        <li>To create and manage your account and let you sign in.</li>
                        <li>To give you modules, quizzes, tests, the AI chatbot, and offline materials.</li>
                        <li>To record your progress and show it to you and your teachers.</li>
                        <li>To let teachers send feedback, make reports and class records, and generate quizzes.</li>
                        <li>To keep the platform secure and prevent misuse.</li>
                        <li>To improve lessons and the platform, using summarized results wherever possible.</li>
                        <li>To follow the law and school requirements.</li>
                    </ul>
                    <p>We rely on your consent (or your parent's or guardian's consent, if you are under 18) and on the school's legitimate educational purposes.</p>

                    <h3 id="pp-students-and-parents">4. Students and parents or guardians</h3>
                    <p>Many of our students are minors. For students under 18, the school asks for a parent or guardian to agree to the account. Parents and guardians can ask to see, correct, or delete their child's data. If you are a parent or guardian and did not agree to an account made for your child, contact us and we will act on it.</p>
                    <p>If you are a student and something here is unclear, please ask your teacher or a trusted adult to read it with you.</p>

                    <h3 id="pp-who-can-see-it">5. Who can see your data</h3>
                    <ul class="legal__list">
                        <li><strong>You</strong> can see your own account, progress, and results.</li>
                        <li><strong>Your teachers</strong> can see your progress, test results, and class records so they can guide you.</li>
                        <li><strong>School administrators</strong> manage accounts and the platform, and can see activity records and platform-level analytics.</li>
                        <li><strong>Service providers</strong> that help us run the platform, such as Google (if you use Google sign-in), our hosting provider, and the AI service that produces chatbot answers and generated quizzes. They may only use data to provide their service to us.</li>
                        <li><strong>Authorities</strong>, only when the law requires it or a court or government agency lawfully asks.</li>
                    </ul>
                    <p>We do not sell your personal data and we do not use it for advertising.</p>

                    <h3 id="pp-ai-chatbot">6. The AI chatbot</h3>
                    <p>The chatbot uses an AI service to answer math questions. Your messages are sent to that service so it can reply. AI answers can be wrong, so check important work with your teacher. Never share passwords, your address, phone number, or other private details in the chatbot.</p>

                    <h3 id="pp-cookies">7. Cookies</h3>
                    <p>We use only the cookies the platform needs to work:</p>
                    <ul class="legal__list">
                        <li>a session cookie, which keeps you signed in while you use the platform;</li>
                        <li>a security token, which protects forms from being sent by someone else; and</li>
                        <li>a "remember me" cookie, which keeps you signed in on your device for up to 30 days if you tick "Remember me for 30 days".</li>
                    </ul>
                    <p>We do not use advertising cookies. You can delete cookies in your browser settings, but you will not be able to sign in without them.</p>

                    <h3 id="pp-how-long">8. How long we keep your data</h3>
                    <p>We keep your data while your account is active and for as long as the school needs it for its learning records. When it is no longer needed, we delete it or make it anonymous. You can ask us to delete your account (see "Your rights"). We may keep some records where the law or school policy requires it.</p>

                    <h3 id="pp-security">9. How we protect your data</h3>
                    <p>We use reasonable organizational, physical, and technical safeguards. For example, passwords are stored in hashed form, what you can see depends on your role, and the platform is meant to be used over secure connections. No system is perfectly secure. If a data breach happens that puts you at real risk, we will notify the National Privacy Commission and the people affected as the law requires.</p>

                    <h3 id="pp-your-rights">10. Your rights</h3>
                    <p>Under the Data Privacy Act, you have the right to:</p>
                    <ul class="legal__list">
                        <li>be informed about how your data is used;</li>
                        <li>access the personal data we hold about you;</li>
                        <li>object to how your data is used;</li>
                        <li>correct data that is wrong or out of date;</li>
                        <li>ask us to delete or block data that is no longer needed or was unlawfully collected;</li>
                        <li>receive a copy of your data in a common format (data portability);</li>
                        <li>claim damages if you were harmed by misuse of your data; and</li>
                        <li>file a complaint with the National Privacy Commission at <a href="https://privacy.gov.ph" rel="noopener noreferrer">privacy.gov.ph</a>.</li>
                    </ul>
                    <p>To use any of these rights, contact us using the details below. Parents and guardians may do this for their child.</p>

                    <h3 id="pp-changes">11. Changes to this policy</h3>
                    <p>We may update this policy. The date at the top shows when it last changed. If the changes are important, we will also tell you on the platform.</p>

                    <h3 id="pp-contact">12. Contact us</h3>
                    <p>Questions or requests about your data? Contact the Data Protection Officer of Bubog National High School through {!! $contact !!}. Please read our <a href="#terms">Terms and Conditions</a> too.</p>
                </div>
            </details>

            <details class="legal-doc" id="terms" data-legal>
                <summary>
                    <span>Terms and Conditions</span>
                    <svg class="icon" aria-hidden="true"><use href="#i-chevron"/></svg>
                </summary>
                <div class="legal legal--inline">
                    <p class="legal__meta">Effective {{ $effective }}. Applies to the Math Learning platform of Bubog National High School.</p>
                    <div class="legal__summary">
                        <strong>In short</strong>
                        Use Math Learning to learn, be honest and respectful, and keep your password to yourself. The AI chatbot can make mistakes, so check with your teacher. The school can suspend accounts that break these rules.
                    </div>
                    <h3 id="tc-agreeing">1. Agreeing to these terms</h3>
                    <p>By creating an account or using Math Learning, you agree to these Terms and Conditions. If you are under 18, your parent or guardian must also agree to them for you. If you do not agree, please do not use the platform.</p>

                    <h3 id="tc-the-platform">2. About the platform</h3>
                    <p>Math Learning is run by Bubog National High School to support Junior High School math. It includes learning modules (Sequences and Series, Polynomials and Polynomial Equations, and Advanced Equations and Functions), quizzes and summative tests, an AI chatbot, progress tracking, offline materials, and tools for teachers and administrators.</p>

                    <h3 id="tc-accounts">3. Your account</h3>
                    <ul class="legal__list">
                        <li>Give accurate information when you sign up. Some accounts, such as teacher accounts, may need approval from the school.</li>
                        <li>Keep your password private. You are responsible for what happens under your account.</li>
                        <li>One person, one account. Do not share your account or pretend to be someone else.</li>
                        <li>Tell your teacher or the school right away if you think someone else used your account.</li>
                    </ul>

                    <h3 id="tc-acceptable-use">4. How to use the platform</h3>
                    <p>Please use Math Learning for learning, and be honest and respectful. You agree not to:</p>
                    <ul class="legal__list">
                        <li>cheat, share test answers, or use the chatbot on any test or assessment your teacher says you must do on your own;</li>
                        <li>bully, harass, or send harmful or inappropriate content to anyone;</li>
                        <li>try to break, hack, overload, or copy data from the platform;</li>
                        <li>look at, change, or collect other people's accounts or data;</li>
                        <li>upload viruses or anything else meant to cause damage; or</li>
                        <li>use the platform for anything unlawful or for making money.</li>
                    </ul>

                    <h3 id="tc-ai-content">5. The AI chatbot and generated content</h3>
                    <p>The chatbot and the quiz generator use artificial intelligence. They can make mistakes or give incomplete explanations. Treat their answers as help, not as final truth, and check important work with your teacher. Teachers should review AI-generated quizzes before giving them to students. Do not share private information in the chatbot.</p>

                    <h3 id="tc-teachers-admins">6. Teachers and administrators</h3>
                    <p>If you are a teacher or administrator, you agree to use student information only for teaching and school administration, to keep it confidential, and to follow school rules and the Data Privacy Act of 2012. Give feedback that is fair and respectful. Manage accounts and platform settings responsibly.</p>

                    <h3 id="tc-materials">7. Learning materials and offline downloads</h3>
                    <p>The lessons, quizzes, and other materials belong to the school or the people who made them. You may use them for your own learning, including offline. Please do not copy, sell, or post them publicly. The work you create, such as your answers and messages, stays yours. You allow the school to use it to run the platform, show your progress, and (without your name) improve lessons.</p>

                    <h3 id="tc-privacy">8. Privacy</h3>
                    <p>Our <a href="#privacy">Privacy Policy</a> explains what personal data we collect, how we use it, and your rights. It is part of these terms.</p>

                    <h3 id="tc-availability">9. Availability and changes to the platform</h3>
                    <p>We try to keep Math Learning working, but we cannot promise it will always be available or error-free. Maintenance or internet problems can interrupt it. Offline downloads may not include the latest updates. We may add, change, or remove features.</p>

                    <h3 id="tc-suspension">10. Suspension and closing accounts</h3>
                    <p>The school may suspend or remove an account that breaks these terms or school rules. You can ask to close your account at any time by contacting us.</p>

                    <h3 id="tc-liability">11. Limits of responsibility</h3>
                    <p>Math Learning is an educational support tool, provided "as is". To the extent the law allows, the school is not responsible for indirect losses that come from using the platform, not being able to use it, or relying on AI-generated content. Nothing in these terms limits any right you have under Philippine law, including the Data Privacy Act of 2012.</p>

                    <h3 id="tc-governing-law">12. Governing law</h3>
                    <p>These terms are governed by the laws of the Republic of the Philippines.</p>

                    <h3 id="tc-changes">13. Changes to these terms</h3>
                    <p>We may update these terms. The date at the top shows when they last changed. If you keep using the platform after an update, you agree to the new terms. If the changes are important, we will tell you on the platform.</p>

                    <h3 id="tc-contact">14. Contact us</h3>
                    <p>Questions about these terms? Contact Bubog National High School through {!! $contact !!}.</p>
                </div>
            </details>

        </div>
    </div>
</section>

</main>

<!-- ================= FOOTER ================= -->
<footer class="footer">
    <div class="container">

        <div class="footer__grid">

            <div class="footer__brand">
                <a href="#top" class="logo" aria-label="Math Learning, back to top">
                    <svg class="logo__mark" viewBox="0 0 32 32" aria-hidden="true"><use href="#logo-mark"/></svg>
                    <span>Math Learning</span>
                </a>
                <p>Empowering students through interactive mathematics education.</p>

                @if (collect($socials)->contains(fn ($s) => ! empty($s['url'])))
                    <ul class="socials">
                        @foreach ($socials as $social)
                            @if (! empty($social['url']))
                                <li>
                                    <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['label'] }}">
                                        <svg class="icon" aria-hidden="true"><use href="#{{ $social['icon'] }}"/></svg>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @endif
            </div>

            <nav aria-label="Explore">
                <h3>Explore</h3>
                <ul>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#how-it-works">How it works</a></li>
                    <li><a href="#modules">Modules</a></li>
                    <li><a href="#teachers">For teachers</a></li>
                </ul>
            </nav>

            <nav aria-label="Account">
                <h3>Account</h3>
                <ul>
                    <li><a href="{{ route('signin-signin') }}">Sign In</a></li>
                    <li><a href="{{ route('signin-signup') }}">Sign Up</a></li>
                </ul>
            </nav>

            <nav aria-label="Legal">
                <h3>Legal</h3>
                <ul>
                    <li><a href="#privacy">Privacy Policy</a></li>
                    <li><a href="#terms">Terms and Conditions</a></li>
                </ul>
            </nav>


        </div>

        <div class="footer__legal">
            <p>© {{ now()->year }} Math Learning Assistant</p>
            <p>Bubog National High School</p>
        </div>

    </div>
</footer>

<!-- ================= CSRF ================= -->
<script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    window.Laravel = {
        csrfToken: '{{ csrf_token() }}'
    };
</script>

</body>
</html>