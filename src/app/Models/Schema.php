<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schema extends Model
{
    protected $fillable = [
        'name',
        'share_token',
        'user_id',
    ];
    public function exercises()
    {
        return $this->hasMany(SchemaExercise::class);
    }
}
