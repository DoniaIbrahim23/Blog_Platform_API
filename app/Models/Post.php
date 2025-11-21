<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    // اسم الجدول في قاعدة البيانات
    protected $table = 'blogs'; // ✅ غيرناه من blogs لـ posts

    // العلاقات اللي دايمًا تتحمل مع الموديل
    protected $with = [
        'comments',
        'user',
    ];

    // الأعمدة اللي مسموح بالـ mass assignment ليها
    protected $fillable = [
        'title',
        'content',
        'category',
        'user_id',
        'image_path',
    ];

    // علاقة الـ Post مع الـ User (كاتب البوست)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // علاقة الـ Post مع الـ Comments
   public function comments()
    {
        return $this->hasMany(Comment::class, 'blog_id'); // ✅ كده Laravel هيستخدم blog_id
    }
}
