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
        Schema::create('my_press_problemas', function (Blueprint $table) {
            $table->id();
            $table->string('problema_redutor_principal')->nullable();
            $table->text('comentario_redutor_principal')->nullable();
            $table->string('problema_temperatura')->nullable();
            $table->text('comentario_temperatura')->nullable();
            $table->string('problema_tambor_principal')->nullable();
            $table->text('comentario_tambor_principal')->nullable();
            $table->unsignedBigInteger('mypress_visita_id');
            $table->foreign('mypress_visita_id')->references('id')->on('my_press_visitas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_press_problemas');
    }
};
