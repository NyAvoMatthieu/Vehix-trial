<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alert_settings', function (Blueprint $table) {
            // seuil_jours existe déjà (partagé par tous les types) et sert de seuil "approche" en jours pour la maintenance.
            $table->unsignedInteger('seuil_km')->nullable()->after('seuil_jours');
            $table->unsignedInteger('retard_jours')->nullable()->after('seuil_km');
            $table->unsignedInteger('retard_km')->nullable()->after('retard_jours');
        });
    }

    public function down(): void
    {
        Schema::table('alert_settings', function (Blueprint $table) {
            $table->dropColumn(['seuil_km', 'retard_jours', 'retard_km']);
        });
    }
};
