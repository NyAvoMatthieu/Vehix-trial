<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Ravitaillement;
use App\Observers\RavitaillementObserver;
use App\Services\VehiculeConsumptionService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(VehiculeConsumptionService::class, function ($app) {
            return new VehiculeConsumptionService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Schema::defaultStringLength(191);
        Ravitaillement::observe(RavitaillementObserver::class);
        // Partagez les chemins PWA avec toutes les pages Inertia
        Inertia::share([
            'pwa_paths' => [
                'sw' => asset('sw.js'), // Chemin absolu vers le service worker
                'manifest' => asset('manifest.json'), // Chemin absolu vers le manifeste
                'offline_page' => asset('offline.html'), // Chemin absolu vers la page hors ligne
            ],
            'auth' => function () {
                return [
                    'user' => function () {
                        $user = Auth::user();

                        if ($user) {
                            return [
                                'id' => $user->id,
                                'name' => $user->name,
                                'email' => $user->email,
                                'role' => $user->role, // Assurez-vous que votre modèle User a un attribut/cast pour le rôle
                                'profile_photo_url' => $user->profile_photo_url,
                                // Ajoutez d'autres attributs nécessaires
                            ];
                        }

                        return null;
                    } // Removed immediate invocation — keep as a closure for lazy evaluation
                ];
            }
        ]);
    }
}
