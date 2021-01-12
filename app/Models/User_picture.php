<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_picture extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'picUrl',
        'name',
        'Users_idUser'

    ];
}
