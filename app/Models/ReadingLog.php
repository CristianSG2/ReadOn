<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingLog extends Model
{
    // Campos que se pueden asignar en masa
    protected $fillable = [
        'user_id',
        'volume_id',
        'title',
        'authors',
        'thumbnail_url',
        'status',
        'rating',
        'review',
    ];

    // Casts útiles
    protected $casts = [
        'rating' => 'integer',
    ];

    // Cada log pertenece a un usuario
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
