<?php

use App\Models\Section;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('lets a teacher change their password with the correct current password', function () {
    $teacher = User::factory()->teacher()->create([
        'approval_status' => 'approved',
        'password' => Hash::make('old-password'),
    ]);

    $response = $this->actingAs($teacher)->postJson(route('teacher.account.password'), [
        'current_password' => 'old-password',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ]);

    $response->assertOk();
    $teacher->refresh();
    expect(Hash::check('new-password-123', $teacher->password))->toBeTrue();
    expect(Hash::check('old-password', $teacher->password))->toBeFalse();
});

it('lets a student change their password with the correct current password', function () {
    $section = Section::factory()->create();
    $student = User::factory()->create([
        'role' => 'student',
        'approval_status' => 'approved',
        'section_id' => $section->id,
        'password' => Hash::make('old-password'),
    ]);

    $response = $this->actingAs($student)->postJson(route('student.account.password'), [
        'current_password' => 'old-password',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ]);

    $response->assertOk();
    $student->refresh();
    expect(Hash::check('new-password-123', $student->password))->toBeTrue();
});

it('rejects a password change with the wrong current password', function () {
    $teacher = User::factory()->teacher()->create([
        'approval_status' => 'approved',
        'password' => Hash::make('old-password'),
    ]);

    $response = $this->actingAs($teacher)->postJson(route('teacher.account.password'), [
        'current_password' => 'totally-wrong',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('current_password');
    $teacher->refresh();
    expect(Hash::check('old-password', $teacher->password))->toBeTrue();
});

it('rejects a password change when the confirmation does not match', function () {
    $teacher = User::factory()->teacher()->create([
        'approval_status' => 'approved',
        'password' => Hash::make('old-password'),
    ]);

    $response = $this->actingAs($teacher)->postJson(route('teacher.account.password'), [
        'current_password' => 'old-password',
        'password' => 'new-password-123',
        'password_confirmation' => 'does-not-match',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('password');
});

it('rejects a password shorter than 8 characters', function () {
    $teacher = User::factory()->teacher()->create([
        'approval_status' => 'approved',
        'password' => Hash::make('old-password'),
    ]);

    $response = $this->actingAs($teacher)->postJson(route('teacher.account.password'), [
        'current_password' => 'old-password',
        'password' => 'short',
        'password_confirmation' => 'short',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('password');
});

it('lets an admin change their password with the correct current password', function () {
    $admin = User::factory()->admin()->create(['password' => Hash::make('old-password')]);

    $this->actingAs($admin)->postJson(route('admin.account.password'), [
        'current_password' => 'old-password',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ])->assertOk();

    expect(Hash::check('new-password-123', $admin->refresh()->password))->toBeTrue();
});

it('rejects an admin password change with the wrong current password', function () {
    $admin = User::factory()->admin()->create(['password' => Hash::make('old-password')]);

    $this->actingAs($admin)->postJson(route('admin.account.password'), [
        'current_password' => 'totally-wrong',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ])->assertUnprocessable()->assertJsonValidationErrors('current_password');

    expect(Hash::check('old-password', $admin->refresh()->password))->toBeTrue();
});

it('keeps the admin password route admin-only', function () {
    $teacher = User::factory()->teacher()->create(['password' => Hash::make('old-password')]);

    // RoleMiddleware turns non-admins away to the homepage, like every admin route.
    $this->actingAs($teacher)->postJson(route('admin.account.password'), [
        'current_password' => 'old-password',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ])->assertRedirect(route('homepage'));

    expect(Hash::check('old-password', $teacher->refresh()->password))->toBeTrue();
});

it('lets a Google sign-up account set a password without the random one it was given', function () {
    $student = User::factory()->googleSignup()->create(['section_id' => Section::factory()->create()->id]);

    $this->actingAs($student)->postJson(route('student.account.password'), [
        'password' => 'my-own-password',
        'password_confirmation' => 'my-own-password',
    ])->assertOk()->assertJson(['message' => 'Password set. You can now sign in with your email too.']);

    $student->refresh();
    expect(Hash::check('my-own-password', $student->password))->toBeTrue()
        ->and($student->hasOwnPassword())->toBeTrue();

    $this->post(route('student.logout'));
    $this->post(route('student.login.submit'), [
        'email' => $student->email,
        'password' => 'my-own-password',
    ])->assertRedirect(route('student.dashboard'));
});

it('asks a Google sign-up account for its current password once it has set one', function () {
    $teacher = User::factory()->teacher()->googleSignup()->create();

    $this->actingAs($teacher)->postJson(route('teacher.account.password'), [
        'password' => 'first-password',
        'password_confirmation' => 'first-password',
    ])->assertOk();

    $this->actingAs($teacher->fresh())->postJson(route('teacher.account.password'), [
        'password' => 'second-password',
        'password_confirmation' => 'second-password',
    ])->assertUnprocessable()->assertJsonValidationErrors('current_password');

    expect(Hash::check('first-password', $teacher->fresh()->password))->toBeTrue();
});

it('still asks an account that only linked Google later for its current password', function () {
    $student = User::factory()->create([
        'section_id' => Section::factory()->create()->id,
        'google_id' => 'google-linked-later',
        'password' => Hash::make('old-password'),
    ]);

    $this->actingAs($student)->postJson(route('student.account.password'), [
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ])->assertUnprocessable()->assertJsonValidationErrors('current_password');

    expect(Hash::check('old-password', $student->fresh()->password))->toBeTrue();
});

it('blocks guests from changing a password', function () {
    $response = $this->postJson(route('teacher.account.password'), [
        'current_password' => 'whatever',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ]);

    $response->assertUnauthorized();
});
