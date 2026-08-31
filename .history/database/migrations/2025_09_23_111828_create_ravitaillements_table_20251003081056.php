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
            $table->date('ravitaillement_date');
            $table->string('station_name');
            $table->decimal('liters', 8, 2);
            $table->decimal('price_per_liter', 8, 3);
            $table->decimal('total_cost', 10, 2);
            $table->integer('odo_station')->nullable();
            $table->integer('odo_arrival')->nullable();
            $table->string('fuel_type')->default('essence');
            $table->string('payment_method')->default('cash');
            $table->string('receipt_number')->nullable();
            $table->boolean('full_tank')->default(false);
            $table->decimal('fuel_left', 8, 2)->nullable();
            $table->text('remarks')->nullable();
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
