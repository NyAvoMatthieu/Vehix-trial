<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Schema::defaultStringLength(191);
         // Partagez les chemins PWA avec toutes les pages Inertia
        Inertia::share([
            'pwa_paths' => [
                'sw' => asset('sw.js'), // Chemin absolu vers le service worker
                'manifest' => asset('manifest.json'), // Chemin absolu vers le manifeste
                'offline_page' => asset('offline.html'), // Chemin absolu vers la page hors ligne
            ],
            // Vous pouvez ajouter d'autres propriétés partagées ici si nécessaire
        ]);
    }
}
