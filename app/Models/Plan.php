<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'start_date',
        'end_date',
        'name',
        'Users_idUser',
        'created_at',
        'updated_at',

    ];
}
