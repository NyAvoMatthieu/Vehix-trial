<?php

namespace App\Models;

use App\Enums\AlertType;
use App\Enums\MaintenanceEcheanceStatut;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehiculeMaintenanceIntervalle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicule_id',
        'intervention_type_id',
        'dernier_km_effectue',
        'derniere_date_effectuee',
        'intervalle_km_override',
        'intervalle_jours_override',
        'last_alert_level',
    ];

    protected $casts = [
        'dernier_km_effectue' => 'integer',
        'derniere_date_effectuee' => 'date',
        'intervalle_km_override' => 'integer',
        'intervalle_jours_override' => 'integer',
    ];

    // Relations
    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function type()
    {
        return $this->belongsTo(MaintenanceInterventionType::class, 'intervention_type_id');
    }

    // Intervalles effectifs (override du véhicule sinon valeur du catalogue)
    public function getIntervalleKmEffectifAttribute(): ?int
    {
        return $this->intervalle_km_override ?? $this->type?->intervalle_km;
    }

    public function getIntervalleJoursEffectifAttribute(): ?int
    {
        return $this->intervalle_jours_override ?? $this->type?->intervalle_jours;
    }

    // Prochaine échéance par critère
    public function getProchainKmAttribute(): ?int
    {
        if (!$this->dernier_km_effectue || !$this->intervalle_km_effectif) {
            return null;
        }

        return $this->dernier_km_effectue + $this->intervalle_km_effectif;
    }

    public function getProchaineDateAttribute(): ?Carbon
    {
        if (!$this->derniere_date_effectuee || !$this->intervalle_jours_effectif) {
            return null;
        }

        return $this->derniere_date_effectuee->copy()->addDays($this->intervalle_jours_effectif);
    }

    // Restants par critère (négatif = dépassé)
    public function getKmRestantsAttribute(): ?int
    {
        if ($this->prochain_km === null) {
            return null;
        }

        $kmActuel = $this->vehicule?->kilometrage_actuel ?? 0;

        return $this->prochain_km - $kmActuel;
    }

    public function getJoursRestantsAttribute(): ?int
    {
        if ($this->prochaine_date === null) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($this->prochaine_date, false);
    }

    /**
     * Statut à 4 niveaux : combinaison la plus critique des critères km et temps.
     * Un critère non configuré ou jamais effectué est simplement ignoré dans la combinaison.
     */
    public function getStatutAttribute(): MaintenanceEcheanceStatut
    {
        $setting = AlertSetting::where('type', AlertType::MAINTENANCE)->first();

        $statutKm = $this->statutPourKm($setting);
        $statutJours = $this->statutPourJours($setting);

        if ($statutKm === null && $statutJours === null) {
            return MaintenanceEcheanceStatut::A_JOUR;
        }

        return MaintenanceEcheanceStatut::pireDe(
            $statutKm ?? MaintenanceEcheanceStatut::A_JOUR,
            $statutJours ?? MaintenanceEcheanceStatut::A_JOUR
        );
    }

    protected function statutPourKm(?AlertSetting $setting): ?MaintenanceEcheanceStatut
    {
        if ($this->km_restants === null) {
            return null;
        }

        $seuilApproche = $setting?->seuil_km;
        $seuilRetard = $setting?->retard_km;

        if ($this->km_restants <= 0) {
            if ($seuilRetard !== null && $this->km_restants <= -$seuilRetard) {
                return MaintenanceEcheanceStatut::RETARD;
            }

            return MaintenanceEcheanceStatut::DUE;
        }

        if ($seuilApproche !== null && $this->km_restants <= $seuilApproche) {
            return MaintenanceEcheanceStatut::APPROCHE;
        }

        return MaintenanceEcheanceStatut::A_JOUR;
    }

    protected function statutPourJours(?AlertSetting $setting): ?MaintenanceEcheanceStatut
    {
        if ($this->jours_restants === null) {
            return null;
        }

        $seuilApproche = $setting?->seuil_jours;
        $seuilRetard = $setting?->retard_jours;

        if ($this->jours_restants <= 0) {
            if ($seuilRetard !== null && $this->jours_restants <= -$seuilRetard) {
                return MaintenanceEcheanceStatut::RETARD;
            }

            return MaintenanceEcheanceStatut::DUE;
        }

        if ($seuilApproche !== null && $this->jours_restants <= $seuilApproche) {
            return MaintenanceEcheanceStatut::APPROCHE;
        }

        return MaintenanceEcheanceStatut::A_JOUR;
    }
}
