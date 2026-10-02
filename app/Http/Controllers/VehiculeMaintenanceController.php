<?php

namespace App\Http\Controllers;

use App\Enums\MaintenanceEcheanceStatut;
use App\Models\Vehicule;
use App\Services\MaintenanceIntervalleService;
use Inertia\Inertia;

class VehiculeMaintenanceController extends Controller
{
    public function __construct(protected MaintenanceIntervalleService $service)
    {
    }

    /**
     * Vue d'ensemble flotte : un aperçu par véhicule (nombre d'alertes, pire statut).
     */
    public function index()
    {
        $vehicules = Vehicule::where('user_id', auth()->id())->get();

        $ordreSeverite = array_flip(array_map(
            fn (MaintenanceEcheanceStatut $s) => $s->value,
            MaintenanceEcheanceStatut::cases()
        ));

        $apercu = $vehicules->map(function (Vehicule $vehicule) use ($ordreSeverite) {
            $suivis = $this->service->checkVehicule($vehicule);

            $pireStatut = $suivis->isEmpty()
                ? null
                : $suivis->sortByDesc(fn ($s) => $ordreSeverite[$s->statut->value])->first()->statut;

            return [
                'vehicule' => $vehicule->only(['id', 'alias', 'make', 'license_plate']),
                'nb_alertes' => $suivis->filter(fn ($s) => $s->statut !== MaintenanceEcheanceStatut::A_JOUR)->count(),
                'pire_statut' => $pireStatut?->value,
                'pire_statut_label' => $pireStatut?->label(),
            ];
        });

        return Inertia::render('Vehicules/MaintenanceApercu', [
            'apercu' => $apercu,
        ]);
    }

    /**
     * Tableau de bord détaillé d'un véhicule : état de chaque type d'intervention.
     */
    public function show(Vehicule $vehicule)
    {
        // Retirer cet appel si aucune VehiculePolicy n'est définie dans le projet.
        $this->authorize('view', $vehicule);

        $suivis = $this->service->checkVehicule($vehicule)->sortBy(fn ($s) => $s->type->nom);

        return Inertia::render('Vehicules/Maintenance', [
            'vehicule' => $vehicule->only(['id', 'alias', 'make', 'license_plate']),
            'suivis' => $suivis->values()->map(fn ($s) => [
                'id' => $s->id,
                'type' => $s->type->nom,
                'dernier_km_effectue' => $s->dernier_km_effectue,
                'derniere_date_effectuee' => $s->derniere_date_effectuee,
                'prochain_km' => $s->prochain_km,
                'prochaine_date' => $s->prochaine_date,
                'km_restants' => $s->km_restants,
                'jours_restants' => $s->jours_restants,
                'statut' => $s->statut->value,
                'statut_label' => $s->statut->label(),
            ]),
        ]);

        // console
    }
}
