<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\BlogPost;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogPost>
 */
class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    public function definition(): array
    {
        return [
            'title'            => $this->faker->words(3, true),  
            'excerpt'          => $this->faker->sentence(6), 
            'description'      => $this->faker->paragraphs(2, true),
            'image'            => 'images/default.jpg',
            'keywords'         => json_encode($this->faker->words(3)),
            'meta_title'       => $this->faker->words(3, true),  
            'meta_description' => $this->faker->text(30),
            'published_at'     => now(),
        ];
    }
}
