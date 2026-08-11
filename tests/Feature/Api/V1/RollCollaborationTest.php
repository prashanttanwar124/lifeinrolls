<?php

use App\Models\FilmRoll;
use App\Models\FilmRollMember;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function makeRoll(User $owner, string $mode = 'standard', array $attributes = []): FilmRoll
{
    $roll = FilmRoll::factory()->mode($mode)->create(array_merge(['user_id' => $owner->id], $attributes));

    FilmRollMember::create([
        'film_roll_id' => $roll->id,
        'user_id' => $owner->id,
        'role' => 'owner',
        'joined_at' => now(),
    ]);

    return $roll;
}

function addMember(FilmRoll $roll, User $user, string $role): FilmRollMember
{
    return FilmRollMember::create([
        'film_roll_id' => $roll->id,
        'user_id' => $user->id,
        'role' => $role,
        'joined_at' => now(),
    ]);
}

function actingAsApi(User $user)
{
    // Flush cached guard state so consecutive requests as different users work.
    app('auth')->forgetGuards();

    return test()->withHeader('Authorization', 'Bearer '.$user->createToken('t')->plainTextToken);
}

test('user can create a roll with mode and becomes owner', function () {
    $user = User::factory()->create();

    $response = actingAsApi($user)->postJson('/api/v1/rolls', [
        'name' => 'Wedding Roll',
        'description' => 'Our big day',
        'mode' => 'surprise',
        'reveal_at' => now()->addDays(2)->toIso8601String(),
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'Wedding Roll')
        ->assertJsonPath('data.mode', 'surprise')
        ->assertJsonPath('data.user_role', 'owner');

    expect($response->json('data.invite_code'))->not->toBeNull();
});

test('user can join a roll by code and by token', function () {
    $owner = User::factory()->create();
    $roll = makeRoll($owner);

    $byCode = User::factory()->create();
    actingAsApi($byCode)->postJson('/api/v1/rolls/join', ['invite_code' => $roll->invite_code])
        ->assertStatus(200)
        ->assertJsonPath('data.user_role', 'contributor');

    $byToken = User::factory()->create();
    actingAsApi($byToken)->postJson('/api/v1/rolls/join', ['invite_token' => $roll->invite_token])
        ->assertStatus(200)
        ->assertJsonPath('data.user_role', 'contributor');

    expect($roll->memberships()->count())->toBe(3);
});

test('non-member cannot view a private roll', function () {
    $owner = User::factory()->create();
    $roll = makeRoll($owner, 'private');

    $stranger = User::factory()->create();
    actingAsApi($stranger)->getJson("/api/v1/rolls/{$roll->id}")
        ->assertStatus(403)
        ->assertJsonPath('success', false);

    actingAsApi($stranger)->getJson("/api/v1/rolls/{$roll->id}/photos")
        ->assertStatus(403);

    actingAsApi($stranger)->getJson("/api/v1/rolls/{$roll->id}/members")
        ->assertStatus(403);
});

test('owner can view invite details, regenerate them, and manage members', function () {
    $owner = User::factory()->create();
    $roll = makeRoll($owner);
    $member = User::factory()->create();
    $membership = addMember($roll, $member, 'contributor');

    $invite = actingAsApi($owner)->getJson("/api/v1/rolls/{$roll->id}/invite");
    $invite->assertStatus(200)
        ->assertJsonStructure(['data' => ['invite_code', 'invite_token', 'invite_link']]);

    $oldCode = $roll->invite_code;
    actingAsApi($owner)->postJson("/api/v1/rolls/{$roll->id}/invite/regenerate")->assertStatus(200);
    expect($roll->refresh()->invite_code)->not->toBe($oldCode);

    // Change member role
    actingAsApi($owner)->patchJson("/api/v1/rolls/{$roll->id}/members/{$membership->id}", ['role' => 'viewer'])
        ->assertStatus(200)
        ->assertJsonPath('data.role', 'viewer');

    // Remove member
    actingAsApi($owner)->deleteJson("/api/v1/rolls/{$roll->id}/members/{$membership->id}")
        ->assertStatus(200);
    expect($roll->memberships()->count())->toBe(1);
});

test('contributor cannot invite or remove members', function () {
    $owner = User::factory()->create();
    $roll = makeRoll($owner);
    $contributor = User::factory()->create();
    addMember($roll, $contributor, 'contributor');
    $victim = addMember($roll, User::factory()->create(), 'viewer');

    actingAsApi($contributor)->getJson("/api/v1/rolls/{$roll->id}/invite")->assertStatus(403);
    actingAsApi($contributor)->deleteJson("/api/v1/rolls/{$roll->id}/members/{$victim->id}")->assertStatus(403);
});

test('contributor can upload a photo but viewer cannot', function () {
    Storage::fake('public');

    $owner = User::factory()->create();
    $roll = makeRoll($owner);
    $contributor = User::factory()->create();
    $viewer = User::factory()->create();
    addMember($roll, $contributor, 'contributor');
    addMember($roll, $viewer, 'viewer');

    actingAsApi($contributor)->postJson("/api/v1/rolls/{$roll->id}/photos", [
        'photo' => UploadedFile::fake()->image('shot.jpg'),
    ])->assertStatus(201)
        ->assertJsonPath('data.upload_status', 'ready');

    actingAsApi($viewer)->postJson("/api/v1/rolls/{$roll->id}/photos", [
        'photo' => UploadedFile::fake()->image('shot2.jpg'),
    ])->assertStatus(403);

    expect($roll->photos()->count())->toBe(1);
});

test('non-member cannot upload a photo', function () {
    Storage::fake('public');

    $owner = User::factory()->create();
    $roll = makeRoll($owner);
    $stranger = User::factory()->create();

    actingAsApi($stranger)->postJson("/api/v1/rolls/{$roll->id}/photos", [
        'photo' => UploadedFile::fake()->image('shot.jpg'),
    ])->assertStatus(403);
});

test('approval roll hides unapproved photos from other members until approved', function () {
    $owner = User::factory()->create();
    $roll = makeRoll($owner, 'approval');
    $uploader = User::factory()->create();
    $otherMember = User::factory()->create();
    addMember($roll, $uploader, 'contributor');
    addMember($roll, $otherMember, 'contributor');

    $photo = Photo::factory()->pending()->create([
        'film_roll_id' => $roll->id,
        'user_id' => $uploader->id,
    ]);

    // Hidden from other members, visible to uploader and owner
    expect(actingAsApi($otherMember)->getJson("/api/v1/rolls/{$roll->id}/photos")->json('data'))->toHaveCount(0);
    expect(actingAsApi($uploader)->getJson("/api/v1/rolls/{$roll->id}/photos")->json('data'))->toHaveCount(1);
    expect(actingAsApi($owner)->getJson("/api/v1/rolls/{$roll->id}/photos")->json('data'))->toHaveCount(1);

    // Contributor cannot approve; owner can
    actingAsApi($otherMember)->postJson("/api/v1/photos/{$photo->id}/approve")->assertStatus(403);
    actingAsApi($owner)->postJson("/api/v1/photos/{$photo->id}/approve")->assertStatus(200);

    expect(actingAsApi($otherMember)->getJson("/api/v1/rolls/{$roll->id}/photos")->json('data'))->toHaveCount(1);
});

test('surprise roll hides photos before reveal time except own uploads and owner/admin', function () {
    $owner = User::factory()->create();
    $roll = makeRoll($owner, 'surprise', ['reveal_at' => now()->addDay()]);
    $memberA = User::factory()->create();
    $memberB = User::factory()->create();
    addMember($roll, $memberA, 'contributor');
    addMember($roll, $memberB, 'contributor');

    Photo::factory()->create(['film_roll_id' => $roll->id, 'user_id' => $memberA->id]);

    // Before reveal: other members see nothing, uploader sees own, owner sees all
    expect(actingAsApi($memberB)->getJson("/api/v1/rolls/{$roll->id}/photos")->json('data'))->toHaveCount(0);
    expect(actingAsApi($memberA)->getJson("/api/v1/rolls/{$roll->id}/photos")->json('data'))->toHaveCount(1);
    expect(actingAsApi($owner)->getJson("/api/v1/rolls/{$roll->id}/photos")->json('data'))->toHaveCount(1);

    // After reveal everyone sees the photos
    $roll->update(['reveal_at' => now()->subMinute()]);
    expect(actingAsApi($memberB)->getJson("/api/v1/rolls/{$roll->id}/photos")->json('data'))->toHaveCount(1);
});

test('owner can update and archive roll, contributor cannot', function () {
    $owner = User::factory()->create();
    $roll = makeRoll($owner);
    $contributor = User::factory()->create();
    addMember($roll, $contributor, 'contributor');

    actingAsApi($contributor)->patchJson("/api/v1/rolls/{$roll->id}", ['name' => 'Hijacked'])
        ->assertStatus(403);

    actingAsApi($owner)->patchJson("/api/v1/rolls/{$roll->id}", ['name' => 'Renamed Roll', 'mode' => 'approval'])
        ->assertStatus(200)
        ->assertJsonPath('data.name', 'Renamed Roll')
        ->assertJsonPath('data.mode', 'approval');

    actingAsApi($contributor)->deleteJson("/api/v1/rolls/{$roll->id}")->assertStatus(403);
    actingAsApi($owner)->deleteJson("/api/v1/rolls/{$roll->id}")->assertStatus(200);

    expect($roll->refresh()->archived_at)->not->toBeNull();

    // Archived rolls disappear from the owner's list
    expect(actingAsApi($owner)->getJson('/api/v1/rolls')->json('data'))->toHaveCount(0);
});

test('uploader can delete own photo and update its upload status', function () {
    $owner = User::factory()->create();
    $roll = makeRoll($owner, 'standard', ['current_photos' => 1]);
    $uploader = User::factory()->create();
    $other = User::factory()->create();
    addMember($roll, $uploader, 'contributor');
    addMember($roll, $other, 'contributor');

    $photo = Photo::factory()->create(['film_roll_id' => $roll->id, 'user_id' => $uploader->id]);

    actingAsApi($other)->deleteJson("/api/v1/photos/{$photo->id}")->assertStatus(403);

    actingAsApi($uploader)->patchJson("/api/v1/photos/{$photo->id}/status", ['upload_status' => 'processing'])
        ->assertStatus(200)
        ->assertJsonPath('data.upload_status', 'processing');

    actingAsApi($uploader)->deleteJson("/api/v1/photos/{$photo->id}")->assertStatus(200);
    expect(Photo::find($photo->id))->toBeNull();
    expect($roll->refresh()->current_photos)->toBe(0);
});
