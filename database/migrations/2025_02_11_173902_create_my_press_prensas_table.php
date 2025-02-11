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
        Schema::create('my_press_prensas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_prensa')->nullable();
            $table->string('fabricante')->nullable();
            $table->string('comprimento')->nullable();
            $table->string('espressura')->nullable();
            $table->string('produto')->nullable();
            $table->string('velocidade')->nullable();
            $table->string('produto_cinta')->nullable();
            $table->string('produto_corrente')->nullable();
            $table->string('produto_bendroads')->nullable();
            $table->unsignedBigInteger('visita_id');
            $table->foreign('visita_id')
                ->references('id')
                ->on('my_press_visitas')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_press_prensas');
    }
};
