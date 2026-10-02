<?php

use App\Models\ActivityLog;
use App\Models\User;

it('flags only the existing Google sign-up accounts that never chose a password', function () {
    $googleStudent = User::factory()->create(['google_id' => 'google-before-logs']);

    $googleTeacher = User::factory()->teacher()->create(['google_id' => 'google-teacher']);
    ActivityLog::record('registration', 'New Teacher Registered (Google)', 'signed up via Google', user: $googleTeacher);

    $linkedStudent = User::factory()->create(['google_id' => 'google-linked-later']);
    ActivityLog::record('registration', 'New Student Registered', 'signed up', user: $linkedStudent);

    $admin = User::factory()->admin()->create(['google_id' => 'google-admin']);
    $formStudent = User::factory()->create();

    $migration = require database_path('migrations/2026_10_02_031524_add_password_automatically_set_to_users_table.php');
    $migration->down();
    $migration->up();

    expect($googleStudent->fresh()->hasOwnPassword())->toBeFalse()
        ->and($googleTeacher->fresh()->hasOwnPassword())->toBeFalse()
        ->and($linkedStudent->fresh()->hasOwnPassword())->toBeTrue()
        ->and($admin->fresh()->hasOwnPassword())->toBeTrue()
        ->and($formStudent->fresh()->hasOwnPassword())->toBeTrue();
});
