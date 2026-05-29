<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->integer('calif_id', true);
            $table->text('calif_txt');
            $table->integer('calif_puntuacion');
            $table->unsignedBigInteger('user_id')->index('user_id');
            $table->integer('espacio_id')->index('espacio_id');
            $table->integer('reserva_id')->nullable();

            $table->foreign('user_id', 'calificaciones_ibfk_1')
                ->references('id')
                ->on('usuarios')
                ->onUpdate('restrict')
                ->onDelete('restrict');
            $table->foreign('espacio_id', 'calificaciones_ibfk_2')
                ->references('espacio_id')
                ->on('espacios')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calificaciones');
    }
};
