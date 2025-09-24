<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows authors to update and delete their own posts', function () {
    $author = User::factory()->create();
    $post = Post::factory()->for($author)->create();

    expect($author->can('update', $post))->toBeTrue();
    expect($author->can('delete', $post))->toBeTrue();
});

it('allows admins to update and delete any post', function () {
    $admin = User::factory()->create();
    setAdminsEnv([$admin->id]);
    $post = Post::factory()->for(User::factory())->create();

    expect($admin->can('update', $post))->toBeTrue();
    expect($admin->can('delete', $post))->toBeTrue();
});

it('prevents non-authors and non-admins from updating/deleting others\' posts', function () {
    // Ensure no admins are set for this test
    setAdminsEnv('');

    $user = User::factory()->create();
    $post = Post::factory()->for(User::factory())->create();

    expect($user->can('update', $post))->toBeFalse();
    expect($user->can('delete', $post))->toBeFalse();
});
