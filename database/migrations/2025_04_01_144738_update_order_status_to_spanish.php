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
        // Actualizar los estados existentes a español
        DB::table('orders')->where('status', 'pending')->update(['status' => 'pendiente']);
        DB::table('orders')->where('status', 'approved')->update(['status' => 'aprobado']);
        DB::table('orders')->where('status', 'declined')->update(['status' => 'rechazado']);

        // Modificar la columna para que use el valor por defecto en español
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->default('pendiente')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir los estados a inglés
        DB::table('orders')->where('status', 'pendiente')->update(['status' => 'pending']);
        DB::table('orders')->where('status', 'aprobado')->update(['status' => 'approved']);
        DB::table('orders')->where('status', 'rechazado')->update(['status' => 'declined']);

        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }
};
