<?php

namespace App\Enums;

enum MaintenanceStatus: string
{
    case EN_ATTENTE = 'en_attente';
    case EN_COURS = 'en_cours';
    case VALIDEE = 'validee';

    public function label(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'En attente',
            self::EN_COURS => 'En cours',
            self::VALIDEE => 'Validée',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'yellow',
            self::EN_COURS => 'blue',
            self::VALIDEE => 'green',
        };
    }

    public function badge(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'bg-yellow-100 text-yellow-800',
            self::EN_COURS => 'bg-blue-100 text-blue-800',
            self::VALIDEE => 'bg-green-100 text-green-800',
        };
    }
}