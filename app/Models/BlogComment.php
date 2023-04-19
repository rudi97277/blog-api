<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogComment extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public const LIKE = 1;
    public const DISLIKE = 0;

    public function like()
    {
        return $this->hasMany(CommentReaction::class)->where('reaction', BlogComment::LIKE);
    }

    public function dislike()
    {
        return $this->hasMany(CommentReaction::class)->where('reaction', BlogComment::DISLIKE);
    }
}
