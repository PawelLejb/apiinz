<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity_date extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'start_date',
        'end_date',
        'Activities_idActivities',
        'periodicityDatesId',
         'periodicity'

    ];
}
