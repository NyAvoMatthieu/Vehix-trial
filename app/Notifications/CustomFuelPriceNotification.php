<?php

namespace App\Notifications;

use App\Models\Ravitaillement;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class CustomFuelPriceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ravitaillement;
    protected $user;
    protected $officialPrice;

    public function __construct(Ravitaillement $ravitaillement, User $user, $officialPrice)
    {
        $this->ravitaillement = $ravitaillement;
        $this->user = $user;
        $this->officialPrice = $officialPrice;
    }

    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        $difference = $this->ravitaillement->price_per_liter - $this->officialPrice;
        $percentDiff = (($difference / $this->officialPrice) * 100);
        
        return (new MailMessage)
            ->subject('⚠️ Prix carburant personnalisé détecté - Vehix')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Un ravitaillement avec un prix personnalisé a été enregistré.')
            ->line('**Détails du ravitaillement :**')
            ->line('- Utilisateur : ' . $this->user->name . ' (' . $this->user->email . ')')
            ->line('- Véhicule : ' . $this->ravitaillement->vehicule->make . ' ' . $this->ravitaillement->vehicule->model)
            ->line('- Plaque : ' . $this->ravitaillement->vehicule->license_plate)
            ->line('- Type carburant : ' . $this->ravitaillement->fuel_type)
            ->line('- Station : ' . $this->ravitaillement->station_service)
            ->line('- Date : ' . $this->ravitaillement->ravitaillement_date->format('d/m/Y'))
            ->line('')
            ->line('**Comparaison des prix :**')
            ->line('- Prix officiel : ' . number_format($this->officialPrice, 2) . ' Ar/L')
            ->line('- Prix payé : ' . number_format($this->ravitaillement->price_per_liter, 2) . ' Ar/L')
            ->line('- Différence : ' . number_format($difference, 2) . ' Ar/L (' . number_format($percentDiff, 1) . '%)')
            ->line('')
            ->line('- Litres : ' . $this->ravitaillement->liters_purchased . ' L')
            ->line('- Montant total : ' . number_format($this->ravitaillement->amount_paid, 2) . ' Ar')
            ->line('')
            ->line('Cela peut indiquer une variation de prix régionale ou une erreur de saisie.')
            ->action('Voir le ravitaillement', url('/ravitaillements/' . $this->ravitaillement->id))
            ->line('Vous pouvez vérifier et ajuster les prix officiels si nécessaire.')
            ->salutation('L\'équipe Vehix');
    }

    public function toArray($notifiable)
    {
        $difference = $this->ravitaillement->price_per_liter - $this->officialPrice;
        $percentDiff = (($difference / $this->officialPrice) * 100);
        
        return [
            'type' => 'custom_fuel_price',
            'title' => '⚠️ Prix carburant personnalisé',
            'message' => "{$this->user->name} a saisi un prix différent du tarif officiel pour {$this->ravitaillement->fuel_type}",
            'ravitaillement_id' => $this->ravitaillement->id,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'vehicule_name' => $this->ravitaillement->vehicule->make . ' ' . $this->ravitaillement->vehicule->model,
            'fuel_type' => $this->ravitaillement->fuel_type,
            'station' => $this->ravitaillement->station_service,
            'official_price' => $this->officialPrice,
            'custom_price' => $this->ravitaillement->price_per_liter,
            'difference' => $difference,
            'percent_difference' => round($percentDiff, 1),
            'liters' => $this->ravitaillement->liters_purchased,
            'total_amount' => $this->ravitaillement->amount_paid,
            'action_url' => url('/admin/fuel-prices'),
            'action_text' => 'Gérer les prix',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}