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
        Schema::table('my_press_temperaturas', function (Blueprint $table) {
            // Remove a foreign key existente
            $table->dropForeign(['mypress_elemento_id']);

            // Remove a coluna existente
            $table->dropColumn('mypress_elemento_id');

            // Adiciona a nova coluna
            $table->unsignedBigInteger('mypress_prensa_id');

            // Adiciona a nova foreign key
            $table->foreign('mypress_prensa_id')->references('id')->on('my_press_prensas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('my_press_temperaturas', function (Blueprint $table) {
            // Remove a foreign key da prensa
            $table->dropForeign(['mypress_prensa_id']);

            // Remove a coluna da prensa
            $table->dropColumn('mypress_prensa_id');

            // Adiciona a coluna do elemento
            $table->unsignedBigInteger('mypress_elemento_id');

            // Adiciona a foreign key do elemento
            $table->foreign('mypress_elemento_id')->references('id')->on('my_press_elementos')->onDelete('cascade');
        });
    }
};
