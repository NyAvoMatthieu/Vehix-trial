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
        Schema::create('ravitaillements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicule_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('chauffeur_name')->nullable();
            $table->date('ravitaillement_date');
            $table->string('station_service');
            $table->decimal('liters_purchased', 10, 2);
            $table->decimal('price_per_liter', 10, 2);
            $table->decimal('amount_paid', 10, 2);
            $table->decimal('total_liters', 10, 2);
            $table->decimal('total_cost', 10, 2);
            $table->decimal('odo_station', 10, 2)->nullable();
            $table->string('fuel_type');
            $table->enum('payment_method', ['carte', 'cash', 'virement', 'mobile'])->default('cash');
            $table->string('receipt_number')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_custom_price')->default(false);
            $table->timestamps();

            $table->index(['vehicule_id', 'ravitaillement_date']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ravitaillements');
    }
};
