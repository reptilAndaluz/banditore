<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\Noticia;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class NoticiaController extends Controller
{
    private const RULES = [
        'titulo' => 'required|string|min:3|max:200',
        'texto' => 'nullable|string',
        'archivo_md' => 'nullable|file|mimes:txt,md|max:2048',
    ];

    private const MESSAGES = [
        'titulo.required' => 'El título es obligatorio.',
        'titulo.min' => 'El título debe tener al menos 3 caracteres.',
        'titulo.max' => 'El título no puede superar los 200 caracteres.',
        'archivo_md.mimes' => 'El archivo adjunto debe ser de tipo .md o .txt.',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $noticias = Noticia::withCount('comentarios')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $totalComentarios = Comentario::count();

        return view('noticia.index', compact('noticias', 'totalComentarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('noticia.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), self::RULES, self::MESSAGES);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $texto = $request->input('texto');

        // Funcionalidad "Subir nota .md": si se sube archivo, se lee su contenido
        if ($request->hasFile('archivo_md')) {
            $contenidoArchivo = file_get_contents($request->file('archivo_md')->getRealPath());
            $texto = $contenidoArchivo;
        }

        // Si no se proporcionó ni texto manual ni archivo
        if (empty(trim($texto ?? ''))) {
            return back()->withErrors(['texto' => 'Debes introducir el texto de la noticia o subir un archivo .md/.txt.'])->withInput();
        }

        try {
            $noticia = Noticia::create([
                'titulo' => $request->input('titulo'),
                'texto' => $texto,
            ]);

            return redirect()->route('noticia.show', $noticia)->with('success', '¡Noticia publicada con éxito!');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'No se pudo guardar la noticia: '.$e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string|Noticia $id): View
    {
        $noticia = $id instanceof Noticia ? $id : Noticia::findOrFail($id);
        $noticia->load('comentarios');

        $otrasNoticias = Noticia::where('id', '!=', $noticia->id)
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        return view('noticia.show', compact('noticia', 'otrasNoticias'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string|Noticia $id): View|RedirectResponse
    {
        $noticia = $id instanceof Noticia ? $id : Noticia::findOrFail($id);

        if (! $noticia->sePuedeEditar()) {
            return redirect()->route('noticia.show', $noticia)
                ->withErrors(['error' => 'No se puede editar la noticia: han pasado más de 20 minutos desde su publicación.']);
        }

        return view('noticia.edit', compact('noticia'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string|Noticia $id): RedirectResponse
    {
        $noticia = $id instanceof Noticia ? $id : Noticia::findOrFail($id);

        if (! $noticia->sePuedeEditar()) {
            return redirect()->route('noticia.show', $noticia)
                ->withErrors(['error' => 'No se puede editar la noticia: han pasado más de 20 minutos desde su publicación.']);
        }

        $validator = Validator::make($request->all(), self::RULES, self::MESSAGES);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $texto = $request->input('texto');

        // Si se subió un nuevo archivo .md o .txt, su contenido tiene preferencia
        if ($request->hasFile('archivo_md')) {
            $contenidoArchivo = file_get_contents($request->file('archivo_md')->getRealPath());
            $texto = $contenidoArchivo;
        }

        if (empty(trim($texto ?? ''))) {
            return back()->withErrors(['texto' => 'Debes introducir el texto de la noticia o subir un archivo .md/.txt.'])->withInput();
        }

        try {
            $noticia->update([
                'titulo' => $request->input('titulo'),
                'texto' => $texto,
            ]);

            return redirect()->route('noticia.show', $noticia)->with('success', '¡Noticia actualizada con éxito!');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'No se pudo actualizar la noticia: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|Noticia $id): RedirectResponse
    {
        $noticia = $id instanceof Noticia ? $id : Noticia::findOrFail($id);

        if (! $noticia->sePuedeBorrar()) {
            return back()->withErrors(['error' => 'No se puede borrar la noticia porque tiene comentarios asociados.']);
        }

        try {
            $noticia->delete();

            return redirect()->route('noticia.index')->with('success', 'Noticia eliminada correctamente.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'No se pudo eliminar la noticia: ' . $e->getMessage()]);
        }
    }
}
