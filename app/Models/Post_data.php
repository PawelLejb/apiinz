<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post_data extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'data',
        'dataName',
        'Posts_idPost',
        'created_at',
        'updated_at'

    ];
}
