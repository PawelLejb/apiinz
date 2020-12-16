<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'title',
        'Groups_idGroup',
        'created_at',
        'updated_at',
        'author',
        'post',
        'authorId'

    ];
}
