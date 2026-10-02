<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicule_maintenance_intervalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('intervention_type_id')
                ->constrained('maintenance_intervention_types')
                ->cascadeOnDelete();

            // Dernier passage connu pour ce (véhicule, type)
            $table->unsignedInteger('dernier_km_effectue')->nullable();
            $table->date('derniere_date_effectuee')->nullable();

            // Overrides optionnels par véhicule (sinon on retombe sur le catalogue)
            $table->unsignedInteger('intervalle_km_override')->nullable();
            $table->unsignedInteger('intervalle_jours_override')->nullable();

            // Anti-doublon de notification, même principe que assurances/visite_techniques
            $table->string('last_alert_level')->nullable();

            $table->timestamps();

            $table->unique(['vehicule_id', 'intervention_type_id'], 'vehicule_type_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicule_maintenance_intervalles');
    }
};
