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
        Schema::table('order_payments', function (Blueprint $table) {
            $table->string('payment_type')->after('amount');
            $table->string('bank_name')->nullable()->after('payment_type');
            $table->decimal('cash_amount', 12, 2)->nullable()->after('bank_name');
            $table->string('accounting_entry')->after('reference_number');
            // Hacer reference_number nullable ya que solo es requerido para pagos bancarios
            $table->string('reference_number')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_payments', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'bank_name', 'cash_amount', 'accounting_entry']);
            $table->string('reference_number')->nullable(false)->change();
        });
    }
};
