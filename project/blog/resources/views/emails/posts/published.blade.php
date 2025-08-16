@component('mail::message')
# Your post has been published

Title: {{ $post->title }}

Excerpt: {{ $post->excerpt }}

@component('mail::button', ['url' => url("/#/blogs/{$post->id}")])
View Post
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
