<?php

namespace App\Notifications;

use App\Models\VisiteTechnique;
use App\Services\EcheanceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VisiteTechniqueEcheanceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected VisiteTechnique $visite,
        protected string $niveau // EcheanceService::NIVEAU_APPROCHE | NIVEAU_EXPIRE
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail($notifiable): MailMessage
    {
        $vehicule = $this->visite->vehicule;
        $vehiculeLabel = $vehicule
            ? "{$vehicule->make} {$vehicule->model} ({$vehicule->license_plate})"
            : 'votre véhicule';

        return (new MailMessage)
            ->subject($this->titre())
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line($this->message())
            ->line('Véhicule : ' . $vehiculeLabel)
            // ->line('Date de validité : ' . $this->visite->validite->format('d/m/Y'))
            ->line('Date de validité : ' . $this->visite->validite)
            ->action('Voir la visite technique', url('/visite-techniques/' . $this->visite->id))
            ->salutation("L'équipe Vehix");
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'visite_technique_echeance',
            'niveau' => $this->niveau,
            'title' => $this->titre(),
            'message' => $this->message(),
            'visite_technique_id' => $this->visite->id,
            'vehicule_id' => $this->visite->vehicule_id,
            'validite' => optional($this->visite->validite)->toDateString(),
            'action_url' => '/visite-techniques/' . $this->visite->id,
            'action_text' => 'Voir la visite technique',
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }

    protected function titre(): string
    {
        return $this->niveau === EcheanceService::NIVEAU_EXPIRE
            ? '🔴 Visite technique expirée'
            : '🟠 Visite technique bientôt à échéance';
    }

    protected function message(): string
    {
        return $this->niveau === EcheanceService::NIVEAU_EXPIRE
            ? 'La visite technique de votre véhicule est arrivée à expiration.'
            : 'La visite technique de votre véhicule arrive bientôt à échéance.';
    }
}
