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
        Schema::create('comentario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('noticia_id');
            $table->text('mensaje');
            $table->enum('valoracion', ['pos', 'neg']);
            $table->timestamps();

            // Integridad referencial: no se puede borrar noticia si tiene comentarios
            $table->foreign('noticia_id')->references('id')->on('noticia')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comentario');
    }
};
