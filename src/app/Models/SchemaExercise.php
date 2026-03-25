<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $schema_id
 * @property int $exercise_id
 * @property int $order
 * @property int|null $comfort_level
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @property-read \App\Models\Schema $schema
 * @property-read \App\Models\Exercise $exercise
 */
class SchemaExercise extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'schema_id',
        'exercise_id',
        'order',
        'comfort_level',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'schema_id' => 'integer',
            'exercise_id' => 'integer',
            'order' => 'integer',
            'comfort_level' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the exercise associated with the schema exercise.
     */
    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    /**
     * Get the schema associated with the schema exercise.
     */
    public function schema(): BelongsTo
    {
        return $this->belongsTo(Schema::class);
    }
}
