<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment_data extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'data',
        'dataName',
        'Comments_idComment',
        'created_at',
        'updated_at'

    ];

}
