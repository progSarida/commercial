<?php

namespace App\Models;

use App\Enums\ContactType;
use App\Enums\EstimateState;
use Illuminate\Database\Eloquent\Model;

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

    protected static function booted()
    {
        static::creating(function ($estimate) {
            //
        });

        static::created(function ($estimate) {
            //
        });

        static::updating(function ($estimate) {
            if($estimate->path !== null && $estimate->estimate_state === null){
                $estimate->estimate_state = EstimateState::PROPOSED;
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
