<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tarefa 3: última troca de nome (bloqueia nova troca por 24h).
            $table->timestamp('name_changed_at')->nullable()->after('preferences');

            // Tarefa 5: bloqueio de chat com médicos + banimento com motivo.
            $table->timestamp('chat_blocked_at')->nullable()->after('name_changed_at');
            $table->timestamp('banned_at')->nullable()->after('chat_blocked_at');
            $table->text('ban_reason')->nullable()->after('banned_at');

            // Tarefa 6: foto de perfil (base64 comprimido — sobrevive a deploys).
            $table->text('profile_photo')->nullable()->after('ban_reason');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'name_changed_at', 'chat_blocked_at', 'banned_at', 'ban_reason', 'profile_photo',
            ]);
        });
    }
};
