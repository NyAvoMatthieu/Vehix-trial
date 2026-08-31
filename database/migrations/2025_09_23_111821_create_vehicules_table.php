<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\VehiculeStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('proprietaire_id')->nullable()->constrained('proprietaires')->onDelete('set null');
            $table->string('make');
            $table->string('model');
            $table->enum('vehicule_type', ['voiture', 'moto', 'utilitaire', 'camion', 'bus', 'autre']);
            $table->date('year');
            $table->string('license_plate')->unique();
            $table->string('vin')->nullable()->unique();
            $table->string('color')->nullable();
            $table->enum('fuel_type', ['essence', 'diesel', 'hybride', 'electrique', 'gpl'])->nullable();
            $table->unsignedBigInteger('mileage')->default(0);

            // Informations techniques du véhicule
            $table->string('categorie')->nullable(); // Genre/Catégorie
            $table->string('numero_serie_type')->nullable(); // Numéro dans la série du type
            $table->string('carrosserie')->nullable(); // Type de carrosserie
            $table->string('numero_moteur')->nullable(); // Numéro moteur
            $table->integer('cylindree')->nullable(); // Cylindrée en cm3
            $table->integer('puissance_administrative')->nullable(); // Puissance administrative (CV)

            // Capacités et poids
            $table->integer('places_assises')->nullable(); // Nombre de places
            $table->decimal('poids_total_charge', 10, 2)->nullable(); // PTAC en kg
            $table->decimal('poids_vide', 10, 2)->nullable(); // Poids à vide en kg
            $table->decimal('charge_utile', 10, 2)->nullable(); // Charge utile en kg
            
            $table->enum('status', array_column(VehiculeStatus::cases(), 'value'))
                  ->default(VehiculeStatus::PENDING->value);
            $table->text('validation_notes')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users');
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicules');
    }
};
