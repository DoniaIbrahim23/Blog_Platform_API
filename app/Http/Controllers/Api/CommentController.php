<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;

class CommentController extends Controller
{
    /** POST /api/posts/{id}/comments */
    public function store(Request $request, $postId)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);

        $post = Post::find($postId);
        if (! $post) {
            return response()->json(['message' => 'Post not found.'], 404);
        }

        $comment = Comment::create([
            'comment'  => $request->input('comment'),
            'user_id'  => $request->user()->id,
            'blog_id'  => $post->id, // ✅ لسه بنستخدم blog_id في الداتا بيز
        ]);

        return response()->json([
            'message' => 'Comment added successfully.',
            'data'    => $comment->load('user')
        ], 201);
    }

    /** GET /api/posts/{id}/comments */
    public function index($postId)
    {
        $post = Post::find($postId);
        if (! $post) {
            return response()->json(['message' => 'Post not found.'], 404);
        }

        $comments = $post->comments()->with('user')->orderBy('created_at','desc')->get();

        return response()->json($comments);
    }
}
