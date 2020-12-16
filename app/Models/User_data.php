<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_data extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'data',
        'dataName',
        'User_notes_idNotes_user'

    ];
}
