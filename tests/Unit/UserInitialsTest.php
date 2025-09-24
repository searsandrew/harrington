<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('computes initials from user name', function () {
    $u1 = User::factory()->create(['name' => 'Ada Lovelace']);
    $u2 = User::factory()->create(['name' => 'Plato']);
    $u3 = User::factory()->create(['name' => '  alan   turing  ']);

    expect($u1->initials())->toBe('AL')
        ->and($u2->initials())->toBe('P')
        ->and($u3->initials())->toBe('at');
});
