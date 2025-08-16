<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use Illuminate\Console\Command;
use App\Jobs\SendPostPublishedEmail;
use Illuminate\Support\Facades\Redis;

class PublishBlogPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:publish-blog-post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $posts = BlogPost::where('published_at', '<=', now())
            ->whereNotNull('published_at')
            ->get();

        foreach ($posts as $post) {
            // Push to Redis if not already there
            if (!Redis::exists("blog_post:{$post->id}")) {
                Redis::set("blog_post:{$post->id}", json_encode($post));
                Redis::sadd('blog_post_index', $post->id);

                SendPostPublishedEmail::dispatch($post);
            }
        }
    }
}
