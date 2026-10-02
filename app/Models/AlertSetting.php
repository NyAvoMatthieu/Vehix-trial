<?php

namespace App\Models;

use App\Enums\AlertType;
use Illuminate\Database\Eloquent\Model;

class AlertSetting extends Model
{
    protected $fillable = [
        'type',
        'seuil_jours',
        'periodicite_jours',
        'actif',
        'updated_by',
        'seuil_km',
        'retard_jours',
        'retard_km',
    ];

    protected $casts = [
        'seuil_jours' => 'integer',
        'periodicite_jours' => 'integer',
        'actif' => 'boolean',
        'seuil_km' => 'integer',
        'retard_jours' => 'integer',
        'retard_km' => 'integer',
    ];

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Retourne le seuil (en jours) configuré pour un type d'échéance.
     * Renvoie 30 par défaut si le réglage n'existe pas ou est désactivé.
     */
    public static function seuilPour(AlertType|string $type): int
    {
        $value = $type instanceof AlertType ? $type->value : $type;

        return static::where('type', $value)
            ->where('actif', true)
            ->value('seuil_jours') ?? 30;
    }

    /**
     * Retourne la périodicité (en jours) configurée pour un type d'échéance,
     * ou null si elle n'a pas encore été renseignée par l'admin.
     */
    public static function periodicitePour(AlertType|string $type): ?int
    {
        $value = $type instanceof AlertType ? $type->value : $type;

        return static::where('type', $value)
            ->where('actif', true)
            ->value('periodicite_jours');
    }

    public function seuilKmPour(AlertType $type): ?int
    {
        return self::where('type', $type)->value('seuil_km');
    }

    public function retardJoursPour(AlertType $type): ?int
    {
        return self::where('type', $type)->value('retard_jours');
    }

    public function retardKmPour(AlertType $type): ?int
    {
        return self::where('type', $type)->value('retard_km');
    }

}
