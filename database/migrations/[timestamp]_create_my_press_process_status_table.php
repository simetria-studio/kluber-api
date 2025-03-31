<?php

namespace Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyPressProcessStatusTable extends Migration
{
    public function up()
    {
        Schema::create('my_press_process_status', function (Blueprint $table) {
            $table->id();
            $table->string('job_id')->unique();
            $table->unsignedBigInteger('visita_id')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed']);
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->foreign('visita_id')
                  ->references('id')
                  ->on('my_press_visitas')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('my_press_process_status');
    }
} 