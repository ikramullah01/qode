<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Seeder;
use App\Models\BlogPost;
use App\Models\User;

class BlogPostsTableSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::first()?->id ?? User::factory()->create()->id;

        $totalPosts = 2;
        $chunkSize = 5;

        for ($i = 0; $i < $totalPosts; $i += $chunkSize) {

            // Generate posts in memory
            $posts = BlogPost::factory()->count($chunkSize)->make([
                'author_id' => $userId,
            ]);

            // Convert to array for batch insert
            $insertData = $posts->map(function ($p) {
                $arr = $p->toArray();
                $arr['published_at'] = optional($p->published_at)->format('Y-m-d H:i:s');
                return $arr;
            })->toArray();

            // Insert chunk into DB
            BlogPost::insert($insertData);

            // Fetch newly inserted posts safely
            $newPosts = BlogPost::latest('id')->take(count($posts))->get();

            foreach ($newPosts as $post) {
                Redis::set("blog_post:{$post->id}", json_encode($post));
                Redis::sadd('blog_post_index', $post->id);
            }

            echo "Seeded and cached " . ($i + $chunkSize) . " posts\n";
        }

        function loopIndex($collection, $item)
        {
            foreach ($collection as $index => $i) {
                if ($i === $item) return $index;
            }
            return 0;
        }
    }
}
