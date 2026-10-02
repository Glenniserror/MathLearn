<?php

use App\Models\Section;
use App\Models\StudentProgress;
use App\Models\User;

beforeEach(function () {
    $this->student = User::factory()->create(['section_id' => Section::factory()->create()->id]);
    $this->modulePage = $this->actingAs($this->student)->get(route('student.modules'))->assertOk()->getContent();
});

it('opens a finished topic again as practice instead of blocking it', function () {
    expect($this->modulePage)
        ->toContain('const practiceRun = !!mqState.completed[key];')
        ->toContain("confirmButtonText: 'Practice Again'")
        ->toContain("denyButtonText: 'Review My Answers'")
        ->toContain('Only your first attempt counts.')
        ->not->toContain("Retakes aren't allowed")
        ->not->toContain('showPhaseAlreadySubmitted');
});

it('treats a phase as practice once its first attempt is recorded', function () {
    expect($this->modulePage)
        ->toContain("mqState.practice  = mqHasAttempt(key, 'pre');")
        ->toContain("mqState.practice = mqHasAttempt(mqState.topicKey, 'activity');")
        ->toContain("mqState.practice = mqHasAttempt(key, 'post') || !!mqState.completed[key];");
});

it('saves nothing from a practice round', function () {
    expect($this->modulePage)
        ->toMatch("/if \(!mqState\.practice\) \{\s*stateFlags\[key\]\.pre=true;.*?mqSaveProgress\(key,'pre'/s")
        ->toMatch("/if \(!mqState\.practice\) \{\s*mqSaveQuizAnswers\(key,'post'.*?mqMarkDone\(\);/s")
        ->toMatch("/if \(!mqState\.practice\) \{\s*stateFlags\[key\]\.activityDone = true;.*?mqSaveQuizAnswers\(key, 'activity'/s");
});

it('lets a failed first activity be practiced until it unlocks the post-test', function () {
    expect($this->modulePage)
        ->toContain("document.getElementById('mq-activity-btn').disabled=false;")
        ->toContain('Practice passed (')
        ->toContain('the Post-Test is unlocked. Your first attempt stays on record.')
        ->not->toContain('one-time attempt, so this result is final');
});

it('still records only the first attempt on the server', function () {
    $this->actingAs($this->student)
        ->postJson(route('student.progress.store'), ['topic_key' => 'ari', 'phase' => 'post', 'score' => 3, 'total' => 15])
        ->assertOk();

    $this->actingAs($this->student)
        ->postJson(route('student.progress.store'), ['topic_key' => 'ari', 'phase' => 'post', 'score' => 15, 'total' => 15])
        ->assertConflict();

    expect(StudentProgress::query()
        ->where('session_id', (string) $this->student->id)
        ->where('topic_key', 'ari')
        ->where('phase', 'post')
        ->value('score'))->toBe(3);
});
