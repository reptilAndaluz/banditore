<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Noticia extends Model
{
    //Esto es el modelo que corresponde a la migracion de Noticia

    protected $table = 'noticia';

    protected $fillable = ['titulo', 'texto'];

    public function comentarios(): HasMany
    {
        return $this->hasMany(Comentario::class, 'noticia_id');
    }

    // Regla: No se puede editar si lleva 20 minutos o más
    public function sePuedeEditar(): bool
    {
        return $this->created_at->diffInMinutes(now()) < 20;
    }

    // Regla: Cuando no tiene comentarios se puede borrar
    public function sePuedeBorrar(): bool
    {
        return $this->comentarios()->doesntExist();
    }

    public function minutosRestantesEdicion(): int
    {
        return max(0, 20 - $this->created_at->diffInMinutes(now()));
    }
}
