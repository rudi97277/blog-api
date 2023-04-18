<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    public CONST TRENDING = 1;
    public CONST NORMAL = 0;

    protected $guarded = ['id'];

    public function comment()
    {
        return $this->hasMany(BlogComment::class);
    }
}
