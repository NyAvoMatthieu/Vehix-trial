<?php

namespace App\Enums;

enum MaintenanceEcheanceStatut: string
{
    case A_JOUR = 'a_jour';
    case APPROCHE = 'approche';
    case DUE = 'due';
    case RETARD = 'retard';

    public function label(): string
    {
        return match ($this) {
            self::A_JOUR => 'À jour',
            self::APPROCHE => 'Bientôt due',
            self::DUE => 'Due',
            self::RETARD => 'En retard',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::A_JOUR => 'bg-green-100 text-green-800 border border-green-200',
            self::APPROCHE => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
            self::DUE => 'bg-orange-100 text-orange-800 border border-orange-200',
            self::RETARD => 'bg-red-100 text-red-800 border border-red-200',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::A_JOUR => '✅',
            self::APPROCHE => '⚡',
            self::DUE => '⚠️',
            self::RETARD => '❌',
        };
    }

    /**
     * Combine deux statuts (critère km + critère temps) et retient le plus critique.
     * Ordre de sévérité croissante : A_JOUR < APPROCHE < DUE < RETARD.
     */
    public static function pireDe(self $a, self $b): self
    {
        $ordre = [self::A_JOUR, self::APPROCHE, self::DUE, self::RETARD];

        return array_search($a, $ordre, true) >= array_search($b, $ordre, true) ? $a : $b;
    }
}
