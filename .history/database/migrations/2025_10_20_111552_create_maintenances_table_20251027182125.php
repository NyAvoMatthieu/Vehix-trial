<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('vehicule_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('technicien_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('type'); // preventive, corrective, diagnostique
            $table->string('nature_intervention');
            $table->decimal('kilometrage_actuel', 10, 2);
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->text('observation_generale')->nullable();
             $table->date('maintenance_date');
            $table->string('status')->default('en_attente'); // en_attente, en_cours, validee
            
            // Validation et signature
            $table->foreignId('validateur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('validated_at')->nullable();
            $table->text('notes_validation')->nullable();
            $table->string('signature_technicien')->nullable();
            $table->string('signature_superviseur')->nullable();
            $table->string('signature_client')->nullable();
            
            // Coûts
            $table->decimal('cout_main_oeuvre', 10, 2)->default(0);
            $table->decimal('cout_pieces', 10, 2)->default(0);
            $table->decimal('cout_total', 10, 2)->default(0);
            
            $table->timestamps();
            
            $table->index(['vehicule_id', 'date_debut']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance');
    }
};
