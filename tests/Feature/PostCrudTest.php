<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post as httpPost;
use function Pest\Laravel\put as httpPut;
use function Pest\Laravel\delete as httpDelete;

uses(RefreshDatabase::class);

it('prevents guests from creating posts', function () {
    $response = httpPost(route('posts.store'), [
        'title' => 'Title',
        'subtitle' => 'Sub',
        'content' => 'Body',
    ]);

    $response->assertRedirect(route('login'));
});

it('allows authenticated users to create posts with sanitized fields', function () {
    $user = User::factory()->create();

    actingAs($user);

    $response = httpPost(route('posts.store'), [
        'title' => '  <b>Hello</b>  ',
        'subtitle' => " <script>alert('x')</script> World ",
        'content' => "<p>Hi</p><img src=x onerror=alert('x')>   ",
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    $post = Post::first();

    expect($post)->not()->toBeNull()
        ->and($post->user_id)->toBe($user->id)
        ->and($post->title)->toBe('Hello')
        ->and($post->subtitle)->toBe('World')
        ->and($post->content)->toBe('Hi');
});

it('enforces policy: only author or admin can update', function () {
    [$author, $other, $admin] = [
        User::factory()->create(),
        User::factory()->create(),
        User::factory()->create(),
    ];

    $post = Post::factory()->for($author)->create([
        'title' => 'Old',
        'subtitle' => 'OldSub',
        'content' => 'OldBody',
    ]);

    // Other user cannot update
    actingAs($other);
    httpPut(route('posts.update', $post), [
        'title' => 'New',
        'subtitle' => 'NewSub',
        'content' => 'NewBody',
    ])->assertForbidden();

    // Author can update
    actingAs($author);
    httpPut(route('posts.update', $post), [
        'title' => '  <i>New</i>  ',
        'subtitle' => ' NewSub ',
        'content' => ' <div>NewBody</div> ',
    ])->assertSessionHasNoErrors()->assertRedirect();

    expect($post->fresh()->title)->toBe('New')
        ->and($post->subtitle)->toBe('NewSub')
        ->and($post->content)->toBe('NewBody');

    // Admin can update
    setAdminsEnv([$admin->id]);
    actingAs($admin);
    httpPut(route('posts.update', $post), [
        'title' => 'Admin',
        'subtitle' => 'AdminSub',
        'content' => 'AdminBody',
    ])->assertSessionHasNoErrors()->assertRedirect();

    expect($post->fresh()->title)->toBe('Admin');
});

it('enforces policy: only author or admin can delete', function () {
    [$author, $other, $admin] = [
        User::factory()->create(),
        User::factory()->create(),
        User::factory()->create(),
    ];

    $post = Post::factory()->for($author)->create();

    // Other user cannot delete
    actingAs($other);
    httpDelete(route('posts.destroy', $post))->assertForbidden();

    // Author can delete
    actingAs($author);
    httpDelete(route('posts.destroy', $post))->assertRedirect();
    expect(Post::find($post->id))->toBeNull();

    // Admin can delete
    $post2 = Post::factory()->for($author)->create();
    setAdminsEnv([$admin->id]);
    actingAs($admin);
    httpDelete(route('posts.destroy', $post2))->assertRedirect();
    expect(Post::find($post2->id))->toBeNull();
});
