<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Get paginated published posts for public blog
     */
    public function index(Request $request)
    {
        $query = Post::query();

        // If requesting admin view, allow all statuses, otherwise published only
        if ($request->boolean('all')) {
            $query->orderBy('created_at', 'desc');
        } else {
            $query->published()->orderBy('published_at', 'desc');
        }

        // Tag filter
        if ($tag = $request->query('tag')) {
            $query->whereJsonContains('tags', $tag);
        }

        // Search filter
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('meta_description', 'like', "%{$search}%");
            });
        }

        $perPage = min((int) $request->query('per_page', 9), 50);
        $posts = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $posts->items(),
            'current_page' => $posts->currentPage(),
            'last_page' => $posts->lastPage(),
            'total' => $posts->total(),
            'per_page' => $posts->perPage(),
        ]);
    }

    /**
     * Get a single post by slug
     */
    public function show(string $slug)
    {
        $post = Post::where('slug', $slug)
            ->orWhere('id', is_numeric($slug) ? (int) $slug : 0)
            ->first();

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found',
            ], 404);
        }

        // Fetch 3 related posts sharing similar tags or latest
        $related = Post::published()
            ->where('id', '!=', $post->id)
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $post,
            'related' => $related,
        ]);
    }

    /**
     * Get all unique tags
     */
    public function tags()
    {
        $posts = Post::published()->pluck('tags');
        $allTags = [];

        foreach ($posts as $tagList) {
            if (is_array($tagList)) {
                foreach ($tagList as $tag) {
                    $cleaned = trim($tag);
                    if ($cleaned !== '' && !in_array($cleaned, $allTags, true)) {
                        $allTags[] = $cleaned;
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => $allTags,
        ]);
    }

    /**
     * Create a post (Admin / API)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug',
            'content' => 'required|string',
            'meta_description' => 'nullable|string|max:500',
            'cover_image_url' => 'nullable|string|max:2048',
            'author' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'status' => 'required|in:draft,published',
            'source' => 'nullable|string|in:manual,ai,ai-edited',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            // Ensure unique slug
            $originalSlug = $validated['slug'];
            $count = 1;
            while (Post::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = "{$originalSlug}-" . $count++;
            }
        }

        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        $post = Post::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Post created successfully.',
            'data' => $post,
        ], 201);
    }

    /**
     * Update a post
     */
    public function update(Request $request, int $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Post not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:posts,slug,' . $post->id,
            'content' => 'sometimes|required|string',
            'meta_description' => 'nullable|string|max:500',
            'cover_image_url' => 'nullable|string|max:2048',
            'author' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'status' => 'sometimes|required|in:draft,published',
            'source' => 'nullable|string|in:manual,ai,ai-edited',
        ]);

        if (isset($validated['status']) && $validated['status'] === 'published' && !$post->published_at) {
            $validated['published_at'] = now();
        } elseif (isset($validated['status']) && $validated['status'] === 'draft') {
            $validated['published_at'] = null;
        }

        $post->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully.',
            'data' => $post,
        ]);
    }

    /**
     * Delete a post
     */
    public function destroy(int $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Post not found'], 404);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully.',
        ]);
    }
}
