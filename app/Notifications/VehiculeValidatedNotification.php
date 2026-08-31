<?php

namespace App\Notifications;

use App\Models\Vehicule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class VehiculeValidatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $vehicule;
    protected $status;
    protected $notes;

    public function __construct(Vehicule $vehicule, string $status, ?string $notes = null)
    {
        $this->vehicule = $vehicule;
        $this->status = $status;
        $this->notes = $notes;
    }

    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject('Statut de validation de votre véhicule - Vehix')
            ->greeting('Bonjour ' . $notifiable->name . ',');

        switch ($this->status) {
            case 'validate':
                $message->line('✅ Excellente nouvelle ! Votre véhicule a été validé avec succès.')
                    ->line('**Détails du véhicule :**')
                    ->line('- Marque : ' . $this->vehicule->make)
                    ->line('- Modèle : ' . $this->vehicule->model)
                    ->line('- Plaque : ' . $this->vehicule->license_plate)
                    ->line('Vous pouvez maintenant utiliser toutes les fonctionnalités de l\'application pour ce véhicule.')
                    ->action('Accéder à mon tableau de bord', url('/dashboard'))
                    ->success();
                break;

            case 'reject':
                $message->line('❌ Nous regrettons de vous informer que votre véhicule a été rejeté.')
                    ->line('**Détails du véhicule :**')
                    ->line('- Marque : ' . $this->vehicule->make)
                    ->line('- Modèle : ' . $this->vehicule->model)
                    ->line('- Plaque : ' . $this->vehicule->license_plate);
                
                if ($this->notes) {
                    $message->line('**Raison du rejet :**')
                        ->line($this->notes);
                }
                
                $message->line('Pour plus d\'informations, veuillez contacter notre support.')
                    ->action('Contacter le support', url('/support'))
                    ->error();
                break;

            case 'correct':
                $message->line('⚠️ Des corrections sont nécessaires pour votre véhicule.')
                    ->line('**Détails du véhicule :**')
                    ->line('- Marque : ' . $this->vehicule->make)
                    ->line('- Modèle : ' . $this->vehicule->model)
                    ->line('- Plaque : ' . $this->vehicule->license_plate);
                
                if ($this->notes) {
                    $message->line('**Corrections demandées :**')
                        ->line($this->notes);
                }
                
                $message->line('Veuillez corriger les informations de votre véhicule.')
                    ->action('Modifier mon véhicule', url('/vehicules/' . $this->vehicule->id . '/edit'))
                    ->level('warning');
                break;
        }

        return $message->line('Merci d\'utiliser Vehix !')
            ->salutation('Cordialement, L\'équipe Vehix');
    }

    public function toArray($notifiable)
    {
        $data = [
            'vehicule_id' => $this->vehicule->id,
            'vehicule_name' => $this->vehicule->make . ' ' . $this->vehicule->model,
            'license_plate' => $this->vehicule->license_plate,
            'status' => $this->status,
            'notes' => $this->notes,
            'type' => 'vehicle_validation',
        ];

        switch ($this->status) {
            case 'validate':
                $data['title'] = '✅ Véhicule validé';
                $data['message'] = "Votre véhicule {$this->vehicule->make} {$this->vehicule->model} a été validé avec succès !";
                $data['action_url'] = url('/dashboard');
                $data['action_text'] = 'Voir le tableau de bord';
                break;

            case 'reject':
                $data['title'] = '❌ Véhicule rejeté';
                $data['message'] = "Votre véhicule {$this->vehicule->make} {$this->vehicule->model} a été rejeté.";
                $data['action_url'] = url('/support');
                $data['action_text'] = 'Contacter le support';
                break;

            case 'correct':
                $data['title'] = '⚠️ Corrections nécessaires';
                $data['message'] = "Des corrections sont nécessaires pour votre véhicule {$this->vehicule->make} {$this->vehicule->model}.";
                $data['action_url'] = url('/vehicules/' . $this->vehicule->id . '/edit');
                $data['action_text'] = 'Corriger maintenant';
                break;
        }

        return $data;
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}