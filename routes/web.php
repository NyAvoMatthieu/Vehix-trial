<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use Inertia\Inertia;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\AssuranceController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\RavitaillementController;
use App\Http\Controllers\TrajetController;
use App\Http\Controllers\ProprietaireController;
use App\Http\Controllers\AdministrateurController;
use App\Http\Controllers\VisiteTechniqueController;
use App\Http\Controllers\FuelPriceController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\NotificationController;
//use App\Http\Controllers\MarqueController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\ConsumptionAnalysisController;
use App\Http\Controllers\Auth\ProprietairePasswordRecoveryController;
use App\Http\Controllers\PositionShareController;
use App\Http\Controllers\AlertSettingController;
use App\Http\Controllers\VehiculeMaintenanceController;
use App\Http\Controllers\Admin\MaintenanceInterventionTypeController;



// Accueil
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/cgu', function () {
    return Inertia::render('cgu');
})->name('cgu');



Route::middleware('guest')->group(function () {

    // Étape 1 : Formulaire de saisie de l'email
    Route::get('/password/recovery', [ProprietairePasswordRecoveryController::class, 'showEmailForm'])
        ->name('password.recovery.email');

    // Étape 2 : Vérification de l'email et génération des questions
    Route::post('/password/recovery/verify-email', [ProprietairePasswordRecoveryController::class, 'verifyEmail'])
        ->name('password.recovery.verify-email');

    // Étape 3 : Affichage des questions de vérification
    Route::get('/password/recovery/questions/{token}', [ProprietairePasswordRecoveryController::class, 'showQuestionsForm'])
        ->name('password.recovery.questions');

    // Étape 4 : Vérification des réponses
    Route::post('/password/recovery/verify-answers/{token}', [ProprietairePasswordRecoveryController::class, 'verifyAnswers'])
        ->name('password.recovery.verify-answers');

    // Étape 5 : Formulaire de réinitialisation du mot de passe
    Route::get('/password/recovery/reset/{token}', [ProprietairePasswordRecoveryController::class, 'showResetForm'])
        ->name('password.recovery.reset');

    // Étape 6 : Enregistrement du nouveau mot de passe
    Route::post('/password/recovery/reset/{token}', [ProprietairePasswordRecoveryController::class, 'resetPassword'])
        ->name('password.recovery.reset-password');
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'prevent.blocked',
])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/rapports', [RapportController::class, 'index'])->name('rapports.index');
    Route::get('/rapports/export', [RapportController::class, 'export'])->name('rapports.export');

    // Analyse de consommation d'un véhicule
    Route::get('/vehicules/{vehicule}/consumption', [ConsumptionAnalysisController::class, 'show'])
        ->name('vehicules.consumption');

    // API pour obtenir l'estimation de consommation (AJAX)
    Route::get('/api/vehicules/{vehicule}/consumption/estimate', [ConsumptionAnalysisController::class, 'estimate'])
        ->name('api.vehicules.consumption.estimate');

    // Forcer la mise à jour de la consommation
    Route::post('/vehicules/{vehicule}/consumption/update', [ConsumptionAnalysisController::class, 'update'])
        ->name('vehicules.consumption.update');

    // Gestion des véhicules (sélection, détail en attente)
    Route::get('/vehicules/selection', [VehiculeController::class, 'selection'])
        ->name('vehicules.selection');

    Route::post('/vehicules/{vehicule}/select', [VehiculeController::class, 'select'])
        ->name('vehicules.select');

    // Désélectionner un véhicule
    Route::post('/vehicules/deselect', [VehiculeController::class, 'deselect'])
        ->name('vehicules.deselect');

    Route::get('/vehicules/{vehicule}/pending', [VehiculeController::class, 'pendingDetail'])
        ->name('vehicules.pending-detail');

    // Vehicle CRUD routes
    Route::resource('vehicules', VehiculeController::class);

    // Propriétaires routes
    Route::resource('proprietaires', ProprietaireController::class);

    // Routes avec validation de véhicule
    Route::middleware(['vehicule.validation'])->group(function () {
        Route::resource('assurances', AssuranceController::class);
        Route::resource('maintenances', MaintenanceController::class);
        Route::resource('ravitaillements', RavitaillementController::class);
        Route::resource('trajets', TrajetController::class);
        Route::resource('visite-techniques', VisiteTechniqueController::class);

        // Dashboard vehicule
        Route::get('/vehicules-maintenance', [VehiculeMaintenanceController::class, 'index'])
            ->name('vehicules.maintenance.index');
        Route::get('/vehicules/{vehicule}/maintenance', [VehiculeMaintenanceController::class, 'show'])
            ->name('vehicules.maintenance.show');
    });

    // Routes de validation
    Route::middleware(['role:validator,administrateur'])
        ->prefix('validator')
        ->name('validator.')
        ->group(function () {
            Route::get('/pending', [VehiculeController::class, 'pending'])->name('pending');
            Route::post('/vehicules/{vehicule}/validate', [VehiculeController::class, 'validateVehicule'])
                ->name('vehicules.validate');
            Route::post('/maintenances/{maintenance}/validate', [MaintenanceController::class, 'validateMaintenance'])
                ->name('maintenances.validateMaintenance');
        });

    // Routes d'administration
    Route::middleware(['role:administrateur'])
        ->prefix('administrateur')
        ->name('administrateur.')
        ->group(function () {
            Route::get('/users', [AdministrateurController::class, 'users'])->name('users.index');
            Route::put('/users/{user}/role', [AdministrateurController::class, 'updateUserRole'])->name('users.role');
            Route::delete('/users/{user}', [AdministrateurController::class, 'deleteUser'])->name('users.delete');
            Route::post('/users/{user}/block', [AdministrateurController::class, 'blockUser'])->name('users.block');
            Route::post('/users/{user}/unblock', [AdministrateurController::class, 'unblockUser'])->name('users.unblock');
            Route::get('/alert-settings', [AlertSettingController::class, 'index']) // routes d'alertes
                ->name('alert-settings.index');
            Route::put('/alert-settings', [AlertSettingController::class, 'update'])
                ->name('alert-settings.update');

            // Maintenance
            Route::get('/maintenance-types', [MaintenanceInterventionTypeController::class, 'index'])
                ->name('maintenance-types.index');
            Route::post('/maintenance-types', [MaintenanceInterventionTypeController::class, 'store'])
                ->name('maintenance-types.store');
            Route::put('/maintenance-types/{maintenanceInterventionType}', [MaintenanceInterventionTypeController::class, 'update'])
                ->name('maintenance-types.update');
            Route::delete('/maintenance-types/{maintenanceInterventionType}', [MaintenanceInterventionTypeController::class, 'destroy'])
                ->name('maintenance-types.destroy');
        });


    // Fuel Prices Management
    Route::middleware(['auth', 'role:administrateur,validator'])->group(function () {
        Route::prefix('admin/fuel-prices')->name('admin.fuel-prices.')->group(function () {
            Route::get('/', [FuelPriceController::class, 'index'])->name('index');
            Route::get('/create', [FuelPriceController::class, 'create'])->name('create');
            Route::post('/', [FuelPriceController::class, 'store'])->name('store');
            Route::get('/{fuelPrice}/edit', [FuelPriceController::class, 'edit'])->name('edit');
            Route::put('/{fuelPrice}', [FuelPriceController::class, 'update'])->name('update');
            Route::delete('/{fuelPrice}', [FuelPriceController::class, 'destroy'])->name('destroy');
            Route::post('/{fuelPrice}/toggle-active', [FuelPriceController::class, 'toggleActive'])->name('toggle-active');
        });
        Route::prefix('administrateur/alert-settings')->name('admin.alert-settings.')->group(function () {
            Route::get('/', [AlertSettingController::class, 'index'])->name('index');
        });
    });

    // Routes des notifications
    Route::middleware(['auth'])->group(function () {
        Route::get('/notifications', [NotificationController::class, 'index'])
            ->name('notifications.index');
        Route::get('/api/notifications', [NotificationController::class, 'getNotifications'])
            ->name('api.notifications.get');
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
            ->name('notifications.read');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])
            ->name('notifications.mark-all-read');
        Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])
            ->name('notifications.destroy');
        Route::delete('/notifications', [NotificationController::class, 'deleteAll'])
            ->name('notifications.delete-all');
    });

    // Broadcasting auth
    Route::post('/broadcasting/auth', function (Illuminate\Http\Request $request) {
        return Broadcast::auth($request);
    })->middleware(['auth']);

    // Route de partage de position
    Route::post('/trajets/position-share', [PositionShareController::class, 'create'])
        ->name('position-share.create');

    /* Admin Marques routes
    Route::middleware(['auth', 'role:administrateur'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/marques', [MarqueController::class, 'index'])->name('marques.index');
        Route::get('/marques/create', [MarqueController::class, 'create'])->name('marques.create');
        Route::post('/marques', [MarqueController::class, 'storeAdmin'])->name('marques.store');
        Route::delete('/marques/{marque}', [MarqueController::class, 'destroy'])->name('marques.destroy');
    });*/
});

Route::middleware('throttle:60,1')->group(function () {
    Route::get('/partage-position/{token}', [PositionShareController::class, 'show'])
        ->name('position-share.show');
    Route::post('/api/position-share/{token}', [PositionShareController::class, 'update'])
        ->name('position-share.update');
    Route::get('/api/position-share/{token}', [PositionShareController::class, 'poll'])
        ->name('position-share.poll');
});
