<?php

namespace App\Notifications;

use App\Models\Vehicule;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewVehiculeAddedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $vehicule;
    protected $user;

    public function __construct(Vehicule $vehicule, User $user)
    {
        $this->vehicule = $vehicule;
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🚗 Nouveau véhicule ajouté - Action requise')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Un nouveau véhicule vient d\'être ajouté et nécessite votre validation.')
            ->line('**Détails du véhicule :**')
            ->line('- Marque : ' . $this->vehicule->make)
            ->line('- Modèle : ' . $this->vehicule->model)
            ->line('- Plaque : ' . $this->vehicule->license_plate)
            ->line('- Type : ' . $this->vehicule->vehicule_type)
            ->line('- VIN : ' . ($this->vehicule->vin ?? 'Non renseigné'))
            ->line('')
            ->line('**Propriétaire :**')
            ->line('- Nom : ' . $this->user->name)
            ->line('- Email : ' . $this->user->email)
            ->line('- ID : #' . $this->user->id)
            ->line('')
            ->line('Veuillez vérifier et valider ce véhicule dans les plus brefs délais.')
            ->action('Valider le véhicule', url('/vehicules/' . $this->vehicule->id))
            ->line('Merci de votre diligence !')
            ->salutation('L\'équipe Vehix');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'new_vehicle',
            'title' => '🚗 Nouveau véhicule à valider',
            'message' => "{$this->user->name} a ajouté un nouveau véhicule : {$this->vehicule->make} {$this->vehicule->model}",
            'vehicule_id' => $this->vehicule->id,
            'vehicule_name' => $this->vehicule->make . ' ' . $this->vehicule->model,
            'license_plate' => $this->vehicule->license_plate,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'action_url' => url('/vehicules/' . $this->vehicule->id),
            'action_text' => 'Voir le véhicule',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}