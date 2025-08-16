<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogPost;
use App\Models\Comment;
use Illuminate\Support\Facades\Redis;

class CommentController extends Controller
{
    // Add a new comment
    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|min:3',
        ]);

        $post = BlogPost::findOrFail($postId);

        $comment = Comment::create([
            'blog_post_id' => $post->id,
            'user_id' => $request->user()->id,
            'content' => $request->input('content'),
        ]);

        // Increment comment count in Redis
        Redis::incr("blog_post:{$post->id}:comment_count");

        return response()->json($comment, 201);
    }

    // Get all comments for a post
    public function index($postId)
    {
        $post = BlogPost::findOrFail($postId);

        // Return all comments from database
        $comments = $post->comments()->with('user')->latest()->get()->map(function ($comment) {
            return [
                'id' => $comment->id,
                'user' => $comment->user->name,
                'content' => $comment->content,
                'created_at' => $comment->created_at->toDateTimeString(),
            ];
        });

        // Optionally, include comment count from Redis
        $count = Redis::get("blog_post:{$post->id}:comment_count") ?? count($comments);

        return response()->json([
            'count' => (int)$count,
            'comments' => $comments,
        ]);
    }

    // Delete a comment
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $postId = $comment->blog_post_id;

        $comment->delete();

        // Decrement comment count in Redis
        Redis::decr("blog_post:{$postId}:comment_count");

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
