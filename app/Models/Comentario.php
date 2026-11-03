<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Comentario extends Model
{
    protected $table = 'comentario';

    protected $fillable = ['noticia_id', 'mensaje', 'valoracion'];

    public function noticia(): BelongsTo
    {
        return $this->belongsTo(Noticia::class, 'noticia_id');
    }

    // Regla: No se puede editar ni borrar si lleva 5 minutos o más
    public function sePuedeModificarOBorrar(): bool
    {
        return $this->created_at->diffInMinutes(now()) < 5;
    }

    public function minutosRestantes(): int
    {
        return max(0, 5 - $this->created_at->diffInMinutes(now()));
    }
}
