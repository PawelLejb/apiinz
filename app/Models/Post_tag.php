<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post_tag extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'name',
        'Posts_idPost',
        'created_at',
        'updated_at'

    ];
}
