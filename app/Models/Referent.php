<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referent extends Model
{
    protected $fillable = [
        'client_id',
        'name',
        'title',
        'phone',
        'fax',
        'smart',
        'email',
        'note',
    ];

    public function contact()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Cerca un referent per email
     */
    public static function findByEmail(string $email): ?self
    {
        return self::where('email', $email)->first();
    }
}
