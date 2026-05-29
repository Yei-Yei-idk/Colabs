<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imagenes', function (Blueprint $table) {
            $table->integer('img_id', true);
            $table->integer('espacio_id')->index('espacio_id');
            $table->binary('foto');

            $table->foreign('espacio_id', 'espacio_id')
                ->references('espacio_id')
                ->on('espacios')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imagenes');
    }
};
