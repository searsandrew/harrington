<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreatePostController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $post = Auth::user()->posts()->create($request->all());
        return redirect()->route('post.edit', $post);
    }
}
