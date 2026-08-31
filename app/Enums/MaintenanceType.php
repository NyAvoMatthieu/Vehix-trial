<?php

namespace App\Enums;

enum MaintenanceType: string
{
    case PREVENTIVE = 'preventive';
    case CORRECTIVE = 'corrective';
    case DIAGNOSTIQUE = 'diagnostique';

    public function label(): string
    {
        return match($this) {
            self::PREVENTIVE => 'Préventive',
            self::CORRECTIVE => 'Corrective',
            self::DIAGNOSTIQUE => 'Diagnostique',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::PREVENTIVE => '🔧',
            self::CORRECTIVE => '⚠️',
            self::DIAGNOSTIQUE => '🔍',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::PREVENTIVE => 'Maintenance planifiée pour prévenir les pannes',
            self::CORRECTIVE => 'Réparation suite à une panne ou dysfonctionnement',
            self::DIAGNOSTIQUE => 'Contrôle et inspection du véhicule',
        };
    }
}