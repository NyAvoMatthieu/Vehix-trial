<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewUserRegisteredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $newUser;

    public function __construct(User $newUser)
    {
        $this->newUser = $newUser;
    }

    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('👤 Nouvel utilisateur inscrit sur Vehix')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Un nouvel utilisateur vient de s\'inscrire sur la plateforme Vehix.')
            ->line('**Détails de l\'utilisateur :**')
            ->line('- Nom : ' . $this->newUser->name)
            ->line('- Email : ' . $this->newUser->email)
            ->line('- Rôle : ' . $this->newUser->role->value)
            ->line('- Date d\'inscription : ' . $this->newUser->created_at->format('d/m/Y à H:i'))
            ->line('- ID : #' . $this->newUser->id)
            ->line('')
            ->line('Vous pouvez consulter le profil de cet utilisateur et gérer ses permissions.')
            ->action('Voir l\'utilisateur', url('/admin/users/' . $this->newUser->id))
            ->line('Total d\'utilisateurs actifs : ' . User::count())
            ->salutation('L\'équipe Vehix');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'new_user',
            'title' => '👤 Nouvel utilisateur inscrit',
            'message' => "{$this->newUser->name} s'est inscrit sur Vehix",
            'user_id' => $this->newUser->id,
            'user_name' => $this->newUser->name,
            'user_email' => $this->newUser->email,
            'user_role' => $this->newUser->role->value,
            'registered_at' => $this->newUser->created_at->toISOString(),
            'action_url' => url('/admin/users'),
            'action_text' => 'Voir tous les utilisateurs',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}