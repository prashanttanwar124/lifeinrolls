<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'currency',
        'interval',
        'max_rolls',
        'max_photos_per_roll',
        'allows_custom_presets',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'max_rolls' => 'integer',
        'max_photos_per_roll' => 'integer',
        'allows_custom_presets' => 'boolean',
        'is_active' => 'boolean',
    ];
}
