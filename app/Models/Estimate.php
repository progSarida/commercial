<?php

namespace App\Models;

use App\Enums\ContactType;
use App\Enums\EstimateState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Estimate extends Model
{
    protected $fillable = [
        'client_id',
        'contact_type',
        'contact_id',
        'date',
        'request_user_id',
        'done',
        'done_user_id',
        'path',
        'estimate_state',
        'state_user_id',
    ];

    protected $casts = [
        'contact_type' => ContactType::class,
        'estimate_state' => EstimateState::class
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function userRequest()
    {
        return $this->belongsTo(User::class, 'request_user_id');
    }

    public function userDone()
    {
        return $this->belongsTo(User::class, 'done_user_id');
    }

    public function userState()
    {
        return $this->belongsTo(User::class, 'state_user_id');
    }

    public function getFormattedClientServices(): array
    {
        $services = $this->client->clientServices()
            ->whereNotNull('service_state')
            ->with('serviceType')
            ->get()
            ->map(function ($service) {
                return $service->serviceType->name . ' - ' . ($service->note ?? 'No note');
            })
            ->toArray();

        return [
            'count' => count($services),
            'label' => count($services) > 0 ? count($services) . ' servizi registrati' : 'Nessun servizio',
            'tooltip' => !empty($services) ? implode("\n", $services) : 'Nessun servizio',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($estimate) {
            //
        });

        static::created(function ($estimate) {
            //
        });

        static::updating(function ($estimate) {
            if($estimate->estimate_state === null && $estimate->path !== null){             // primo caricamento file
                $estimate->estimate_state = EstimateState::PROPOSED;                        // metto stato a 'Proposto'
            }
            else if($estimate->path === null){                                              // cancello file
                $estimate->estimate_state = null;                                           // metto stato a null
            }
            if ($estimate->isDirty('done')) {                                               // se modifico 'done'
                $estimate->done_user_id = Auth::id();                                       // aggiorno l'id dello user che ha modificato 'done' per ultimo
            }
            if ($estimate->isDirty('estimate_state')) {                                     // se modifico 'estimate_state'
                $estimate->state_user_id = Auth::id();                                      // aggiorno l'id dello user che ha modificato 'estimate_state' per ultimo
            }
        });

        static::saved(function ($estimate) {
            //
        });

        static::deleting(function ($estimate) {
            //
        });

        static::deleted(function ($estimate) {
            //
        });
    }
}
