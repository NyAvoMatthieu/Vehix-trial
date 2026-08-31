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
            $table->dateTime('ravitaillement_date');
            $table->string('station_name');
            $table->decimal('liters', 8, 2);
            $table->decimal('price_per_liter', 8, 3);
            $table->decimal('total_cost', 10, 2);
            $table->integer('odo_station')->unsigned();
            $table->integer('odo_arrival')->unsigned()->nullable();
            $table->enum('fuel_type', ['essence', 'diesel', 'gpl', 'electrique', 'hybride']);
            $table->enum('payment_method', ['especes', 'carte', 'mobile', 'bon']);
            $table->string('receipt_number', 100)->nullable();
            $table->boolean('is_full_tank')->default(true);
            $table->decimal('average_consumption', 5, 2)->nullable();
            $table->decimal('fuel_left', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
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
