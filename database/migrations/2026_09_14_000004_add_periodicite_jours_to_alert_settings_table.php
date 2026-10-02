<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('alert_settings', function (Blueprint $table) {
            // Nombre de jours entre 2 visites (utilisé pour estimer la prochaine
            // visite technique quand la date officielle n'est pas renseignée).
            // Laissé vide par défaut : à remplir depuis l'écran admin.
            $table->unsignedInteger('periodicite_jours')->nullable()->after('seuil_jours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alert_settings', function (Blueprint $table) {
            $table->dropColumn('periodicite_jours');
        });
    }
};
