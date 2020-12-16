<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group_role extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'roleName',
        'created_at',
        'updated_at'

    ];
}
