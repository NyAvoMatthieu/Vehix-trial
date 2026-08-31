<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
//use App\Http\Controllers\MarqueController;
use App\Http\Controllers\FuelPriceController;

// ============================================
// ROUTES API PUBLIQUES (sans authentification)
// ============================================

// Route pour récupérer les marques par type (ACCESSIBLE PUBLIQUEMENT)
// Cette route doit être accessible pour le formulaire de création de véhicule
//Route::get('/marques/{type}', [MarqueController::class, 'getByType'])->name('api.marques.byType');

// ============================================
// ROUTES API PROTÉGÉES (avec authentification)
// ============================================

Route::middleware('auth:sanctum')->group(function () {
    // Route utilisateur authentifié
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    /* Routes API pour les marques (nécessitent auth)
    Route::prefix('marques')->name('api.marques.')->group(function () {
        // Créer une nouvelle marque (utilisateur authentifié)
        Route::post('/', [MarqueController::class, 'store'])->name('store');
        
        // Activer/désactiver une marque (admin)
        Route::post('/{marque}/toggle-status', [MarqueController::class, 'toggleStatus'])->name('toggleStatus');
    });*/

    // API endpoint for getting current fuel prices
    Route::get('/fuel-prices/current', [FuelPriceController::class, 'getCurrentPrices']);
});