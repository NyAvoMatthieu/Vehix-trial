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
        Schema::create('maintenance_pieces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_id')->constrained()->onDelete('cascade');
            $table->string('reference')->nullable();

            // Informations de base
            $table->string('nom_piece');
            $table->string('reference_code')->nullable();
            $table->string('emplacement')->nullable();

            // Quantité et prix
            $table->integer('quantite')->default(1);
            $table->decimal('prix_unitaire', 10, 2)->default(0);
            $table->decimal('prix_total', 10, 2)->default(0);

            // Dates et utilisation
            $table->date('date_installation');
            $table->decimal('limite_utilisation', 10, 2)->default(0);
            $table->decimal('utilisation_actuelle', 10, 2)->default(0);
            $table->decimal('potentiel_restant', 10, 2)->default(0);
            $table->string('unite_mesure')->default('Tour'); // tour,km, heures, cycles, jours, mois, annees
            $table->date('prochaine_maintenance')->nullable();

            // Autres
            $table->text('observation')->nullable();
            $table->boolean('alerte_proche_limite')->default(false);

            $table->timestamps();

            $table->index(['maintenance_id', 'alerte_proche_limite']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_pieces');
    }
};
