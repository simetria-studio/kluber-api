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
        Schema::table('usuario', function (Blueprint $table) {
            $table->text('access_token')->nullable();
            $table->dateTime('token_expires_in')->nullable();
            $table->string('password')->after('senha');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            $table->dropColumn('access_token');
            $table->dropColumn('token_expires_in');
            $table->dropColumn('password');
            $table->dropTimestamps();
        });
    }
};
