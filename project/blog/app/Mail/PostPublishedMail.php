<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use app\Models\BlogPost;

class PostPublishedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public BlogPost $post) {}

    public function build()
    {
        return $this->subject('Your blog post has been published!')
            ->markdown('emails.posts.published', [
                'post' => $this->post
            ]);
    }
}
