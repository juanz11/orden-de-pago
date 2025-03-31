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
            $table->enum('department', [
                'RECURSOS HUMANOS',
                'PRESIDENCIA',
                'ADMINISTRACIÓN',
                'COMERCIAL',
                'MERCADEO',
                'CONSULTORÍA JURÍDICA',
                'LOGÍSTICA Y ALMACÉN',
                'SERVICIOS GENERALES',
                'MENTE Y SALUD',
                'TECNOLOGÍA DE LA INFORMACIÓN',
                'FINANZAS'
            ])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('department');
        });
    }
};
