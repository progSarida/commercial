<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrefecturalDecreeStreet extends Model
{
    protected $fillable = [
        'prefectural_decree_id',
        'city_id', 
        'name', 
        'note'
    ];

    public function prefecturalDecree(): BelongsTo
    {
        return $this->belongsTo(PrefecturalDecree::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
