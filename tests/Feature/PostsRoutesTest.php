<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects /posts to /posts/published for authenticated users and protects guests', function () {
    // Guest redirected to login
    $guestResp = $this->get(route('posts.index'));
    $guestResp->assertRedirect(route('login'));

    // Authenticated user redirected to published tab
    $user = User::factory()->create();
    $this->actingAs($user);
    $resp = $this->get(route('posts.index'));
    $resp->assertRedirect(route('posts.published'));
});

it('serves posts tab pages for authenticated users', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('posts.published'))->assertOk();
    $this->get(route('posts.scheduled'))->assertOk();
    $this->get(route('posts.drafts'))->assertOk();
});
