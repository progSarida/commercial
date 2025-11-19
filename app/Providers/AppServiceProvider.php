<?php

namespace App\Providers;

use App\Http\Middleware\CheckDbSession;
use App\Responses\SsoLogoutResponse;
use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;
use Filament\Http\Responses\Auth\LogoutResponse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LogoutResponse::class, SsoLogoutResponse::class);
        // Filament::serving(function () {
        //     foreach (Filament::getPanels() as $panel) {
        //         $panel->middleware([
        //             CheckDbSession::class, // Applicazione diretta
        //         ]);
        //     }
        // });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
