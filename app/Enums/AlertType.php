<?php

namespace App\Enums;

enum AlertType: string
{
    case ASSURANCE = 'assurance';
    case VISITE_TECHNIQUE = 'visite_technique';
    case MAINTENANCE = 'maintenance';

    public function label(): string
    {
        return match ($this) {
            self::ASSURANCE => 'Assurance',
            self::VISITE_TECHNIQUE => 'Visite technique',
            self::MAINTENANCE => 'Maintenance',
        };
    }
}
