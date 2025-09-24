<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

function makePost(User $user, array $overrides = []): Post {
    return Post::factory()->for($user)->create($overrides);
}

it('aggregates published, scheduled, and draft counts for a user in one call', function () {
    $user = User::factory()->create();

    // Drafts (is_published = 0, published_at = null)
    makePost($user, ['is_published' => false, 'published_at' => null]);
    makePost($user, ['is_published' => false, 'published_at' => null]);

    // Scheduled (is_published = 0, published_at not null)
    makePost($user, ['is_published' => false, 'published_at' => now()->addDay()]);

    // Published (is_published = 1)
    makePost($user, ['is_published' => true, 'published_at' => now()->subDay()]);
    makePost($user, ['is_published' => true, 'published_at' => now()]);

    $counts = $user->postTabCounts();

    expect($counts)
        ->toHaveKeys(['published', 'scheduled', 'drafts'])
        ->and($counts['published'])->toBe(2)
        ->and($counts['scheduled'])->toBe(1)
        ->and($counts['drafts'])->toBe(2);
});

it('returns same counts on repeated calls within request (static cache)', function () {
    $user = User::factory()->create();
    makePost($user, ['is_published' => true, 'published_at' => now()]);

    $first = $user->postTabCounts();

    // Create another published post after first call; cache keeps previous value in same request
    makePost($user, ['is_published' => true, 'published_at' => now()]);

    $second = $user->postTabCounts();

    expect($second)->toBe($first)
        ->and($second['published'])->toBe(1);
});
