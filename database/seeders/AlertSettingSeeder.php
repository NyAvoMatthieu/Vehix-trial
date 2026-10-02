<?php

namespace Database\Seeders;

use App\Enums\AlertType;
use App\Models\AlertSetting;
use Illuminate\Database\Seeder;

class AlertSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (AlertType::cases() as $type) {
            AlertSetting::updateOrCreate(
                ['type' => $type->value],
                ['seuil_jours' => 30, 'actif' => true]
            );
        }
    }
}
