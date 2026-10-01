<?php

use App\Models\Section;
use App\Models\StudentProgress;
use App\Models\TeacherFeedback;
use App\Models\User;
use App\Services\PlatformOverview;

use function Pest\Laravel\get;

it('shows labelled sample data until a student is approved', function () {
    User::factory()->create(['approval_status' => 'pending']);

    get('/')->assertOk()
        ->assertSee('Class overview')
        ->assertSee('Sample data')
        ->assertDontSee('Live data');
});

it('shows live platform-wide numbers once students are approved', function () {
    $section = Section::factory()->create();
    [$ana, $ben, $carla] = User::factory()->count(3)->create(['section_id' => $section->id])->all();
    User::factory()->create(['approval_status' => 'pending', 'section_id' => $section->id]);

    foreach ([[$ana, 'ari'], [$ana, 'geo'], [$ben, 'div']] as [$student, $topic]) {
        StudentProgress::create(['session_id' => (string) $student->id, 'topic_key' => $topic, 'phase' => 'post', 'score' => 4, 'total' => 5, 'passed' => true]);
    }

    // A pre-test alone doesn't finish a topic.
    StudentProgress::create(['session_id' => (string) $carla->id, 'topic_key' => 'har', 'phase' => 'pre', 'score' => 2, 'total' => 5]);

    TeacherFeedback::factory()->count(2)->create(['teacher_id' => $section->teacher_id, 'student_id' => $ana->id]);
    TeacherFeedback::factory()->read()->create(['teacher_id' => $section->teacher_id, 'student_id' => $ben->id]);
    TeacherFeedback::factory()->create(['teacher_id' => $section->teacher_id, 'student_id' => $ben->id, 'sender' => 'student']);

    get('/')->assertOk()
        ->assertSee('School overview')
        ->assertSee('Live data')
        ->assertDontSee('Sample data')
        // 3 approved students; the pending one isn't counted.
        ->assertSee('<b class="metric__value">3</b>', false)
        // 3 of 36 student-topics done.
        ->assertSee('<b class="metric__value">8%</b>', false)
        // 2 unread teacher feedback; read feedback and a student's reply don't count.
        ->assertSee('<b class="metric__value">2</b>', false)
        // Module 1: 2 of 15, module 2: 1 of 9, module 3: none yet.
        ->assertSee('data-width="13"', false)
        ->assertSee('data-width="11"', false)
        ->assertSee('data-width="0"', false)
        ->assertDontSee($ana->name)
        ->assertDontSee($ben->name);
});

it('colors module progress like the dashboards: blue in progress, green when complete', function () {
    $student = User::factory()->create();

    foreach (['ari', 'geo', 'har', 'fib', 'fin', 'div'] as $topic) {
        StudentProgress::create(['session_id' => (string) $student->id, 'topic_key' => $topic, 'phase' => 'post', 'score' => 5, 'total' => 5, 'passed' => true]);
    }

    preg_match('/<section[^>]+id="teachers".*?<\/section>/s', get('/')->assertOk()->getContent(), $section);

    expect($section[0] ?? '')
        ->toContain('<span class="row__pct row__pct--done">100%</span>')
        ->toContain('<span class="bar__fill bar__fill--done" data-width="100"></span>')
        ->toContain('<span class="row__pct">33%</span>')
        ->toContain('<span class="bar__fill" data-width="33"></span>')
        ->not->toContain('--low');
});

it('returns only aggregate numbers, never any one student', function () {
    User::factory()->create(['name' => 'Juan Dela Cruz']);

    $overview = app(PlatformOverview::class)->get();

    expect(array_keys($overview))->toBe(['students', 'avg_progress', 'pending_feedback', 'modules'])
        ->and(collect($overview['modules'])->every(fn (array $module) => array_keys($module) === ['name', 'avg']))->toBeTrue()
        ->and(json_encode($overview))->not->toContain('Juan');
});

it('refreshes the live numbers every few minutes', function () {
    User::factory()->create();

    expect(app(PlatformOverview::class)->get()['students'])->toBe(1);

    User::factory()->create();

    expect(app(PlatformOverview::class)->get()['students'])->toBe(1);

    $this->travel(PlatformOverview::CACHE_MINUTES + 1)->minutes();

    expect(app(PlatformOverview::class)->get()['students'])->toBe(2);
});
