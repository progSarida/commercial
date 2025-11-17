<?php

namespace App\Models;

use App\Enums\TenderItemProcessingState;
use Illuminate\Database\Eloquent\Model;

class TenderNecessaryDoc extends Model
{
    protected $fillable = [
        'tender_id',
        'doc',
        'doc_processing_state',
    ];

    protected $casts = [
        'doc_processing_state' => TenderItemProcessingState::class,
    ];

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    protected static function booted()
    {
        static::creating(function ($doc) {
            //
        });

        static::created(function ($doc) {
            //
        });

        static::updating(function ($doc) {
            //
        });

        static::saved(function ($doc) {
            //
        });

        static::deleting(function ($doc) {
            //
        });

        static::deleted(function ($doc) {
            //
        });
    }
}
