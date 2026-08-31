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
            $table->date('date_delivrance');

            // Cotisations détaillées
            $table->decimal('prime_cp', 10, 2)->default(0); // Corps propre
            $table->decimal('prime_de', 10, 2)->default(0); /; // Couverture accident
            $table->decimal('prime_div', 10, 2)->default(0)->after('prime_ca'); // Divers
            $table->decimal('prime_total', 10, 2)->default(0)->after('prime_div');

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
