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
        Schema::table('users', function (Blueprint $table) {
            $table->string('recovery_token')->nullable();
            $table->timestamp('recovery_token_expires_at')->nullable();
            $table->integer('recovery_attempts')->default(0);
            $table->timestamp('recovery_blocked_until')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'recovery_token',
                'recovery_token_expires_at',
                'recovery_attempts',
                'recovery_blocked_until'
            ]);
        });
    }
};
