<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note_tag extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'name',
        'Notes_idNote',
        'created_at',
        'updated_at'

    ];
}
