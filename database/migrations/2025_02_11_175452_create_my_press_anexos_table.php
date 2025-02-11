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
        Schema::create('my_press_anexos', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->nullable();
            $table->string('tipo')->nullable();
            $table->string('url')->nullable();
            $table->unsignedBigInteger('mypress_comentario_id');
            $table->foreign('mypress_comentario_id')->references('id')->on('my_press_comentarios')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_press_anexos');
    }
};
