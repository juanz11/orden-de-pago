<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('order_approvals', function (Blueprint $table) {
            if (!Schema::hasColumn('order_approvals', 'status')) {
                $table->string('status')->default('pendiente')->after('user_id');
            }
            if (!Schema::hasColumn('order_approvals', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('order_approvals', 'comments')) {
                $table->text('comments')->nullable()->after('approved_at');
            }
        });
    }

    public function down()
    {
        Schema::table('order_approvals', function (Blueprint $table) {
            $table->dropColumn(['status', 'approved_at', 'comments']);
        });
    }
};
