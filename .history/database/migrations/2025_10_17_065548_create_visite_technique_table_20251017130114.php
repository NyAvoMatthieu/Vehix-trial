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
        Schema::create('visite_technique', function (Blueprint $table) {
            $table->id();
           
            // Relations
            $table->foreignId('vehicule_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Véhicule (référence - déjà dans vehicules)
            $table->string('make')->nullable(); // Snapshot
            $table->string('license_plate')->nullable(); // Snapshot
            $table->integer('kilometrage')->nullable();
            
            // Visite
            $table->date('date_visite');
            $table->date('validite')->nullable();
            $table->string('centre')->nullable();
            $table->string('operateur')->nullable();
            $table->string('vta')->nullable();
            $table->string('verificateur')->nullable();
            $table->enum('type_visite', ['Initiale', 'Périodique', 'Contre-visite'])->default('Périodique');
            $table->enum('aptitude', ['APTE', 'INAPTE'])->default('APTE');
            
            // PV
            $table->string('numero_pv')->nullable();
            $table->date('date_pv')->nullable();
            
            // Reçu
            $table->string('numero_recu')->nullable();
            
            // Paiement
            $table->decimal('droit', 10, 2)->default(0);
            $table->decimal('pv_frais', 10, 2)->default(0);
            $table->decimal('carte_frais', 10, 2)->default(0);
            $table->decimal('tht', 10, 2)->default(0);
            $table->decimal('tva', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            
            // Carte violette
            $table->string('numero_carte_violette')->nullable();
            $table->date('date_carte_violette')->nullable();
            
            // Licence
            $table->string('numero_licence')->nullable();
            $table->date('date_licence')->nullable();
            
            // Autres
            $table->string('patente')->nullable();
            $table->string('ani')->nullable();
            $table->text('observations')->nullable();
            
            $table->timestamps();
            
            // Index
            $table->index(['vehicule_id', 'date_visite']);
            $table->index('aptitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visite_technique');
    }
};
