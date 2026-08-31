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
            $table->date('trajet_date');
            $table->string('departure');
            $table->string('destination');
            $table->decimal('distance', 10, 2)->nullable();
            $table->time('heure_depart');
            $table->time('heure_arrivee');
            $table->stringm('purpose', [
                'professionnel',
                'personnel',
                'livraison',
                'transport_marchandises',
                'service_client',
                'rendez_vous',
                'formation',
                'maintenance',
                'urgence',
                'autre'
            ]);
            $table->decimal('km_depart', 10, 2);
            $table->decimal('km_arrivee', 10, 2);
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
