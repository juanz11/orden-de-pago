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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('description');
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });

        // Eliminar las columnas que ahora estarán en order_items
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['description', 'unit_price', 'quantity']);
            // Agregar total general a la orden
            $table->decimal('total', 10, 2)->after('other_supplier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');

        Schema::table('orders', function (Blueprint $table) {
            $table->string('description');
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity');
            $table->dropColumn('total');
        });
    }
};
