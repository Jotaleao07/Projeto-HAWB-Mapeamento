<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            // paciente que iniciou a conversa
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // médico receptivo
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            // uma conversa por par (paciente, médico)
            $table->unique(['user_id', 'doctor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
