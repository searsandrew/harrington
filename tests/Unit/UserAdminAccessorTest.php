<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('reports admin true when id present in CSV config list', function () {
    $user = User::factory()->create();

    // Configure as CSV string
    setAdminsEnv($user->id . ', ' . User::factory()->create()->id);

    expect($user->fresh()->is_admin)->toBeTrue();
});

it('reports admin true when id present in JSON array config', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    setAdminsEnv(json_encode([$other->id, $user->id]));

    expect($user->fresh()->is_admin)->toBeTrue();
});

it('reports admin false when config empty or missing id', function () {
    $user = User::factory()->create();

    setAdminsEnv('');
    expect($user->fresh()->is_admin)->toBeFalse();

    $notAdmin = User::factory()->create();
    setAdminsEnv([$notAdmin->id]);
    expect($user->fresh()->is_admin)->toBeFalse();
});
