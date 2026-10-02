<?php

namespace App\Notifications;

use App\Models\VehiculeMaintenanceIntervalle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceEcheanceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public VehiculeMaintenanceIntervalle $suivi)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $vehicule = $this->suivi->vehicule;
        $type = $this->suivi->type;
        $statut = $this->suivi->statut;

        $mail = (new MailMessage)
            ->subject("Maintenance {$statut->label()} — {$vehicule->alias}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("L'intervention « {$type->nom} » sur le véhicule {$vehicule->alias} ({$vehicule->license_plate}) est {$statut->label()}.");

        if ($this->suivi->km_restants !== null) {
            $mail->line($this->suivi->km_restants >= 0
                ? "Kilométrage restant : {$this->suivi->km_restants} km"
                : 'Kilométrage dépassé de ' . abs($this->suivi->km_restants) . ' km');
        }

        if ($this->suivi->jours_restants !== null) {
            $mail->line($this->suivi->jours_restants >= 0
                ? "Jours restants : {$this->suivi->jours_restants} jour(s)"
                : 'Retard de ' . abs($this->suivi->jours_restants) . ' jour(s)');
        }

        return $mail->action('Voir le véhicule', route('vehicules.maintenance.show', $vehicule));
    }

    public function toArray(object $notifiable): array
    {
        $vehicule = $this->suivi->vehicule;
        $type = $this->suivi->type;
        $statut = $this->suivi->statut;

        return [
            'type' => 'maintenance_echeance',
            'niveau' => $statut->value,
            'title' => $statut->emoji() . ' Maintenance ' . mb_strtolower($statut->label()),
            'message' => "L'intervention « {$type->nom} » sur {$vehicule->alias} est {$statut->label()}.",
            'vehicule_id' => $vehicule->id,
            'vehicule_alias' => $vehicule->alias,
            'intervention_type_id' => $type->id,
            'intervention_nom' => $type->nom,
            'statut' => $statut->value,
            'statut_label' => $statut->label(),
            'km_restants' => $this->suivi->km_restants,
            'jours_restants' => $this->suivi->jours_restants,
            'action_url' => "/vehicules/{$vehicule->id}/maintenance",
            'action_text' => 'Voir le suivi maintenance',
        ];
    }
}
