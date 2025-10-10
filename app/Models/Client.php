<?php

namespace App\Models;

use App\Enums\ClientType;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'client_type',
        'phone',
        'email',
        'site',
        'state_id',
        'region_id',
        'province_id',
        'city_id',
        'place',
        'zip_code',
        'address',
        'civic',
        'note',
    ];

    protected $casts = [
        'client_type' => ClientType::class
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function region(){
        return $this->belongsTo(Region::class);
    }

    public function province(){
        return $this->belongsTo(Province::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }

    public function contacts(){
        return $this->hasMany(Contact::class);
    }
}
