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
        Schema::create('trajets', function (Blueprint $table) {
            $table->id();
           $table->foreignId('vehicule_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('departure')->nullable();
            $table->string('destination')->nullable();
            $table->decimal('distance', 10, 2)->nullable();
            $table->dateTime('heure_depart')->nullable();
            $table->dateTime('heure_arrivee')->nullable();
            $table->string('purpose')->nullable();
            
            // Mode de saisie: 'odometer' ou 'trajet'
            $table->enum('kilometrage_mode', ['odometer', 'trajet'])->default('odometer');
            
            // Champs pour le mode "trajet"
            $table->decimal('km_depart', 10, 2)->nullable()->default(0);
            $table->decimal('km_arrivee', 10, 2)->nullable();
            
            // Champs pour le mode "odometer" (compteur du véhicule)
            $table->decimal('odo_start', 10, 2)->nullable();
            $table->decimal('odo_end', 10, 2)->nullable();
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trajets');
    }
};
