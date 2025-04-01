<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero modificar la longitud de la columna
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status', 20)->change();
        });

        // Luego actualizar los estados existentes a español
        DB::statement("UPDATE orders SET status = 'pendiente' WHERE status = 'pending'");
        DB::statement("UPDATE orders SET status = 'aprobado' WHERE status = 'approved'");
        DB::statement("UPDATE orders SET status = 'rechazado' WHERE status = 'declined'");

        // Finalmente establecer el valor por defecto en español
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status', 20)->default('pendiente')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Primero revertir los estados a inglés
        DB::statement("UPDATE orders SET status = 'pending' WHERE status = 'pendiente'");
        DB::statement("UPDATE orders SET status = 'approved' WHERE status = 'aprobado'");
        DB::statement("UPDATE orders SET status = 'declined' WHERE status = 'rechazado'");

        // Luego restaurar el valor por defecto en inglés
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->change();
        });
    }
};
