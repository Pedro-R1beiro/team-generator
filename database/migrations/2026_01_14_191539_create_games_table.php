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
        Schema::create('games', function (Blueprint $table) {
            $table->id();

            $table->foreignId('team_set_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('team_1', 1); // A, B, C, D
            $table->string('team_2', 1);

            $table->unsignedTinyInteger('score_1')->nullable();
            $table->unsignedTinyInteger('score_2')->nullable();

            $table->string('winner', 1)->nullable(); // A, B, C, D

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
