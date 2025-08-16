<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'excerpt',
        'description',
        'image',
        'keywords',
        'meta_title',
        'meta_description',
        'published_at',
        'author_id'
    ];

    protected $casts = [
        'keywords' => 'array',
        'published_at' => 'datetime',
    ];

    // Author relationship
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
