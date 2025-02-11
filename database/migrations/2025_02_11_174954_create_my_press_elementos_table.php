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
        Schema::create('my_press_elementos', function (Blueprint $table) {
            $table->id();
            $table->string('consumo_nominal')->nullable();
            $table->string('consumo_real')->nullable();
            $table->string('consumo_real_adicional')->nullable();
            $table->string('toma_consumo_real')->nullable();
            $table->string('posicao')->nullable();
            $table->string('tipo')->nullable();
            $table->unsignedBigInteger('mypress_prensa_id');
            $table->foreign('mypress_prensa_id')->references('id')->on('my_press_prensas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_press_elementos');
    }
};
