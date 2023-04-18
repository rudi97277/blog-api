<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentReaction extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public CONST LIKE = 1;
    public CONST DISLIKE = 0;

}
