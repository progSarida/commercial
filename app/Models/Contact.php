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
}
