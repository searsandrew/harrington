<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows empty state for published tab with link to create', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $resp = $this->get(route('posts.published'));
    $resp->assertOk();
    $resp->assertSee('New Post');
    $resp->assertSee('No published posts');
    $resp->assertSee('Create Post');
});

it('shows empty state for scheduled tab with link to create', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $resp = $this->get(route('posts.scheduled'));
    $resp->assertOk();
    $resp->assertSee('No scheduled posts');
    $resp->assertSee('Create Post');
});

it('shows empty state for drafts tab with link to create', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $resp = $this->get(route('posts.drafts'));
    $resp->assertOk();
    $resp->assertSee('No draft posts');
    $resp->assertSee('Create Post');
});
