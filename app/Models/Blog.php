<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    public const TRENDING = 1;
    public const NORMAL = 0;
    public const LIKE = 1;
    public const DISLIKE = 0;

    protected $guarded = ['id'];

    public function comment()
    {
        return $this->hasMany(BlogComment::class);
    }

    public function like()
    {
        return $this->hasMany(BlogReaction::class)->where('reaction', Blog::LIKE);
    }

    public function dislike()
    {
        return $this->hasMany(BlogReaction::class)->where('reaction', Blog::DISLIKE);
    }

    public function author_data()
    {
        return $this->belongsTo(User::class, 'author');
    }
}
