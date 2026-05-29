<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reserva', function (Blueprint $table) {
            $table->integer('reserva_id', true);
            $table->time('rsva_hora_inicio');
            $table->time('rsva_hora_fin');
            $table->date('rsva_fecha');
            $table->string('rsva_estado', 30);
            $table->longText('rsva_descripcion');
            $table->integer('rsva_num_invitados')->nullable();
            $table->unsignedBigInteger('user_id')->index('user_id');
            $table->integer('espacio_id')->index('espacio_id');

            $table->foreign('user_id', 'reserva_ibfk_1')
                ->references('id')
                ->on('usuarios')
                ->onUpdate('restrict')
                ->onDelete('restrict');
            $table->foreign('espacio_id', 'reserva_ibfk_2')
                ->references('espacio_id')
                ->on('espacios')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reserva');
    }
};
