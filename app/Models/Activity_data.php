<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity_data extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'data',
        'data_name',
        'activity_notes_idActivity_notes'

    ];
}
