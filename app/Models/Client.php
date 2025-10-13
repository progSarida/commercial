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

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function clientServices()
    {
        return $this->hasMany(ClientService::class);
    }

    protected static function booted()
    {
        static::creating(function ($client) {
            //
        });

        static::created(function ($client) {
            $services = ServiceType::all();
            foreach ($services as $service) {
                ClientService::create([
                    'client_id' => $client->id,
                    'service_type_id' => $service->id,
                    'service_state' => null,
                    'referent' => null,
                    'phone' => null,
                    'email' => null,
                    'note' => null,
                ]);
            }
        });

        static::updating(function ($client) {
            //
        });

        static::saved(function ($client) {
            //
        });

        static::deleting(function ($client) {
            //
        });

        static::deleted(function ($client) {
            //
        });
    }
}
