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
        Schema::create('my_press_visitas', function (Blueprint $table) {
            $table->id();
            $table->date('data_visita');
            $table->string('cliente');
            $table->string('contato_cliente')->nullable();
            $table->string('contato_kluber')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_press_visitas');
    }
};
