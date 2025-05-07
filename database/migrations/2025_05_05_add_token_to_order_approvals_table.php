<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('order_approvals', function (Blueprint $table) {
            $table->string('token', 64)->nullable()->unique()->after('comments');
        });
    }

    public function down()
    {
        Schema::table('order_approvals', function (Blueprint $table) {
            $table->dropColumn('token');
        });
    }
};
