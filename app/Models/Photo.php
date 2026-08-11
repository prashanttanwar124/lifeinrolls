<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'film_roll_id',
        'user_id',
        'camera_preset_id',
        'photo_url',
        'thumbnail_url',
        'caption',
        'status',
        'upload_status',
        'download_count',
    ];

    protected $casts = [
        'download_count' => 'integer',
    ];

    public function filmRoll(): BelongsTo
    {
        return $this->belongsTo(FilmRoll::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cameraPreset(): BelongsTo
    {
        return $this->belongsTo(CameraPreset::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(PhotoReport::class);
    }
}
