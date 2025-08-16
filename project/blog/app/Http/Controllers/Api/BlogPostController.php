<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SendPostPublishedEmail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class BlogPostController extends Controller
{

    public function index()
    {
        return BlogPost::with('author')->latest()->paginate(9);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'excerpt'          => 'required|string',
            'description'      => 'required|string',
            'image'            => 'nullable|image|max:2048',
            'keywords'         => 'nullable|array',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'published_at'     => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            $image      = $request->file('image');
            $imageName  = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images'), $imageName);
            $data['image'] = 'images/' . $imageName;
        }

        $data['author_id'] = $request->user()->id;

        $post = BlogPost::create($data);



        // If post is published, dispatch email job and also store in Redis
        if ($post->published_at && $post->published_at <= now()) {
            Redis::set("blog_post:{$post->id}", json_encode($post));

            // Update search index in Redis
            $this->updateRedisIndex($post);
            SendPostPublishedEmail::dispatch($post)->onQueue('emails');
        }

        return response()->json($post, 201);
    }

    public function show(BlogPost $blogPost)
    {
        return $blogPost->load('author', 'comments', 'comments.user');
    }

    public function update(Request $request, BlogPost $blogPost)
    {
        $data = $request->validate([
            'title'            => 'sometimes|string|max:255',
            'excerpt'          => 'sometimes|string',
            'description'      => 'sometimes|string',
            'image'            => 'sometimes|image|max:2048',
            'keywords'         => 'nullable|array',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'published_at'     => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($blogPost->image);
            $data['image'] = $request->file('image')->store('blog_images', 'public');
        }

        $blogPost->update($data);

        if ($blogPost->published_at && $blogPost->published_at <= now()) {
            SendPostPublishedEmail::dispatch($blogPost);
        }

        return response()->json($blogPost);
    }

    public function destroy(BlogPost $blogPost)
    {
        Storage::disk('public')->delete($blogPost->image);
        $blogPost->delete();
        // Remove from Redis
        Redis::del("blog_post:{$blogPost->id}");
        $this->removeFromRedisIndex($blogPost);
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function searchPost(Request $request)
    {
        $query = strtolower($request->input('q'));
        $ids = Redis::smembers('blog_post_index');
        rsort($ids);

        $results = [];
        foreach ($ids as $id) {
            $postData = Redis::get("blog_post:{$id}");
            if ($postData) {
                $post = json_decode($postData, true);
                // Check title, excerpt , description, or keywords
                $matches = false;
                // dd($post['title']);
                if (str_contains(strtolower($post['title']), $query) !== false) {
                    $matches = true;
                    // dd($post);
                } elseif (str_contains(strtolower($post['excerpt']), $query) !== false) {
                    $matches = true;
                } elseif (str_contains(strtolower($post['description']), $query) !== false) {
                    $matches = true;
                } elseif (!empty($post['keywords'])) {
                    foreach ($post['keywords'] as $keyword) {
                        if (str_contains(strtolower($keyword), $query) !== false) {
                            $matches = true;
                            break;
                        }
                    }
                }

                if ($matches) {
                    $results[] = $post;
                }
            }
        }

        return response()->json($results);
    }

    protected function updateRedisIndex(BlogPost $post)
    {
        $key = 'blog_post_index';
        Redis::sadd($key, $post->id); // Adds to set, no duplicates
    }

    protected function removeFromRedisIndex(BlogPost $post)
    {
        $key = 'blog_post_index';
        Redis::srem($key, $post->id); // Removes from set
    }
}
