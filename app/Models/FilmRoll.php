<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FilmRoll extends Model
{
    use HasFactory;

    public const MODES = ['standard', 'live', 'surprise', 'approval', 'private'];

    public const MEMBER_ROLES = ['owner', 'admin', 'contributor', 'viewer'];

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'cover_image',
        'invite_code',
        'invite_token',
        'max_photos',
        'current_photos',
        'roll_type',
        'status',
        'camera_preset_id',
        'starts_at',
        'ends_at',
        'reveal_at',
        'archived_at',
        'expires_at',
    ];

    protected $casts = [
        'max_photos' => 'integer',
        'current_photos' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'reveal_at' => 'datetime',
        'archived_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cameraPreset(): BelongsTo
    {
        return $this->belongsTo(CameraPreset::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'film_roll_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(FilmRollMember::class);
    }

    public function roleFor(?User $user): ?string
    {
        if ($user === null) {
            return null;
        }

        return $this->memberships()->where('user_id', $user->id)->value('role');
    }

    public function isMember(?User $user): bool
    {
        return $this->roleFor($user) !== null;
    }

    public function isOwnerOrAdmin(?User $user): bool
    {
        return in_array($this->roleFor($user), ['owner', 'admin'], true);
    }

    public function canContribute(?User $user): bool
    {
        return in_array($this->roleFor($user), ['owner', 'admin', 'contributor'], true);
    }

    public function isRevealed(): bool
    {
        return $this->roll_type !== 'surprise'
            || $this->reveal_at === null
            || $this->reveal_at->isPast();
    }
}
