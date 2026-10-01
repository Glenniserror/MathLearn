<?php

use App\Models\Section;
use App\Models\User;

/*
 * The student, teacher, and admin dashboards share one palette: blue for
 * everything, gray for neutral states, and red only for destructive or
 * error states. The stat cards' colored icon tiles are the one deliberate
 * exception, so those are asserted to stay as they are.
 */

it('renders the student dashboard with blue action and download tiles', function () {
    $student = User::factory()->approved()->create([
        'role' => 'student',
        'section_id' => Section::factory()->create()->id,
    ]);

    $response = $this->actingAs($student)->get(route('student.dashboard'));

    $response->assertOk()
        ->assertSee('action-icon-wrap blue-theme', false)
        ->assertSee('download-icon blue-theme', false)
        ->assertSee('class="nav-badge"', false)
        ->assertSee('class="notice-card"', false)
        ->assertSee('icon-container green-theme', false)
        ->assertSee('icon-container purple-theme', false);

    foreach (['green', 'orange', 'purple'] as $color) {
        $response->assertDontSee("action-icon-wrap {$color}-theme", false)
            ->assertDontSee("download-icon {$color}-theme", false);
    }

    $response->assertDontSee('#fcd34d', false)
        ->assertDontSee('background:#ef4444', false);
});

it('renders pending student approvals on the teacher dashboard as blue approval cards', function () {
    $teacher = User::factory()->teacher()->approved()->create();
    $pending = User::factory()->create([
        'role' => 'student',
        'approval_status' => 'pending',
        'section_id' => Section::factory()->create(['teacher_id' => $teacher->id])->id,
    ]);

    $response = $this->actingAs($teacher)->get(route('teacher.dashboard'));

    $response->assertOk()
        ->assertSee('class="approval-panel"', false)
        ->assertSee($pending->name)
        ->assertSee('approval-btn approve', false)
        ->assertSee('approval-btn reject', false)
        ->assertSee('class="secondary-btn" id="report-export-btn"', false)
        ->assertSee('icon-container purple-theme', false)
        ->assertDontSee('action-icon-wrap orange-theme', false)
        ->assertDontSee('action-icon-wrap purple-theme', false)
        ->assertDontSee('#fef3c7', false)
        ->assertDontSee('#10b981', false)
        ->assertDontSee('#1e88e5', false);
});

it('renders pending teacher approvals and permissions on the admin dashboard in blue', function () {
    $admin = User::factory()->admin()->create();
    $pending = User::factory()->teacher()->create(['approval_status' => 'pending']);

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertOk()
        ->assertSee('class="approval-panel"', false)
        ->assertSee($pending->name)
        ->assertSee('class="perm yes"', false)
        ->assertSee('class="perm no"', false)
        ->assertSee('class="secondary-btn" id="open-clear-old-logs-btn"', false)
        ->assertSee('icon-container orange-theme', false)
        ->assertDontSee('✅')
        ->assertDontSee('❌')
        ->assertDontSee('background:#f97316', false)
        ->assertDontSee('#fef3c7', false)
        ->assertDontSee('#10b981', false);

    foreach (['green', 'orange', 'purple'] as $color) {
        $response->assertDontSee("action-icon-wrap {$color}-theme", false);
    }
});

it('renders the learning modules page without the green, amber, or purple accents', function () {
    $student = User::factory()->approved()->create([
        'role' => 'student',
        'section_id' => Section::factory()->create()->id,
    ]);

    $response = $this->actingAs($student)->get(route('student.modules'));

    $response->assertOk()
        ->assertSee('class="mq-btn mq-btn--secondary" id="mq-activity-btn">Activity</button>', false)
        ->assertDontSee('var(--green', false)
        ->assertDontSee('var(--amber', false)
        ->assertDontSee('var(--purple', false)
        ->assertDontSee('var(--orange', false);
});

it('renders the approval queue pages with blue actions', function (string $role, string $routeName) {
    $user = User::factory()->approved()->create(['role' => $role]);

    $this->actingAs($user)->get(route($routeName))
        ->assertOk()
        ->assertSee('.btn-approve { background: #2563eb', false)
        ->assertDontSee('#10b981', false)
        ->assertDontSee('#fef3c7', false)
        ->assertDontSee('#1e88e5', false);
})->with([
    'admin teacher approvals' => ['admin', 'admin.teacher-approvals'],
    'teacher student approvals' => ['teacher', 'teacher.student-approvals'],
]);

it('keeps green, orange, and purple out of the dashboard styles except the stat-card tiles', function (string $path) {
    $css = file_get_contents(resource_path($path));

    // The :root tokens and the stat cards' icon-tile classes are the only
    // places these accents are still allowed to appear.
    $outsideStatTiles = preg_replace(
        ['/:root\s*\{[^}]*\}/', '/\.(green|orange|purple)-theme\s*\{[^}]*\}/'],
        '',
        $css,
    );

    expect($outsideStatTiles)
        ->not->toMatch('/var\(--(green|orange|purple|yellow)/')
        ->not->toMatch('/#(10b981|059669|16a34a|0f9b6c|f97316|ea580c|f59e0b|fbbf24|a855f7|8b5cf6)\b/i');
})->with([
    'student' => 'css/dashboard/student_dashboard.css',
    'teacher' => 'css/dashboard/teacher_dashboard.css',
    'admin' => 'css/dashboard/admin_dashboard.css',
    'chat widget' => 'css/dashboard/chatbot.css',
    'chat widget tabs' => 'css/dashboard/math-panel.css',
]);

it('keeps green, orange, and purple out of the markup the dashboard scripts render', function (string $path) {
    $js = file_get_contents(resource_path($path));

    expect($js)
        ->not->toMatch('/(green|orange|purple)-(theme|avatar)/')
        ->not->toMatch('/var\(--(green|orange|purple)/')
        ->not->toMatch('/#(10b981|059669|16a34a|f97316|ea580c|f59e0b|d97706|a855f7|8b5cf6|ec4899|06b6d4|1e88e5)\b/i');
})->with([
    'student' => 'js/dashboard/student_dashboard.js',
    'teacher' => 'js/dashboard/teacher_dashboard.js',
    'admin' => 'js/dashboard/admin_dashboard.js',
    'page-load progress bar' => 'js/nav-progress.js',
]);
