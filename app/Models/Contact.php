<?php

namespace App\Models;

use App\Enums\ContactType;
use App\Enums\OutcomeType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Contact extends Model
{
    protected $fillable = [
        'contact_type',
        'client_id',
        'date',
        'time',
        'note',
        'outcome_type',
        'services',
        'user_id',
    ];

    protected $casts = [
        'contact_type' => ContactType::class,
        'outcome_type' => OutcomeType::class,
        'services' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Query per 'Chiamate'
    public function scopeCalls($query)
    {
        return $query->where('contact_type', ContactType::CALL);
    }

    // Query per 'Visite'
    public function scopeVisits($query)
    {
        return $query->where('contact_type', ContactType::VISIT);
    }

    // Query per 'Scadenze clienti'
    public function scopeDeadlines($query)
    {
        return $query->where('contact_type', ContactType::DEADLINE);
    }

    protected static function booted()
    {
        static::creating(function ($contact) {
            //
        });

        static::created(function ($contact) {
            if($contact->outcome_type === OutcomeType::ESTIMATE){
                Estimate::create([
                    'client_id' => $contact->client_id,
                    'contact_type' => $contact->contact_type,
                    'contact_id' => $contact->id,
                    'date' => $contact->date,
                    'request_user_id' => Auth::user()->id,
                    'done' => 0,
                    'done_user_id' => null,
                    'path' => null,
                    'estimate_state' => null,
                    'state_user_id' => null,
                ]);
            }
        });

        static::updating(function ($contact) {
            //
        });

        static::updated(function ($contact) {
            if($contact->outcome_type !== OutcomeType::ESTIMATE){
                $estimate = Estimate::where('contact_id', $contact->id)->first();
                if($estimate){
                    $estimate->delete();
                }
            }
        });

        static::saved(function ($contact) {
            //
        });

        static::deleting(function ($contact) {
            //
        });

        static::deleted(function ($contact) {
            //
        });
    }
}
