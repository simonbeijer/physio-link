<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $youtube_url
 * @property int $start_time
 * @property int $end_time
 * @property int $timer_duration
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Exercise extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'youtube_url',
        'start_time',
        'end_time',
        'timer_duration',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_time' => 'float',
            'end_time' => 'float',
            'timer_duration' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
