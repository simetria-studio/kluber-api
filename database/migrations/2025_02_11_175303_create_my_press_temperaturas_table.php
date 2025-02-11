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
        Schema::create('my_press_temperaturas', function (Blueprint $table) {
            $table->id();
            $table->string('zona1')->nullable();
            $table->string('zona2')->nullable();
            $table->string('zona3')->nullable();
            $table->string('zona4')->nullable();
            $table->string('zona5')->nullable();
            $table->unsignedBigInteger('mypress_elemento_id');
            $table->foreign('mypress_elemento_id')->references('id')->on('my_press_elementos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_press_temperaturas');
    }
};
