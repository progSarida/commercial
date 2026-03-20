<?php

namespace App\Models;

use App\Enums\FeasibilityType;
use Illuminate\Database\Eloquent\Model;

class BiddingState extends Model
{
    protected $fillable = [
        'feasibility_type',
        'name',
        'description',
        'position',
    ];

    protected $casts = [
        'feasibility_type' => FeasibilityType::class,
    ];

    protected static function booted()
    {
        static::creating(function ($service) {
            //
        });

        static::created(function ($service) {
            //
        });

        static::updating(function ($service) {
            //
        });

        static::saved(function ($service) {
            //
        });

        static::deleting(function ($service) {
            //
        });

        static::deleted(function ($service) {
            //
        });
    }
}
