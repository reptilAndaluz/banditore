<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ComentarioController extends Controller
{
    private const RULES = [
        'noticia_id' => 'required|exists:noticia,id',
        'mensaje' => 'required|string|min:2|max:1000',
        'valoracion' => 'required|in:pos,neg',
    ];

    private const MESSAGES = [
        'mensaje.required' => 'El comentario no puede estar vacío.',
        'mensaje.min' => 'El comentario debe contener al menos 2 caracteres.',
        'mensaje.max' => 'El comentario no puede superar los 1000 caracteres.',
        'valoracion.required' => 'Debes indicar una valoración positiva o negativa.',
        'valoracion.in' => 'La valoración debe ser positiva (pos) o negativa (neg).',
        'noticia_id.required' => 'El identificador de la noticia es obligatorio.',
        'noticia_id.exists' => 'La noticia especificada no existe.',
    ];

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), self::RULES, self::MESSAGES);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            Comentario::create([
                'noticia_id' => $request->input('noticia_id'),
                'mensaje' => $request->input('mensaje'),
                'valoracion' => $request->input('valoracion'),
            ]);

            return redirect()->route('noticia.show', $request->input('noticia_id'))
                ->with('success', '¡Comentario publicado correctamente!');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'No se pudo publicar el comentario: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string|Comentario $id): View|RedirectResponse
    {
        $comentario = $id instanceof Comentario ? $id : Comentario::with('noticia')->findOrFail($id);
        $comentario->loadMissing('noticia');

        if (! $comentario->sePuedeModificarOBorrar()) {
            return redirect()->route('noticia.show', $comentario->noticia_id)
                ->withErrors(['error' => 'No se puede editar el comentario: han pasado más de 5 minutos desde su publicación.']);
        }

        return view('comentario.edit', compact('comentario'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string|Comentario $id): RedirectResponse
    {
        $comentario = $id instanceof Comentario ? $id : Comentario::findOrFail($id);

        if (! $comentario->sePuedeModificarOBorrar()) {
            return redirect()->route('noticia.show', $comentario->noticia_id)
                ->withErrors(['error' => 'No se puede modificar el comentario: han pasado más de 5 minutos desde su publicación.']);
        }

        $rules = [
            'mensaje' => self::RULES['mensaje'],
            'valoracion' => self::RULES['valoracion'],
        ];

        $validator = Validator::make($request->all(), $rules, self::MESSAGES);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $comentario->update([
                'mensaje' => $request->input('mensaje'),
                'valoracion' => $request->input('valoracion'),
            ]);

            return redirect()->route('noticia.show', $comentario->noticia_id)
                ->with('success', '¡Comentario actualizado correctamente!');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'No se pudo actualizar el comentario: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|Comentario $id): RedirectResponse
    {
        $comentario = $id instanceof Comentario ? $id : Comentario::findOrFail($id);
        $noticiaId = $comentario->noticia_id;

        if (! $comentario->sePuedeModificarOBorrar()) {
            return redirect()->route('noticia.show', $noticiaId)
                ->withErrors(['error' => 'No se puede borrar el comentario: han pasado más de 5 minutos desde su publicación.']);
        }

        try {
            $comentario->delete();

            return redirect()->route('noticia.show', $noticiaId)
                ->with('success', 'Comentario eliminado correctamente.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'No se pudo eliminar el comentario: ' . $e->getMessage()]);
        }
    }
}
