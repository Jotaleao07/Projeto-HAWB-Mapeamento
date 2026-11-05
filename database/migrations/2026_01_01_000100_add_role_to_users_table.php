<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // paciente | doctor | admin
            $table->string('role')->default('patient')->after('password');
            $table->string('specialty')->nullable()->after('role');
            $table->string('crm')->nullable()->after('specialty');
            $table->string('status')->default('approved')->after('crm');
            $table->text('bio')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'specialty', 'crm', 'status', 'bio']);
        });
    }
};
