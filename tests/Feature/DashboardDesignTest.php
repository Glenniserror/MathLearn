<?php

use App\Models\Section;
use App\Models\User;

dataset('dashboard stylesheets', [
    'student' => 'css/dashboard/student_dashboard.css',
    'teacher' => 'css/dashboard/teacher_dashboard.css',
    'admin' => 'css/dashboard/admin_dashboard.css',
]);

dataset('dashboard scripts', [
    'student' => 'js/dashboard/student_dashboard.js',
    'teacher' => 'js/dashboard/teacher_dashboard.js',
    'admin' => 'js/dashboard/admin_dashboard.js',
]);

it('keeps the original green, orange and purple palette', function (string $stylesheet) {
    $css = file_get_contents(resource_path($stylesheet));

    expect($css)
        ->toMatch('/--green:\s*#10b981;/')
        ->toMatch('/--orange:\s*#f97316;/')
        ->toMatch('/--purple:\s*#a855f7;/')
        ->toMatch('/\.action-icon-wrap\.orange-theme\s*\{[^}]*linear-gradient\(135deg, #fb923c, var\(--orange\)\)/')
        ->toMatch('/\.action-icon-wrap\.purple-theme\s*\{[^}]*linear-gradient\(135deg, #c084fc, var\(--purple\)\)/');
})->with('dashboard stylesheets');

it('gives sweetalert popups the dashboard blue and gray buttons', function (string $stylesheet) {
    expect(file_get_contents(resource_path($stylesheet)))
        ->toMatch('/\.swal2-container\s*\{[^}]*--swal2-confirm-button-background-color:\s*var\(--blue\);[^}]*--swal2-cancel-button-background-color:\s*var\(--text-3\);/');
})->with('dashboard stylesheets');

it('makes the logout confirm button red with a gray cancel', function (string $script) {
    preg_match('/You will be logged out of your account\.(.*?)confirmButtonText: \'Yes, logout!\'/s', file_get_contents(resource_path($script)), $dialog);

    expect($dialog[1] ?? '')
        ->toMatch("/confirmButtonColor:\s*'#ef4444'/")
        ->toMatch("/cancelButtonColor:\s*'#6b7280'/");
})->with('dashboard scripts');

it('never shows a red or washed-out cancel button in confirm dialogs', function (string $script) {
    expect(file_get_contents(resource_path($script)))
        ->not->toContain("cancelButtonColor: '#d33'")
        ->not->toContain("cancelButtonColor: '#d1d5db'")
        ->not->toContain("cancelButtonColor:'#d33'");
})->with('dashboard scripts');

it('styles the pending student approvals panel for teachers', function () {
    $teacher = User::factory()->teacher()->create();
    $section = Section::factory()->create(['teacher_id' => $teacher->id]);
    $student = User::factory()->create([
        'name' => 'ana Reyes',
        'approval_status' => 'pending',
        'section_id' => $section->id,
    ]);

    $this->actingAs($teacher)->get(route('teacher.dashboard'))
        ->assertOk()
        ->assertSee('<section class="approval-panel">', false)
        ->assertSee('<div class="approval-avatar">A</div>', false)
        ->assertSee('ana Reyes')
        ->assertSee(route('teacher.student.approve', $student->id), false)
        ->assertSee(route('teacher.student.reject', $student->id), false)
        ->assertSee('class="approval-btn approve"', false)
        ->assertSee('class="approval-btn reject"', false)
        ->assertDontSee('border-left: 4px solid #f59e0b', false);
});

it('styles the pending teacher approvals panel for admins', function () {
    $admin = User::factory()->admin()->create();
    $teacher = User::factory()->teacher()->create([
        'name' => 'ñino Santos',
        'approval_status' => 'pending',
    ]);

    $this->actingAs($admin)->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('<section class="approval-panel">', false)
        ->assertSee('<div class="approval-avatar">Ñ</div>', false)
        ->assertSee(route('admin.teacher.approve', $teacher->id), false)
        ->assertSee(route('admin.teacher.reject', $teacher->id), false)
        ->assertDontSee('border-left: 4px solid #f59e0b', false);
});

it('hides the approvals panel when nobody is waiting', function () {
    $teacher = User::factory()->teacher()->create();
    $admin = User::factory()->admin()->create();
    $otherSection = Section::factory()->create();
    User::factory()->create(['approval_status' => 'pending', 'section_id' => $otherSection->id]);

    $this->actingAs($teacher)->get(route('teacher.dashboard'))
        ->assertOk()
        ->assertDontSee('class="approval-panel"', false);

    $this->actingAs($admin)->get(route('admin.dashboard'))
        ->assertOk()
        ->assertDontSee('class="approval-panel"', false);
});

