<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /** GET /api/posts */
    public function index(Request $request)
    {
        $perPage   = (int) $request->query('per_page', 10);
        $search    = $request->query('search');
        $category  = $request->query('category');
        $authorId  = $request->query('author_id');
        $dateFrom  = $request->query('date_from');
        $dateTo    = $request->query('date_to');
        $page      = (int) $request->query('page', 1);

        $cacheKey = "posts:page={$page}:per={$perPage}:search={$search}:cat={$category}:author={$authorId}:from={$dateFrom}:to={$dateTo}";

        $result = Cache::remember($cacheKey, now()->addMinutes(2), function () use (
            $perPage, $search, $category, $authorId, $dateFrom, $dateTo
        ) {
            $query = Post::with('user');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('category', 'like', "%{$search}%")
                      ->orWhereHas('user', function($q2) use ($search) {
                          $q2->where('name', 'like', "%{$search}%");
                      });
                });
            }

            if ($category) {
                $query->where('category', $category);
            }

            if ($authorId) {
                $query->where('user_id', $authorId);
            }

            if ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }

            if ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            }

            return $query->orderBy('created_at', 'desc')->paginate($perPage);
        });

        return response()->json($result);
    }

    /** GET /api/posts/{id} */
    public function show($id)
    {
        $post = Post::with(['user', 'comments.user'])->find($id);

        if (! $post) {
            return response()->json(['message' => 'Post not found.'], 404);
        }

        if ($post->image_path) {
            $post->image_url = Storage::disk('public')->url($post->image_path);
        } else {
            $post->image_url = null;
        }

        return response()->json($post);
    }

    /** POST /api/posts */
    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'required|string',
            'category' => ['required','string','max:100', Rule::in(['Technology','Lifestyle','Education'])],
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $user = $request->user();

        $attrs = $request->only(['title', 'content', 'category']);
        $attrs['user_id'] = $user->id;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'public');
            $attrs['image_path'] = $path;
        }

        $post = Post::create($attrs);

        Cache::flush();

        return response()->json([
            'message' => 'Post created successfully.',
            'data'    => $post->load('user')
        ], 201);
    }

    /** PUT /api/posts/{id} */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found.'], 404);
        }

        $user = $request->user();

        if (!($user->role === 'admin' || $user->id === $post->user_id)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $request->validate([
            'title'    => 'sometimes|required|string|max:255',
            'content'  => 'sometimes|required|string',
            'category' => ['sometimes','required','string','max:100', Rule::in(['Technology','Lifestyle','Education'])],
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $attrs = $request->only(['title', 'content', 'category']);

        if ($request->hasFile('image')) {
            if ($post->image_path && Storage::disk('public')->exists($post->image_path)) {
                Storage::disk('public')->delete($post->image_path);
            }
            $path = $request->file('image')->store('posts', 'public');
            $attrs['image_path'] = $path;
        }

        $post->update($attrs);

        Cache::flush();

        return response()->json([
            'message' => 'Post updated successfully.',
            'data'    => $post->fresh()->load('user')
        ]);
    }

    /** DELETE /api/posts/{id} */
    public function destroy(Request $request, $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found.'], 404);
        }

        $user = $request->user();

        if (!($user->role === 'admin' || $user->id === $post->user_id)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        if ($post->image_path && Storage::disk('public')->exists($post->image_path)) {
            Storage::disk('public')->delete($post->image_path);
        }

        $post->delete();

        Cache::flush();

        return response()->json(['message' => 'Post deleted successfully.']);
    }
}
