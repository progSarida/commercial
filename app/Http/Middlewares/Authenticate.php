<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Closure;
use Illuminate\Support\Facades\Session;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        // 1. Controlla se il cookie di sessione è presente nella richiesta.
        // Se non c'è il cookie, la sessione è persa/inesistente.
        if (!$request->hasSession() || !$request->session()->has(config('session.cookie'))) {
            return $this->unauthenticated($request, $guards);
        }

        // 2. 🛑 CONTROLLO ESPLICITO NELLO STORAGE (DB/Redis) 🛑
        
        $sessionId = $request->session()->getId();

        // Legge i dati della sessione dallo storage (DB, Redis, ecc.) tramite il Session Handler.
        // Se read() ritorna una stringa vuota, i dati non esistono o sono scaduti.
        if (! Session::getHandler()->read($sessionId)) {
            
            // Invalida la sessione sul server e rigenera il token CSRF (pulizia)
            $request->session()->invalidate(); 
            $request->session()->regenerateToken();
            
            // Reindirizza l'utente alla schermata di login.
            return $this->unauthenticated($request, $guards);
        }
        
        // ----------------------------------------------------
        
        // Se la sessione è valida nello storage, procedi con l'autenticazione standard.
        $this->authenticate($request, $guards);

        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Imposta qui la rotta di reindirizzamento al login (es. 'login' o '/admin/login')
        return $request->expectsJson() ? null : route('login'); 
    }
}