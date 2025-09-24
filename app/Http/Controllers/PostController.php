<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Post::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $post = new Post($validated);
        $post->user_id = Auth::id();
        $post->save();

        return back();
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $post->update($validated);

        return back();
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);
        $post->delete();

        return back();
    }
}
