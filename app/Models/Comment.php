<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{

    use HasFactory;
    protected $fillable = [
        'id',
        'author',
        'comment',
        'Posts_idPost',
        'created_at',
        'updated_at',
        'authorId'

    ];
}
