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

        <!-- Dashboard preview (sample data for illustration) -->
        <div class="dash reveal" role="img"
             aria-label="Preview of the teacher dashboard showing class average, quiz submissions, topic scores, and students who need help.">

            <div class="dash__head" aria-hidden="true">
                <div>
                    <strong>Class overview</strong>
                    <span class="sub">Grade 10 · Rizal</span>
                </div>
                <span class="tag">Sample data</span>
            </div>

            <div class="dash__stats" aria-hidden="true">
                <div class="dash__stat"><b>38</b><span>TOTAL STUDENTS</span></div>
                <div class="dash__stat"><b>82%</b><span>AVG. PROGRESS</span></div>
                <div class="dash__stat"><b>4</b><span>PENDING FEEDBACK</span></div>
            </div>

            <p class="dash__title" aria-hidden="true">Average progress by module</p>
            <div class="rows" aria-hidden="true">
                <div>
                    <div class="row__top"><span>Sequences and Series</span><span>88%</span></div>
                    <div class="bar"><span class="bar__fill w-88"></span></div>
                </div>
                <div>
                    <div class="row__top"><span>Polynomials and Polynomial Equations</span><span>74%</span></div>
                    <div class="bar"><span class="bar__fill w-74"></span></div>
                </div>
                <div>
                    <div class="row__top"><span>Advanced Equations and Functions</span><span>61%</span></div>
                    <div class="bar"><span class="bar__fill bar__fill--low w-61"></span></div>
                </div>
            </div>

            <div class="students" aria-hidden="true">
                <div class="student">
                    <span class="avatar">MJ</span>
                    <span class="name">Mark Joseph T.</span>
                    <span class="pct">54%</span>
                    <span class="pill">Send feedback</span>
                </div>
                <div class="student">
                    <span class="avatar">AR</span>
                    <span class="name">Angela R.</span>
                    <span class="pct">58%</span>
                    <span class="pill">Send feedback</span>
                </div>
            </div>

        </div>

    </div>
</section>

<!-- ================= FINAL CTA ================= -->
<section class="cta" aria-labelledby="cta-title">

    <picture>
        <source type="image/webp"
                srcset="/image/pexels-photo-6344238-640w.webp 640w,
                        /image/pexels-photo-6344238-1280w.webp 1280w,
                        /image/pexels-photo-6344238-1440w.webp 1440w,
                        /image/pexels-photo-6344238-1920w.webp 1920w"
                sizes="100vw">
        <img class="cta__photo" src="/image/pexels-photo-6344238.jpeg" alt=""
             width="1920" height="1280" loading="lazy" decoding="async">
    </picture>

    <div class="container reveal">
        <h2 id="cta-title">Ready for your first lesson?</h2>
        <p>Join Math Learning and take math one step at a time.</p>

        <div class="cta__actions">
            <a href="{{ route('signin-signup') }}" class="btn btn--white">
                Create your account
                <svg class="icon icon--go" aria-hidden="true"><use href="#i-arrow"/></svg>
            </a>
            <p class="cta__alt">Already have an account? <a href="{{ route('signin-signin') }}">Sign in</a></p>
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

            @if (collect($socials)->contains(fn ($s) => ! empty($s['url'])))
                <div>
                    <h3>Follow us</h3>
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
                </div>
            @endif

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