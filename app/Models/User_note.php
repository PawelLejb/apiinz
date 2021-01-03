<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_note extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'title',
        'note',
        'created_at',
        'updated_at',
        'Users_idUser'

    ];

}

