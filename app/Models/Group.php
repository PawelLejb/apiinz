<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'name',
        'description',
        'picture'

    ];
    public function users(){
        return $this->belongsToMany(User::class, 'group_users', 'Groups_idGroup', 'Users_idUser')->withTimestamps();

    }
}
