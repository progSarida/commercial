<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrefecturalDecree extends Model
{
    protected $fillable = [
        'province_id',
        'note',
        'attachment_path'
    ];

    // Svuotato i casts poiché non ci sono più JSON
    protected $casts = [];

    // Relazione nativa con la Provincia
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    /**
     * Relazione con i Comuni (Cities)
     */
    public function cities(): BelongsToMany
    {
        // Se hai seguito la convenzione dei nomi, basta questo:
        return $this->belongsToMany(City::class);
        
        // Se la tabella pivot ha un nome diverso, specificalo così:
        // return $this->belongsToMany(City::class, 'nome_tabella_pivot');
    }

    /**
     * Relazione con i Clienti (Clients)
     */
    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class);
    }

    /**
     * Ottieni tutte le strade associate al decreto
     */
    public function streets(): HasMany
    {
        return $this->hasMany(PrefecturalDecreeStreet::class);
    }

    protected static function booted()
    {
        static::creating(function ($service) {
            //
        });

        static::created(function ($service) {
            //
        });

        static::updating(function ($service) {
            //
        });

        static::saved(function ($decree) {
            //
        });

        static::deleting(function ($service) {
            //
        });

        static::deleted(function ($service) {
            //
        });
    }

    /**
     * Sincronizza i clienti "Comune" derivati dalle città associate al decreto.
     * Da chiamare DOPO che le relazioni many-to-many del form sono state salvate
     * (es. in afterCreate()/afterSave() delle pagine Filament).
     */
    public function syncAutomaticClients(): void
    {
        $cityIds = $this->cities()->pluck('cities.id')->toArray();

        $automaticClientIds = [];

        foreach ($cityIds as $cityId) {
            $client = \App\Models\Client::where('city_id', $cityId)
                ->where('client_type', \App\Enums\ClientType::CITY)
                ->first();

            if (! $client) {
                $city = \App\Models\City::find($cityId);
                $client = \App\Models\Client::create([
                    'name'        => $city->name,
                    'client_type' => \App\Enums\ClientType::CITY,
                    'state_id'    => 111,
                    'region_id'   => $city->province->region_id ?? null,
                    'province_id' => $city->province_id ?? $this->province_id,
                    'city_id'     => $cityId,
                    'note'        => "Creato automaticamente dal Decreto Prefettizio N. {$this->id}",
                ]);
            }

            $automaticClientIds[] = $client->id;
        }

        $this->clients()->sync($automaticClientIds);
    }
}