it('styles the approval panel with the original amber, green and red colors', function (string $stylesheet) {
    expect(file_get_contents(resource_path($stylesheet)))
        ->toMatch('/\.approval-item\s*\{[^}]*background:\s*#fffbeb;[^}]*border-left:\s*4px solid #f59e0b;/')
        ->toMatch('/\.approval-btn\.approve\s*\{[^}]*background:\s*var\(--green\);/')
        ->toMatch('/\.approval-btn\.reject\s*\{[^}]*color:\s*#dc2626;/');
})->with([
    'teacher' => 'css/dashboard/teacher_dashboard.css',
    'admin' => 'css/dashboard/admin_dashboard.css',
]);

it('draws the search icon in css instead of an emoji placeholder', function () {
    $teacher = User::factory()->teacher()->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($teacher)->get(route('teacher.dashboard'))
        ->assertOk()
        ->assertSee('placeholder="Search by name…"', false)
        ->assertDontSee('placeholder="🔍', false);

    $this->actingAs($admin)->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('placeholder="Search by name or email…"', false)
        ->assertDontSee('placeholder="🔍', false);

    foreach (['teacher', 'admin'] as $role) {
        expect(file_get_contents(resource_path("css/dashboard/{$role}_dashboard.css")))
            ->toMatch('/\.search-input\s*\{[^}]*url\("data:image\/svg\+xml/');
    }
});

it('renders roles and permissions as allowed and denied icons', function () {
    $admin = User::factory()->admin()->create();

    $html = $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->getContent();

    expect(substr_count($html, 'class="perm-icon is-allowed"'))->toBe(12)
        ->and(substr_count($html, 'class="perm-icon is-denied"'))->toBe(6)
        ->and($html)->not->toContain('<td>✅</td>')
        ->and($html)->not->toContain('<td>❌</td>');
});

it('gives the activity log buttons their own colors with working hover states', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('class="primary-btn is-muted" id="open-archived-logs-btn"', false)
        ->assertSee('class="primary-btn is-warning" id="open-clear-old-logs-btn"', false)
        ->assertDontSee('background:#6b7280"', false)
        ->assertDontSee('background:#f97316"', false);

    expect(file_get_contents(resource_path('css/dashboard/admin_dashboard.css')))
        ->toMatch('/\.primary-btn\.is-muted:hover\s*\{[^}]*background:\s*#4b5563;/')
        ->toMatch('/\.primary-btn\.is-warning:hover\s*\{[^}]*background:\s*#ea580c;/');
});

it('renders the danger zone with classes instead of inline styles', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('<div class="settings-section is-danger">', false)
        ->assertSee('class="danger-btn is-compact" id="reset-platform-btn"', false)
        ->assertDontSee('max-width:200px', false);
});

it('colors the admin pending-review module badge', function () {
    expect(file_get_contents(resource_path('css/dashboard/admin_dashboard.css')))
        ->toMatch('/\.badge-average\s*\{[^}]*background:\s*#fef3c7;[^}]*color:\s*#b45309;/');

    expect(file_get_contents(resource_path('js/dashboard/admin_dashboard.js')))
        ->toContain("return 'badge-average';");
});

it('balances stat card rows so no card is left alone on a row', function () {
    expect(file_get_contents(resource_path('css/dashboard/admin_dashboard.css')))
        ->toContain('.metrics-grid:has(> .metric-card:nth-child(4):last-child) { grid-template-columns: repeat(2, 1fr); }')
        ->toContain('.metrics-grid:has(> .metric-card:nth-child(5):last-child) { grid-template-columns: repeat(6, 1fr); }');

    expect(file_get_contents(resource_path('css/dashboard/teacher_dashboard.css')))
        ->toContain('.metrics-grid:has(> .metric-card:nth-child(6):last-child) { grid-template-columns: repeat(3, 1fr); }');
});

it('shows module review hints in the original amber and red', function (string $role) {
    expect(file_get_contents(resource_path("js/dashboard/{$role}_dashboard.js")))
        ->toContain('<div class="module-hint is-rejected">')
        ->toContain('<div class="module-hint is-pending">');

    expect(file_get_contents(resource_path("css/dashboard/{$role}_dashboard.css")))
        ->toMatch('/\.module-hint\.is-pending\s*\{\s*background:\s*#fff7ed;\s*color:\s*#ea580c;\s*\}/')
        ->toMatch('/\.module-hint\.is-rejected\s*\{\s*background:\s*#fef2f2;\s*color:\s*#dc2626;\s*\}/');
})->with(['teacher', 'admin']);

