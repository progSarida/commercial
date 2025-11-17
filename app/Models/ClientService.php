<?php

namespace App\Models;

use App\Enums\ServiceState;
use Illuminate\Database\Eloquent\Model;

class ClientService extends Model
{
    protected $fillable = [
        'client_id',
        'service_type_id',
        'service_state',
        'referent',
        'phone',
        'email',
        'note',
    ];

    protected $casts = [
        'service_state' => ServiceState::class,
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }
}
