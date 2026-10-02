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
        Schema::table('trajets', function (Blueprint $table) {
            // Distingue le mode de saisie utilisé (Mode 1 manuel vs Mode 2 géolocalisation)
            $table->enum('mode_saisie', ['manuel', 'geolocalisation'])
                ->default('manuel')
                ->after('kilometrage_mode');

            // Coordonnées GPS du départ et de l'arrivée (Mode 2 uniquement)
            $table->decimal('depart_latitude', 10, 7)->nullable()->after('departure');
            $table->decimal('depart_longitude', 10, 7)->nullable()->after('depart_latitude');
            $table->decimal('arrivee_latitude', 10, 7)->nullable()->after('destination');
            $table->decimal('arrivee_longitude', 10, 7)->nullable()->after('arrivee_latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trajets', function (Blueprint $table) {
            $table->dropColumn([
                'mode_saisie',
                'depart_latitude',
                'depart_longitude',
                'arrivee_latitude',
                'arrivee_longitude',
            ]);
        });
    }
};
