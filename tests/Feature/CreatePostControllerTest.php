<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects guests from create post route to login', function () {
    $this->get(route('post.create'))
        ->assertRedirect(route('login'));
});

it('creates a new empty post for the authenticated user and redirects to edit', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('post.create'));

    $response->assertRedirect();

    $post = Post::first();

    expect($post)->not()->toBeNull()
        ->and($post->user_id)->toBe($user->id)
        ->and($response->headers->get('Location'))
            ->toBe(route('post.edit', $post));
});
