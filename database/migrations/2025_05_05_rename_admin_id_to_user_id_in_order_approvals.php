<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Renombrar la columna directamente con SQL
        DB::statement('ALTER TABLE order_approvals CHANGE admin_id user_id BIGINT UNSIGNED NOT NULL');
        
        // Recrear índices y llaves foráneas
        Schema::table('order_approvals', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['order_id', 'user_id']);
        });
    }

    public function down()
    {
        // Eliminar índices y llaves foráneas
        Schema::table('order_approvals', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique(['order_id', 'user_id']);
        });

        // Renombrar la columna de vuelta
        DB::statement('ALTER TABLE order_approvals CHANGE user_id admin_id BIGINT UNSIGNED NOT NULL');
        
        // Recrear índices y llaves foráneas
        Schema::table('order_approvals', function (Blueprint $table) {
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['order_id', 'admin_id']);
        });
    }
};
