<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    protected $fillable = [
        "name",
        "youtube_url",
        "start_time",
        "end_time",
        "timer_duration"
    ];
}
