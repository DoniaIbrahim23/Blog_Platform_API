<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'blogs';

    protected $with = [
        'comments',
        'user',
    ];

    protected $fillable = [
        'title',
        'content',
        'category',
        'user_id',
        'image_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

   public function comments()
    {
        return $this->hasMany(Comment::class, 'blog_id');
    }
}
