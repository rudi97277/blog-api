<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentReaction extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public const LIKE = 1;
    public const DISLIKE = 0;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
