<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Symfony\Component\HttpFoundation\Response;

class User extends Authenticatable  implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'close_estimate',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(\Filament\Panel $panel): bool                            // gestione accessi ai panel
    {
        // if ($panel->getId() === 'admin') { return $this->is_admin; }                        // solo chi è admin può accedere al relativo panel
        if ($panel->getId() === 'admin') { return $this->hasRole('super_admin'); }          // solo chi è ha ruolo super_admin può accedere al panel amministrazione
        if ($panel->getId() === 'user') { return true; }                                    // tutti gli utenti possono accedere al panel operatore
        return false;
    }

        public function loginRedirect(): ?Response
    {
        $destinationPanelId = null;
        if ($this->hasRole('super_admin'))
            $destinationPanelId = 'admin';
        else
            $destinationPanelId = 'user';
        
        if (!$destinationPanelId)
            return abort(403, 'Accesso non autorizzato a nessun pannello.');
        
        return redirect()->to(Filament::getPanel($destinationPanelId)->getUrl());
    }
}
