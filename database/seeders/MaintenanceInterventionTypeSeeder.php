<?php

namespace Database\Seeders;

use App\Models\MaintenanceInterventionType;
use Illuminate\Database\Seeder;

class MaintenanceInterventionTypeSeeder extends Seeder
{
    /**
     * ⚠️ Catalogue PROVISOIRE, basé sur les 2 groupes décrits dans le récap
     * ("tous les 3 mois ou 6000 km" et "tous les 1 an ou 20000 km").
     * À corriger avec les libellés et seuils exacts des 2 images du carnet
     * d'entretien d'origine avant de lancer ce seeder en réel.
     */
    public function run(): void
    {
        $interventions = [
            // Groupe court : 3 mois / 6000 km
            ['nom' => 'Vidange moteur', 'intervalle_jours' => 90, 'intervalle_km' => 6000],
            ['nom' => 'Remplacement filtre à huile', 'intervalle_jours' => 90, 'intervalle_km' => 6000],
            ['nom' => 'Contrôle niveaux (refroidissement, frein, direction)', 'intervalle_jours' => 90, 'intervalle_km' => 6000],
            ['nom' => 'Contrôle pression et usure des pneus', 'intervalle_jours' => 90, 'intervalle_km' => 6000],

            // Groupe long : 1 an / 20000 km
            ['nom' => 'Remplacement filtre à air', 'intervalle_jours' => 365, 'intervalle_km' => 20000],
            ['nom' => 'Remplacement filtre à carburant', 'intervalle_jours' => 365, 'intervalle_km' => 20000],
            ['nom' => 'Remplacement plaquettes de frein', 'intervalle_jours' => 365, 'intervalle_km' => 20000],
            ['nom' => 'Remplacement bougies', 'intervalle_jours' => 365, 'intervalle_km' => 20000],
            ['nom' => 'Contrôle courroie de distribution', 'intervalle_jours' => 365, 'intervalle_km' => 20000],
        ];

        foreach ($interventions as $intervention) {
            MaintenanceInterventionType::updateOrCreate(
                ['nom' => $intervention['nom']],
                $intervention + ['actif' => true]
            );
        }
    }
}
