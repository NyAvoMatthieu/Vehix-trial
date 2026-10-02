<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * NB: `maintenances` n'est volontairement pas touchée ici : cette table
     * n'a pas encore de colonne "prochaine échéance" exploitable. Elle sera
     * mise à jour avec ce même champ lors de la Partie 4 (maintenance
     * préventive), en même temps que les colonnes d'intervalle.
     */
    public function up(): void
    {
        Schema::table('assurances', function (Blueprint $table) {
            $table->string('last_alert_level')->nullable()->after('end_date');
        });

        Schema::table('visite_techniques', function (Blueprint $table) {
            $table->string('last_alert_level')->nullable()->after('validite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assurances', function (Blueprint $table) {
            $table->dropColumn('last_alert_level');
        });

        Schema::table('visite_techniques', function (Blueprint $table) {
            $table->dropColumn('last_alert_level');
        });
    }
};
