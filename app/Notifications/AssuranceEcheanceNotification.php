<?php

namespace App\Notifications;

use App\Models\Assurance;
use App\Services\EcheanceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssuranceEcheanceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Assurance $assurance,
        protected string $niveau // EcheanceService::NIVEAU_APPROCHE | NIVEAU_EXPIRE
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail($notifiable): MailMessage
    {
        $vehicule = $this->assurance->vehicule;
        $vehiculeLabel = $vehicule
            ? "{$vehicule->make} {$vehicule->model} ({$vehicule->license_plate})"
            : 'votre véhicule';

        return (new MailMessage)
            ->subject($this->titre())
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line($this->message())
            ->line('Véhicule : ' . $vehiculeLabel)
            ->line("Date d'échéance : " . $this->assurance->end_date->format('d/m/Y'))
            ->action("Voir l'assurance", url('/assurances/' . $this->assurance->id))
            ->salutation("L'équipe Vehix");
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'assurance_echeance',
            'niveau' => $this->niveau,
            'title' => $this->titre(),
            'message' => $this->message(),
            'assurance_id' => $this->assurance->id,
            'vehicule_id' => $this->assurance->vehicule_id,
            'end_date' => optional($this->assurance->end_date)->toDateString(),
            'action_url' => '/assurances/' . $this->assurance->id,
            'action_text' => "Voir l'assurance",
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }

    protected function titre(): string
    {
        return $this->niveau === EcheanceService::NIVEAU_EXPIRE
            ? '🔴 Assurance expirée'
            : '🟠 Assurance bientôt expirée';
    }

    protected function message(): string
    {
        return $this->niveau === EcheanceService::NIVEAU_EXPIRE
            ? "L'assurance de votre véhicule est arrivée à expiration."
            : "L'assurance de votre véhicule arrive bientôt à expiration.";
    }
}
