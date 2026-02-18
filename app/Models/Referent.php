<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referent extends Model
{
    protected $fillable = [
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
}
