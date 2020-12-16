<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity_note extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'title',
        'note,',
        'Activities_idActivities'

    ];
}
