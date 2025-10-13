<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'position',
        'mandatory'
    ];

    protected $casts = [
        //
    ];

    protected static function booted()
    {
        static::creating(function ($service) {
            //
        });

        static::created(function ($service) {
            $clients = Client::all();
            foreach ($clients as $client) {
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
