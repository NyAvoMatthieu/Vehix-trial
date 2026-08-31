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
        Schema::create('assurances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicule_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Informations administratives
            $table->string('company')->nullable();
            $table->string('assureur')->nullable();
            $table->string('agence')->nullable();
            $table->string('policy_number')->nullable();
            $table->date('date_delivrance')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

           // Cotisation principale
            $table->decimal('cotisation', 10, 2)->default(0)->nullable();
            
            // Cotisations détaillées
            $table->decimal('premium', 10, 2)->default(0)->nullable(); // ancien nom probablement
            $table->decimal('prime_cp', 10, 2)->default(0)->nullable(); // Corps propre
            $table->decimal('prime_de', 10, 2)->default(0)->nullable(); // Dommages équipements
            $table->decimal('prime_ca', 10, 2)->default(0)->nullable(); // Couverture accident
            $table->decimal('prime_div', 10, 2)->default(0)->nullable(); // Divers
            $table->decimal('deductible', 10, 2)->default(0)->nullable(); // franchise
            $table->text('coverage_details')->nullable();
            $table->decimal('prime_total', 10, 2)->default(0)->nullable();

            // Informations de signature
            $table->string('lieu_signature')->nullable();
            $table->date('date_signature')->nullable();
            $table->string('agent_nom')->nullable();
            $table->text('notes_signature')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assurances');
    }
};
