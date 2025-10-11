<?php

namespace App\Models;

use App\Enums\ContactType;
use App\Enums\OutcomeType;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'contact_type',
        'client_id',
        'date',
        'time',
        'note',
        'outcome_type',
        'user_id',
    ];

    protected $casts = [
        'contact_type' => ContactType::class,
        'outcome_type' => OutcomeType::class
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
}
