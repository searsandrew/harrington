<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('belongs to a user', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    expect($post->user->is($user))->toBeTrue();
});

it('sanitizes attributes via mutators', function () {
    $user = User::factory()->create();

    $post = Post::create([
        'user_id' => $user->id,
        'title' => "  <b>Title</b> ",
        'subtitle' => " <script>1</script> Sub ",
        'content' => " <div> Content </div> ",
    ]);

    expect($post->title)->toBe('Title')
        ->and($post->subtitle)->toBe('Sub')
        ->and($post->content)->toBe('Content');
});
