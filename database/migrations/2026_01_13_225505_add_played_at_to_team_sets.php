<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('team_sets', function (Blueprint $table) {
            $table->date('played_at')->nullable()->after('name');
        });
    }

    public function down()
    {
        Schema::table('team_sets', function (Blueprint $table) {
            $table->dropColumn('played_at');
        });
    }
};
