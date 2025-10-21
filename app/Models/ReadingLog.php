<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingLog extends Model
{
    /**
     * Campos asignables.
     */
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

    /**
     * Casts útiles.
     */
    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Lista de estados válidos.
     */
    public const STATUSES = ['wishlist', 'reading', 'read', 'dropped'];

    /**
     * Setter para normalizar el estado.
     * Si viene vacío o inválido → 'wishlist'.
     */
    public function setStatusAttribute($value): void
    {
        $v = is_string($value) ? strtolower(trim($value)) : '';
        $this->attributes['status'] = in_array($v, self::STATUSES, true) ? $v : 'wishlist';
    }

    /**
     * Devuelve el label traducido del estado.
     */
    public function getStatusLabelAttribute(): string
    {
        $map = [
            'wishlist' => 'Lista de deseos',
            'reading'  => 'Leyendo',
            'read'     => 'Leído',
            'dropped'  => 'Abandonado',
        ];

        return $map[$this->status] ?? ucfirst((string) $this->status);
    }

    /**
     * Cada log pertenece a un usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
