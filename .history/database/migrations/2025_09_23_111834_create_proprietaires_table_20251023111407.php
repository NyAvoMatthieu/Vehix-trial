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
        Schema::create('proprietaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['personnel', 'entreprise']);

            // Informations personnelles (pour type = personnel)
            $table->string('nom')->nullable();
            $table->string('prenom')->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('lieu_naissance')->nullable();
            $table->enum('sexe', ['masculin', 'feminin'])->nullable();
            $table->string('nationalite')->nullable();
            $table->string('numero_piece_identite')->nullable();
            $table->date('date_delivrance_piece')->nullable();
            $table->string('situation_familiale')->nullable();

            // Informations entreprise (pour type = entreprise)
            $table->string('raison_sociale')->nullable();
            $table->string('nom_commercial')->nullable();
            $table->string('forme_juridique')->nullable();
            $table->string('nif')->nullable(); // Numéro d'identification fiscale
            $table->string('statistique')->nullable();
            $table->string('rcs')->nullable(); // Registre du commerce
            $table->date('date_creation')->nullable();
            $table->string('secteur_activite')->nullable();

            // Coordonnées communes
            $table->text('adresse_complete')->nullable();
            $table->string('commune')->nullable();
            $table->string('fokontany')->nullable();
            $table->string('profession')->nullable(); // Pour personnel
            $table->string('telephone_mobile')->nullable();
            $table->string('telephone_fixe')->nullable();
            $table->string('email')->nullable();
            $table->string('site_web')->nullable(); // Pour entreprise

            // Représentant légal (pour entreprise)
            $table->string('representant_nom')->nullable();
            $table->string('representant_prenom')->nullable();
            $table->string('representant_fonction')->nullable();
            $table->string('representant_telephone')->nullable();
            $table->string('representant_email')->nullable();
            $table->string('representant_numero_piece')->nullable();
            $table->date('representant_date_delivrance')->nullable();
            $table->string('representant_lieu_delivrance')->nullable();

            // Contact administratif (pour entreprise)
            $table->string('responsable_flotte')->nullable();
            $table->string('responsable_telephone')->nullable();
            $table->string('responsable_email')->nullable();

            // Permis de conduire (pour personnel)
            $table->string('numero_permis')->nullable();
            $table->string('categorie_permis')->nullable();
            $table->date('date_delivrance_permis')->nullable();

            // Autorisations (pour entreprise)
            $table->string('autorisation_transport')->nullable();
            $table->date('autorisation_date_delivrance')->nullable();
            $table->date('autorisation_validite')->nullable();
            $table->string('autorisation_type')->nullable();

            // Observations
            $table->text('observations')->nullable();

            $table->timestamps();

            // Index
            $table->index('user_id');
            $table->index('type');
            $table->index('nif');
            $table->index('rcs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proprietaires');
    }
};
