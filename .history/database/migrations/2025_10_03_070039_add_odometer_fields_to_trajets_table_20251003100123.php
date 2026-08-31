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
        Schema::table('trajets', function (Blueprint $table) {
            //
             $table->decimal('odo_start', 10, 2)->nullable()->after('km_arrivee');
            $table->decimal('odo_end', 10, 2)->nullable()->after('odo_start');
            $table->enum('kilometrage_mode', ['odometer', 'trajet'])->default('odometer')->after('odo_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trajets', function (Blueprint $table) {
            //
        });
    }
};