it('marks only destructive teacher table actions in red', function () {
    $js = file_get_contents(resource_path('js/dashboard/teacher_dashboard.js'));

    expect(substr_count($js, 'class="tbl-btn feedback"'))->toBe(1)
        ->and($js)->toContain('data-action="open-feedback"')
        ->toContain('<button class="tbl-btn del" data-action="delete-module"')
        ->toMatch('/class="tbl-btn del"\s+data-action="delete-saved-quiz"/')
        ->toMatch('/class="tbl-btn del"\s+data-action="unpublish"/')
        ->toMatch('/class="tbl-btn del"[^>]*\s+data-action="remove-custom-topic"/');

    expect(file_get_contents(resource_path('css/dashboard/teacher_dashboard.css')))
        ->toMatch('/\.tbl-btn\.del\s*\{\s*color:\s*#b91c1c;\s*\}/')
        ->toMatch('/\.tbl-btn\.feedback:hover\s*\{[^}]*color:\s*#9333ea;/');
});

it('keeps each section on its original accent color across reports and class record', function () {
    $js = file_get_contents(resource_path('js/dashboard/teacher_dashboard.js'));

    expect($js)
        ->toContain("const SECTION_COLORS = ['#3b82f6', '#10b981', '#f97316', '#8b5cf6', '#ec4899', '#06b6d4'];")
        ->toContain('<div class="section-card" style="--section-color:${sectionColor(idx)}">')
        ->toContain('<div class="section-card is-record" style="--section-color:${sectionColor(idx)}">');

    expect(file_get_contents(resource_path('css/dashboard/teacher_dashboard.css')))
        ->toMatch('/\.marker\s*\{[^}]*background:\s*var\(--section-color\);/')
        ->toMatch('/\.section-card\.is-record\s*\{\s*border-left:\s*4px solid var\(--section-color\);\s*\}/');
});

it('shows the locked summative test notice as an amber card', function () {
    $student = User::factory()->create(['section_id' => Section::factory()->create()->id]);

    $this->actingAs($student)->get(route('student.dashboard'))
        ->assertOk()
        ->assertSee('<section class="notice-card">', false)
        ->assertSee('<div id="lock-progress-display" class="notice-progress"></div>', false);

    expect(file_get_contents(resource_path('css/dashboard/student_dashboard.css')))
        ->toMatch('/\.notice-card\s*\{[^}]*background:\s*#fffbeb;/');
});

it('gives the math assistant the same blue gradient as the ai chat button', function () {
    $blueGradient = 'linear-gradient(135deg, var(--blue-mid), var(--blue))';
    $mathPanel = file_get_contents(resource_path('css/dashboard/math-panel.css'));

    expect(file_get_contents(resource_path('css/dashboard/student_dashboard.css')))
        ->toMatch('/\.sidebar-fab-btn\s*\{[^}]*background:\s*'.preg_quote($blueGradient, '/').';/');

    expect($mathPanel)
        ->toContain(".chat-header { background: {$blueGradient}; }")
        ->toContain(".fab-btn { background: {$blueGradient}; }")
        ->not->toMatch('/#0F9B6C/i');
});

it('shows the page loading bar in the same blue gradient', function () {
    expect(file_get_contents(resource_path('js/nav-progress.js')))
        ->toContain('background: linear-gradient(90deg, #60a5fa, #2563eb);')
        ->not->toContain('#10b981');
});

it('escapes teacher feedback before it reaches the student dashboard', function () {
    expect(file_get_contents(resource_path('js/dashboard/student_dashboard.js')))
        ->toContain('${escapeHtml(f.teacherName)}')
        ->toContain('${escapeHtml(f.date)}');
});

it('lets wide approval tables scroll on small screens', function () {
    $admin = User::factory()->admin()->create();
    $teacher = User::factory()->teacher()->create();

    $this->actingAs($admin)->get(route('admin.teacher-approvals'))
        ->assertOk()
        ->assertSee('overflow-x: auto', false)
        ->assertDontSee('overflow: hidden', false)
        ->assertDontSee('#1e88e5', false);

    $this->actingAs($teacher)->get(route('teacher.student-approvals'))
        ->assertOk()
        ->assertSee('overflow-x: auto', false)
        ->assertDontSee('overflow: hidden', false)
        ->assertDontSee('#1e88e5', false);
});
