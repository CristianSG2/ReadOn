<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingLog extends Model
{
    use HasFactory;

    /**
     * Campos asignables.
     */
    protected $fillable = [
        'user_id',
        'volume_id',
        'title',
        'authors',
        'thumbnail_url',
        'isbn',
        'status',
        'rating',
        'review',
    ];

    /**
     * Casts útiles.
     */
    protected $casts = [
        'rating' => 'float',
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
     * Devuelve la mejor URL de portada disponible.
     * Prioridad: thumbnail de Google Books → Open Library por ISBN → placeholder local.
     */
    public function getCoverUrl(): string
    {
        if (!empty($this->thumbnail_url)) {
            return str_replace('http://', 'https://', $this->thumbnail_url);
        }

        if (!empty($this->isbn)) {
            return "https://covers.openlibrary.org/b/isbn/{$this->isbn}-L.jpg?default=false";
        }

        return asset('images/no-cover.svg');
    }

    /**
     * Cada log pertenece a un usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
