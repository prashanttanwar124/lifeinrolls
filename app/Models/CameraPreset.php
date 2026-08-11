<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CameraPreset extends Model
{
    use HasFactory;

    protected $fillable = [
        'preset_key',
        'name',
        'engine',
        'version',
        'lut',
        'lut_checksum',
        'lut_intensity',
        'grain',
        'bloom',
        'vignette',
        'softness',
        'date_stamp',
        'aspect_ratio',
        'is_premium',
        'minimum_app_version',
        'description',
        'sample_image_url',
        'is_active',
    ];

    protected $casts = [
        'version' => 'integer',
        'lut_intensity' => 'float',
        'grain' => 'float',
        'bloom' => 'float',
        'vignette' => 'float',
        'softness' => 'float',
        'date_stamp' => 'boolean',
        'is_premium' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function resolveLutChecksum(): string
    {
        if ($this->lut_checksum) {
            return $this->lut_checksum;
        }

        if ($this->lut && ! str_starts_with($this->lut, 'http')) {
            $filePath = storage_path('app/public/'.ltrim($this->lut, '/'));
            if (file_exists($filePath)) {
                return hash_file('sha256', $filePath);
            }
        }

        return '696217c8d564711b8a60b8e6857dbba2cad4e2db2a4f43271696593031477be5';
    }

    public function toMobileProfile(): array
    {
        $lutUrl = $this->lut
            ? (str_starts_with($this->lut, 'http') ? $this->lut : url('storage/'.ltrim($this->lut, '/')))
            : null;

        return [
            'id' => $this->preset_key,
            'name' => $this->name,
            'engine' => $this->engine,
            'version' => (int) $this->version,
            'lut_url' => $lutUrl,
            'lut_checksum' => $this->resolveLutChecksum(),
            'lut_intensity' => (float) $this->lut_intensity,
            'grain' => (float) $this->grain,
            'bloom' => (float) $this->bloom,
            'vignette' => (float) $this->vignette,
            'softness' => (float) $this->softness,
            'date_stamp' => (bool) $this->date_stamp,
            'aspect_ratio' => $this->aspect_ratio,
            'premium' => (bool) $this->is_premium,
            'minimum_app_version' => $this->minimum_app_version ?? '0.1.0',
        ];
    }

    public function filmRolls(): HasMany
    {
        return $this->hasMany(FilmRoll::class);
    }
}
