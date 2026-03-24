<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchemaExercise extends Model
{
    protected $fillable = [
        'schema_id',
        'exercise_id',
        'order',
        'comfort_level'
    ];

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
    public function schema()
    {
        return $this->belongsTo(Schema::class);
    }
}
